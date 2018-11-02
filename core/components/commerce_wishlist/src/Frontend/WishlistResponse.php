<?php
namespace PoconoSewVac\Wishlist\Frontend;

use modmore\Commerce\Frontend\Response;

class WishlistResponse extends Response {
    public function __construct(\Commerce $commerce)
    {
        $this->commerce = $commerce;
        $this->adapter = $commerce->adapter;
    }
}