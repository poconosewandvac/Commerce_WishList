<?php
namespace PoconoSewVac\Wishlist\Frontend\Actions;

use PoconoSewVac\Wishlist\Frontend\Response;
use PoconoSewVac\Wishlist\Frontend\Request;

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
     * @var Request
     */
    protected $request;

    /**
     * Action constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->commerce = $this->request->commerce;
        $this->adapter = $this->commerce->adapter;
        $this->user = $this->commerce->adapter->getUser();
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
     * Default has permission method
     *
     * @return bool
     */
    public function hasPermission()
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * Execute the given action
     *
     * @param Response response object for passing back data to the client
     * @return mixed
     */
    public abstract function execute();

    /**
     * Set the response back to the client
     *
     * @param Response $response
     * @return mixed
     */
    public abstract function output(Response $response);
}