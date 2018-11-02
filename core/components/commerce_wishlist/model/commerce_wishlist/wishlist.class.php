<?php
$loaderPath = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
if (!file_exists($loaderPath)) {
    throw new Exception('Could not load autoloader, file does not exist at ' . $loaderPath
        . '. Are dependencies properly installed?');
}
// Setting the loader to a global allows us to store a reference to it in the service class.
global $loader;
$loader = require $loaderPath;

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
    /**
     * @var modX
     */
    public $modx;

    /**
     * The Composer Autoloader instance.
     *
     * @var Composer\Autoload\ClassLoader $adapter
     */
    public $loader;

    /**
     * @var int
     */
    public $user;

    /**
     * @var Commerce
     */
    public $commerce;

    /**
     * @var array
     */
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

        global $loader;
        $this->loader =& $loader;
        $this->loader->add('PoconoSewVac\\Wishlist\\', __DIR__);

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
     * @return xPDOObject list object
     */
    public function getList($list)
    {
        return $this->modx->getObject("WishlistList", ['secret' => $list]);
    }

    /**
     * Get the default user list (based on pos)
     *
     * @return xPDOObject|bool
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
     * @return array
     */
    public function getLists()
    {
        return $this->modx->getCollection('WishlistList', [
            'user' => $this->getUser(),
            'removed' => 0
        ]);
    }

    /** 
     * Get all user lists
     * 
     * @param string $list
     * @return array
     */
    public function getFormattedItems($list)
    {
        $query = $this->modx->newQuery("WishlistItem");
        $query->select($this->modx->getSelectColumns('WishlistItem', 'WishlistItem'));

        // Join comProduct
        $query->select($this->modx->getSelectColumns('comProduct', 'comProduct', 'product_'));
        $query->innerJoin('comProduct', 'comProduct', ["WishlistItem.product = comProduct.id"]);

        $query->select('WishlistList.secret');
        $query->innerJoin('WishlistList', 'WishlistList', ["WishlistItem.list = WishlistList.id"]);
        $query->where([
            'WishlistList.secret' => $list,
            'WishlistItem.removed' => 0,
            'comProduct.removed' => 0
        ]);
        
        return $this->modx->getCollection('WishlistItem', $query);
    }
}
