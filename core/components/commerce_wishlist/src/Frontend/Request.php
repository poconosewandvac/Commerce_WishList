<?php
namespace PoconoSewVac\Wishlist\Frontend;

use modmore\Commerce\Events\Admin\Actions;

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
    /**
     * @var modmore\Commerce
     */
    public $commerce;
    /**
     * @var string
     */
    protected $requestMethod;
    /**
     * @var string|null
     */
    protected $actionType;
    /**
     * @var Actions\Action
     */
    protected $action;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var bool
     */
    protected $valid = false;

    // Action types
    const ADD_LIST = 'add-list';
    const ADD_ITEM = 'add-item';
    const READ_LIST = 'read-list';
    const READ_ITEM = 'read-item';
    const EDIT_LIST = 'edit-list';
    const EDIT_ITEM = 'edit-item';
    const REMOVE_LIST = 'remove-list';
    const REMOVE_ITEM = 'remove-item';

    /**
     * Request constructor.
     * @param $requestMethod string Request method, ex GET / POST
     * @param $actionType string Wishlist action type
     * @param $commerce Commerce instance
     * @param array $data Data from client (body)
     */
    public function __construct($requestMethod, $actionType, $commerce, array $data = [])
    {
        $this->requestMethod = $requestMethod;
        $this->actionType = $actionType;
        $this->data = $data;
        $this->commerce = $commerce;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getDataField($field)
    {
        return $this->data[$field];
    }

    /**
     * Returns an instance of an action depending on the requested action.
     * Returns null if no appropriate action found
     *
     * @return Actions\Action|null
     */
    public function getAction()
    {
        if ($this->action) {
            return $this->action;
        }

        $action = null;

        // Route GET and POST requests
        if ($this->requestMethod === 'GET') {
            switch ($this->actionType) {
                case self::READ_ITEM:
                    $action = new Actions\ReadItem($this);
                    break;
                case self::READ_LIST:
                    $action = new Actions\ReadList($this);
                    break;
                default:
                    $action = new Actions\ReadList($this);
                    break;
            }
        } else if ($this->requestMethod === 'POST') {
            switch ($this->actionType) {
                case self::ADD_LIST:
                    $action = new Actions\AddList($this);
                    break;
                case self::ADD_ITEM:
                    $action = new Actions\AddItem($this);
                    break;
                case self::EDIT_LIST:
                    $action = new Actions\EditList($this);
                    break;
                case self::REMOVE_LIST:
                    $action = new Actions\RemoveList($this);
                    break;
                case self::REMOVE_ITEM:
                    $action = new Actions\RemoveItem($this);
                    break;
            }
        }

        $this->action = $action;
        return $action;
    }
}