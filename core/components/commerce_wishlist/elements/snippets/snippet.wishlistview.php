<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

use PoconoSewVac\Wishlist\Frontend\Request;
use PoconoSewVac\Wishlist\Frontend\Response;

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Try getting data from JSON, else form data
$data = (array) json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
    $data = $_REQUEST;
}

// Registration of default CSS/JS on web
$registerCss = (bool)$modx->getOption("registerCss", $scriptProperties, true);
$registerJs = (bool)$modx->getOption("registerJs", $scriptProperties, true);

$path = $modx->getOption('commerce.core_path', null, MODX_CORE_PATH . 'components/commerce/') . 'model/commerce/';
$params = ['mode' => $modx->getOption('commerce.mode')];

// Initiate Commerce
/** @var Commerce|null $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $path, $params);
if (!($commerce instanceof Commerce)) {
    return '<p class="error">Oops! It is not possible to view your wishlist. We\'re sorry for the inconvenience. Please try again later.</p>';
}
if ($commerce->isDisabled()) {
    return $commerce->adapter->lexicon('commerce.mode.disabled.message');
}

// Initiate Wishlist
$wishlist = $modx->getService('wishlist', 'Wishlist', $modx->getOption('commerce_wishlist.core_path', null, $modx->getOption('core_path') . 'components/commerce_wishlist/') . 'model/commerce_wishlist/', [$scriptProperties, 'user' => $user]);
if (!($wishlist instanceof Wishlist)) return '';

// Request and response
/** @var PoconoSewVac\Wishlist\Frontend\Request $request */
$request = new Request($requestMethod, $action, $commerce, $data);
/** @var PoconoSewVac\Wishlist\Frontend\Response $response */
$response = new Response($commerce);

// Get and execute the action
$action = $request->getAction();
$action->execute($response);

// Get the response
return $action->getResponse();