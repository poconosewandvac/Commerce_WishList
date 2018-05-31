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

class WishlistGateway {
    private $modx;
    private $scriptProperties;

    public function __construct(modX &$modx) {
        $this->modx =& $modx;
    }
}