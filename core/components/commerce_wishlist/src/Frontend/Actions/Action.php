<?php
namespace PoconoSewVac\Wishlist\Frontend\Actions;

use PoconoSewVac\Wishlist\Frontend\Response;

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
abstract class Action
{
    /**
     * @var \modUser|null
     */
    protected $user;
    /**
     * @var \Commerce
     */
    protected $commerce;
    /**
     * @var modmore\Commerce\Adapter\AdapterInterface
     */
    protected $adapter;
    /**
     * @var array
     */
    protected $options;

    /**
     * Action constructor.
     *
     * @param $params
     * @param \Commerce $commerce
     */
    public function __construct(array $options, \Commerce $commerce)
    {
        $this->options = $options;
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
        $this->user = $commerce->adapter->getUser();
    }

    /**
     * Get the class name to use as an identifier
     *
     * @return string
     */
    public static function getClassName()
    {
        return get_called_class();
    }

    /**
     * Gets an option by key
     *
     * @param string $option
     * @return mixed
     */
    public function getOption(string $option)
    {
        return $this->options[$option];
    }

    /**
     * Determines if the user is logged ins
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool) $this->user;
    }

    /**
     * Per action implementation of permission checking
     *
     * @return bool
     */
    public abstract function hasPermission();

    /**
     * Execute the given action
     *
     * @param Response response object for passing back data to the client
     * @return mixed
     */
    public abstract function execute(Response $response);
}