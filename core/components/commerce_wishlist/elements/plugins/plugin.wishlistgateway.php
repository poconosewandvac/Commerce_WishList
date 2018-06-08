<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

// URI paths
$viewPath = $modx->getOption("commerce_wishlist.view_uri", null, "view");
$editPath = $modx->getOption("commerce_wishlist.edit_uri", null, "edit");
$deletePath = $modx->getOption("commerce_wishlist.delete_uri", null, "delete");
$addPath = $modx->getOption("commerce_wishlist.add_uri", null, "add");

$resourceId = $modx->getOption('commerce_wishlist.resource');
if (!$resourceId) {
    $modx->log(MODX_LOG_LEVEL_ERROR, "[commerce_wishlist] commerce_wishlist.resource not set.");
    return;
}

$requestURI = trim($_SERVER['REQUEST_URI'], '/');

$uri = $modx->getObject('modResource', [
    'uri' =>  $requestURI
]);

// Check URI path
if (substr($requestURI, 0, strlen($requestURI)) === $requestURI) {
    $requestParts = explode('/', $requestURI);
    $l = count($requestParts);
    
    // Handle adding requests
    if ($requestParts[$l - 2] === $addPath) {
        if ($requestParts[$l - 1] === $modx->getOption('commerce_wishlist.item_uri', null, "item")) {
            $_REQUEST["type"] = "add_item";
        } else if ($requestParts[$l - 1] === $modx->getOption('commerce_wishlist.list_uri', null, "list")) {
            $_REQUEST["type"] = "add_list";
        }

        $modx->sendForward($resourceId);
    }
    
    // Handle delete requests
    if ($requestParts[$l - 2] === $deletePath) {
        if ($requestParts[$l - 1] === $modx->getOption('commerce_wishlist.item_uri', null, "item")) {
            $_REQUEST["type"] = "delete_item";
        } else if ($requestParts[$l - 1] === $modx->getOption('commerce_wishlist.list_uri', null, "list")) {
            $_REQUEST["type"] = "delete_list";
        }

        $modx->sendForward($resourceId);   
    }
    
    // Handle editing requests
    if ($requestParts[$l - 2] === $editPath) {
        if ($requestParts[$l - 1] === $modx->getOption('commerce_wishlist.item_uri', null, "item")) {
            $_REQUEST["type"] = "edit_item";
        } else if ($requestParts[$l - 1] === $modx->getOption('commerce_wishlist.list_uri', null, "list")) {
            $_REQUEST["type"] = "edit_list";
        }
        
        $modx->sendForward($resourceId);   
    }
    
    // Handle view requests
    if ($requestParts[$l - 2] === $viewPath) {
        $_REQUEST['secret'] = $requestParts[$l - 1];
        
        $modx->sendForward($resourceId);  
    }
}