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
class AddList extends Action
{
    public function hasPermission()
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        return false;
    }

    public function execute(Response $response)
    {

    }
}