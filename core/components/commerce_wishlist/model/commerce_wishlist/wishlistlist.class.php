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
class WishlistList extends comSimpleObject
{
    /**
     * Gets the user the list belongs to
     *
     * @return int
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * Gets the list items
     *
     * @param array $where
     * @return array
     */
    public function getItems($where = null)
    {
        $c = $this->adapter->newQuery('WishlistItem');

        if (!$where) {
            $where = [
                'list' => $this->get('id'),
                'removed' => 0,
            ];
        }

        $c->where($where);
        return $this->adapter->getIterator('WishlistItem', $c);
    }

    /**
     * Checks if the user that requested the list has permission to view it
     *
     * @return boolean
     */
    public function hasReadPermission()
    {
        if ($this->get('share')) {
            return true;
        }

        if ($this->getUser() == $this->adapter->getUser()) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the user that requested the list has permission to edit it
     * 
     * @return boolean
     */
    public function hasEditPermission()
    {
        if ($this->getUser() == $this->adapter->getUser()) {
            return true;
        }

        return false;
    }

    /**
     * Generates secret field to use for identification
     *
     * @param int bytes of secret field
     * @param bool check if it is a duplicate
     * @return void
     */
    public function generateSecret($bytes = 5, $check = true)
    {
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
