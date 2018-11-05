<?php
namespace PoconoSewVac\Wishlist\Frontend;

use PoconoSewVac\Wishlist\Frontend\Actions;
use PoconoSewVac\Wishlist\Frontend\Requirements;

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
class Request
{
    protected $type;
    protected $method;
    protected $responseType;
    protected $request;

    public function __construct(array $request)
    {
        $allowedTypes = [
            Actions\AddList::getClassName(),
            Actions\AddItem::getClassName(),
            Actions\ReadList::getClassName(),
            Actions\ReadItem::getClassName(),
            Actions\EditList::getClassName(),
            Actions\EditItem::getClassName(),
            Actions\DeleteList::getClassName(),
            Actions\DeleteItem::getClassName()
        ];

        if (in_array($type, $allowedTypes)) {
            $this->type = $type;
        }

        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getField(string $field)
    {
        return $this->request[$field];
    }
}