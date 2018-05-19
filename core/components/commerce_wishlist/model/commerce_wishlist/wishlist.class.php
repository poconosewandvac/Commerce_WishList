<?php
/**
 * Wishlist for Commerce.
 *
 * Copyright 2018 by Tony Klapatch <tony@klapatch.net>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_wishlist
 * @license See core/components/commerce_wishlist/docs/license.txt
 */
class Wishlist {
    public $modx;
    public $user;
    public $commerce;
    public $config = [];

    /**
     * Initialize modX, Commerce, and user
     *
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX &$modx, array $config = array()) {
        // Initialize Wishlist
        $this->modx =& $modx;
        $this->user = $config['user'];

        $corePath = $this->modx->getOption('commerce_wishlist.core_path', $config, $this->modx->getOption('core_path').'components/commerce_wishlist/');
        $assetsUrl = $this->modx->getOption('commerce_wishlist.assets_url', $config, $this->modx->getOption('assets_url').'components/commerce_wishlist/');
        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'controllersPath' => $corePath.'controllers/',
            'chunksPath' => $corePath.'elements/chunks/',
            'snippetsPath' => $corePath.'elements/snippets/',
            'baseUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'connectorUrl' => $assetsUrl.'connector.php'
        ]);
        
        // Add packages
        $this->modx->addPackage('commerce_wishlist', $this->config['modelPath']);

        // Load Commerce
        $commercePath = $this->modx->getOption('commerce.core_path', null, $this->modx->getOption('core_path') . 'components/commerce/') . 'model/commerce/';
        $this->commerce = $this->modx->getService('commerce', 'Commerce', $commercePath, ['mode' => $this->modx->getOption('commerce.mode')]);
    }

    /**
     * Gets the user id
     * 
     * @return int
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Register default css/js
     *
     * @param bool $css
     * @param bool $js
     * @return void
     */
    public function registerAssets($css, $js) {
        if ($css) {
            //$this->modx->regClientCSS($this->config['cssUrl'] . 'wishlist.css');
        }
        if ($js) {
            $this->modx->regClientScript($this->config['jsUrl'] . 'wishlist.js');
        }
    }

    /**
     * Add list from submission
     *
     * @param array $values
     * @return int|bool list id
     */
    public function addList($values) {
        $values['secret'] = $this->generateSecret();
        $values['user'] = $this->getUser();

        $query = $this->modx->newObject("WishlistList");
        $query->fromArray($values);
        $query->save();

        if (!$query) {
            return false;
        }

        return $query->get('id');
    }

    /**
     * Add item from submission
     *
     * @param array $values
     * @return int|bool item id
     */
    public function addItem($values) {
        $query = $this->modx->newObject("WishlistItem");
        $query->fromArray($values);
        $query->save();

        if (!$query) {
            return false;
        }

        return $query->get('id');
    }

    /** 
     * Get all user lists
     * 
     * @param array $where Override what lists it is looking for
     * @return collection
     */
    public function getLists($where = null) {
        $query = $this->modx->newQuery("WishlistList");

        if ($where) {
            $query->fromArray($where);
        } else {
            $query->where([
                'user' => $this->getUser()
            ]);
        }
        
        return $this->modx->getCollection('WishlistList', $query);
    }

    /** 
     * Get the default user list (based on pos)
     * 
     * @return list id
     */
    public function getDefaultList() {
        $default = $this->modx->getObject("WishlistList", [
            'user' => $this->getUser(),
            'pos' => 0
        ]);

        return $default ? $default->get('id') : false;
    }

    /**
     * Fetches the name of the list
     *
     * @param string list
     * @param bool secret use secret
     * @return list object
     */
    public function getList($list, $secret = false) {
        $query = $this->modx->newQuery("WishlistList");

        if ($secret) {
            $query->where([
                'secret' => $list
            ]);
        } else {
            $query->where([
                'id' => $list
            ]);
        }

        return $this->modx->getObject('WishlistList', $query);
    }

