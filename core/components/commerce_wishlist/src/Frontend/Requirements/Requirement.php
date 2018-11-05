<?php
namespace PoconoSewVac\Wishlist\Frontend\Requirements;

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
abstract class Requirement
{
    abstract public function isMet(Request $request);
}