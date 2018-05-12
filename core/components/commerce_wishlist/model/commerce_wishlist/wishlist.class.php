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

        $basePath = $this->modx->getOption('commerce_wishlist.core_path', $config, $this->modx->getOption('core_path').'components/commerce_wishlist/');
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
            //$this->modx->regClientScript($this->config['jsUrl'] . 'wishlist.js');
        }
    }
}