    /** 
     * "Removes" a list (just sets "removed" column to 1) 
     * 
     * @param string list
     * @param bool secret use secret
     * @return void
     */
    public function deleteList($list, $secret = false) {
        $query = $this->getList($list, $secret);

        if ($query) {
            $query->set('removed', 0);
            $query->save();
        }
    }

    /** 
     * Checks if user can read the list.
     * 
     * @param string list
     * @param bool secret use secret
     * @return bool
     */
    public function hasReadPermission($list, $secret = false) {
        $check = $this->getList($list, $secret);

        // If list doesn't exist
        if (!$check) {
            return false;
        }

        if (((int)$check->get('user') === $this->user) || (int)$check->get('share') === 1) {
            return true;
        }

        return false;
    }

    /**
     * If user has permission to edit list
     *
     * @param [type] $list
     * @param boolean $secret
     * @return boolean
     */
    public function hasEditPermission($list, $secret = false) {
        $query = $this->modx->newQuery("WishlistList");

        if ($secret) {
            $query->where([
                'secret' => $list,
                'user' => $this->getUser()
            ]);
        } else {
            $query->where([
                'id' => $list,
                'user' => $this->getUser()
            ]);
        }

        return $this->modx->getObject("WishlistList", $query) ? true : false;
    }

    /** 
     * Get all user lists
     * 
     * @param string $join Package to join on.
     * @param array $where Override what lists it is looking for
     * @return collection
     */
    public function getFormattedItems($list, $secret = false, $where = null, $join = 'comProduct') {
        $query = $this->modx->newQuery("WishlistItem");
        $query->select($this->modx->getSelectColumns('WishlistItem', 'WishlistItem'));

        switch ($join) {
            case "comProduct":
                $query->select($this->modx->getSelectColumns('comProduct', 'comProduct'));
                $query->innerJoin('comProduct', 'comProduct', ["WishlistItem.product = comProduct.id"]);

                break;

            case "modResource":
                $query->select($this->modx->getSelectColumns('modResource', 'modResource'));
                $query->innerJoin('modResource', 'modResource', ["WishlistItem.target = modResource.id"]);

                break;
        }

        if ($where) {
            $query->fromArray($where);
        } else if ($secret) {
            $query->select('WishlistList.secret');
            $query->innerJoin('WishlistList', 'WishlistList', ["WishlistItem.list = WishlistList.id"]);
            $query->where([
                'WishlistList.secret' => $list
            ]);
        } else {
            $query->where([
                'WishlistItem.list' => $list
            ]);
        }
        
        return $this->modx->getCollection('WishlistItem', $query);
    }

    /**
     * Gets a single item by id
     * 
     * @param int item id
     */
    public function getItem($item) {
        $query = $this->modx->newQuery("WishlistItem");
        $query->where([
            'id' => $item
        ]);
        
        return $this->modx->getObject('WishlistItem', $query);
    }

    /**
     * Gets all items in a list
     * 
     * @param int list id
     * @return items
     */
    public function getItems($list) {
        $query = $this->modx->newQuery("WishlistItem");
        $query->where([
            'list' => $list
        ]);
        $query->sortby('pos', 'ASC');
        
        return $this->modx->getCollection('WishlistItem', $query);
    }

    /** 
     * "Removes" an item (just sets "removed" column to 1) 
     * 
     * @param string item
     * @return void
     */
    public function deleteItem($item) {
        $query = $this->getItem($item);

        if ($query) {
            $query->set('removed', 0);
            $query->save();
        }
    }

    /**
     * Generates secret field to use in URL
     *
     * @param int bytes of secret field
     * @param bool check if it is a duplicate
     * @return void
     */
    public function generateSecret($bytes = 5, $check = true) {
        $secret = bin2hex(openssl_random_pseudo_bytes($bytes));

        // Check to ensure random generated string has not been used before
        if ($check) {
            $query = $this->modx->getObject('WishlistList', ['secret' => $secret]);
            
            if ($query) {
                // Generate a new one if it is being used.
                $secret = $this->generateSecret($bytes, $check);
            }
        }

        return $secret;
    }
}
