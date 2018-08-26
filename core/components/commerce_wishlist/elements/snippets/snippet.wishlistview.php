<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

$values = $modx->getOption("values", $_REQUEST, []);
$type = $modx->getOption("type", $_REQUEST, null);
$secret = $modx->getOption("secret", $_REQUEST, null);

// Registration of default CSS/JS on web
$registerCss = (bool)$modx->getOption("registerCss", $scriptProperties, true);
$registerJs = (bool)$modx->getOption("registerJs", $scriptProperties, true);

$user = $modx->user->get('id');
$resource = &$modx->resource;

// Output placeholders
$placeholders = [];

// Various useful properties array for easy access in twig
$placeholders['properties'] = [
    'resource' => $resource,
];

// Load WishList class
$wishlist = $modx->getService('wishlist', 'Wishlist', $modx->getOption('commerce_wishlist.core_path', null, $modx->getOption('core_path') . 'components/commerce_wishlist/') . 'model/commerce_wishlist/', [$scriptProperties, 'user' => $user]);
if (!($wishlist instanceof Wishlist)) return '';
$wishlist->registerAssets($registerCss, $registerJs);

// Tries getting the list the user has requested
if ($secret) {
    $list = $wishlist->getList($secret);

    if (!$list) {
        $modx->sendForward($modx->getOption("error_page"));
    }
} else {
    $list = $wishlist->getList($wishlist->getDefaultList());
}

// If no lists on profile, invalid list, and user is logged in, show the no lists tpl
if (!$list && $user) {
    $output = $wishlist->commerce->twig->render('commerce_wishlist/no-list.html.twig', $placeholders);
    return $wishlist->commerce->adapter->parseMODXTags($output);
} else if (!$list) {
    $modx->sendForward($modx->getOption("error_page"));
}

if (!$list->hasReadPermission()) {
    $modx->sendUnauthorizedPage();
}

// Add to properties for use in Twig
$placeholders['hasEditPermission'] = $list->hasEditPermission();
$placeholders['hasReadPermission'] = $list->hasReadPermission();;

// Make list details available in the wrapper
$placeholders['list'] = $list->toArray();

// Fetch all available lists
$getLists = $wishlist->getLists();
foreach ($getLists as $l) {
    $lists[] = $l->toArray();
}
if (empty($lists)) {
    $output = $wishlist->commerce->twig->render('commerce_wishlist/no-list.html.twig', $placeholders);
    return $wishlist->commerce->adapter->parseMODXTags($output);
}
$placeholders['lists'] = $lists;

// Fetch items in the list
if ($_REQUEST["type"] === "add_list") {
    $output = $wishlist->commerce->twig->render('commerce_wishlist/add-list.html.twig', $placeholders);
    return $wishlist->commerce->adapter->parseMODXTags($output);
} else {
    $getItems = $wishlist->getFormattedItems($list->get('id'));
    foreach ($getItems as $i) {
        $item = $i->toArray();
        
        // Moves products into sub array 'product' for Twig. Unset product id from WishlistItem, because comProduct has it anyways
        $item['product'] = [];
        foreach ($item as $key => $value) {
            if (strncmp($key, 'product_', 8) === 0) {
                $item['product'][substr($key, 8, strlen($key))] = $value;
                unset($item[$key]);
            }
        }

        $items[] = $item;
    }
    $placeholders['items'] = $items;
}

// Render the twig file, then render MODX tags
$output = $wishlist->commerce->twig->render('commerce_wishlist/list-view.html.twig', $placeholders);
return $wishlist->commerce->adapter->parseMODXTags($output);