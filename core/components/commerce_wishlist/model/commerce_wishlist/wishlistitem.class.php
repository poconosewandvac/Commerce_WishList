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
class WishlistItem extends comSimpleObject
{
    protected $_list;
    protected $_product;

    /**
     * Fetches the list associated with the item
     *
     * @return WishlistList
     */
    public function getList()
    {
        if (!$this->_list) {
            $this->_list = $this->adapter->getObject('WishlistList', $this->get('list'));
        }

        return $this->_list;
    }

    /**
     * Fetches the Commerce product associated with the item
     *
     * @return comProduct
     */
    public function getProduct()
    {
        if ($this->_product) {
            $this->_product = $this->adapter->getObject('comProduct', $this->get('product'));
        }

        return $this->_product;
    }
}
