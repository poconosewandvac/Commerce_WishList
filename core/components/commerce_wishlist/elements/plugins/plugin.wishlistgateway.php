<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

// For testing
$resourceId = 2349;
$requestURI = trim($_SERVER['REQUEST_URI'], '/');

$uri = $modx->getObject('modResource', [
    'uri' =>  $requestURI
]);

// Check URI path
if (substr($requestURI, 0, strlen($requestURI)) === $requestURI) {
    $requestParts = explode('/', $requestURI);
    $l = count($requestParts);
    
    // Handle adding requests
    if ($requestParts[$l - 1] === "add") {
        $modx->sendForward($resourceId);
    }
    
    // Handle delete requests
    if ($requestParts[$l - 2] === "delete") {
        $modx->sendForward($resourceId);   
    }
    
    if ($requestParts[$l - 2] === "view") {
        $_REQUEST['secret'] = $requestParts[$l - 1];
        $modx->sendForward($resourceId);  
    }
}