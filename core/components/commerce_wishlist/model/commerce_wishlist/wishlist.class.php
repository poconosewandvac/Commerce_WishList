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
class Wishlist
{
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
    public function __construct(modX &$modx, array $config = array())
    {
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Register default css/js
     *
     * @param bool $css
     * @param bool $js
     * @return void
     */
    public function registerAssets($css, $js)
    {
        if ($css) {
            $this->modx->regClientCSS($this->config['cssUrl'] . 'wishlist.css');
        }
        if ($js) {
            $this->modx->regClientScript($this->config['jsUrl'] . 'wishlist.js');
        }
    }

    /**
     * Fetches a list object based on id or secret
     *
     * @param string list
     * @return list object
     */
    public function getList($list)
    {
        return $this->modx->getObject("WishlistList", ['secret' => $list]);
    }

    /** 
     * Get all user lists
     * 
     * @return collection
     */
    public function getLists()
    {
        $query = $this->modx->newQuery("WishlistList");

        $query->where([
            'user' => $this->getUser(),
            'removed' => 0
        ]);
        
        return $this->modx->getCollection('WishlistList', $query);
    }


    /** 
     * Get the default user list (based on pos)
     * 
     * @return list|bool id|success
     */
    public function getDefaultList()
    {
        $default = $this->modx->getObject("WishlistList", [
            'user' => $this->getUser(),
            'pos' => 0,
            'removed' => 0
        ]);

        return $default ? $default->get('id') : false;
    }

    /** 
     * Get all user lists
     * 
     * @param string $join Package to join on.
     * @param array $where Override what lists it is looking for
     * @return collection
     */
    public function getFormattedItems($list, $secret = false, $where = null)
    {
        $query = $this->modx->newQuery("WishlistItem");
        $query->select($this->modx->getSelectColumns('WishlistItem', 'WishlistItem'));

        // Join comProduct
        $query->select($this->modx->getSelectColumns('comProduct', 'comProduct', 'product_'));
        $query->innerJoin('comProduct', 'comProduct', ["WishlistItem.product = comProduct.id"]);

        if ($where) {
            $query->fromArray($where);
        } else if ($secret) {
            $query->select('WishlistList.secret');
            $query->innerJoin('WishlistList', 'WishlistList', ["WishlistItem.list = WishlistList.id"]);
            $query->where([
                'WishlistList.secret' => $list,
                'WishlistItem.removed' => 0,
                'comProduct.removed' => 0
            ]);
        } else {
            $query->where([
                'WishlistItem.list' => $list,
                'WishlistItem.removed' => 0,
                'comProduct.removed' => 0
            ]);
        }
        
        return $this->modx->getCollection('WishlistItem', $query);
    }
}
