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

// Passed values from web
$values = $modx->getOption("values", $_REQUEST, null);
$type = $modx->getOption("type", $_REQUEST, null);
$secret = $modx->getOption("secret", $_REQUEST, null);

// Registration of default CSS/JS on web
$registerCss = (bool)$modx->getOption("registerCss", $scriptProperties, true);
$registerJs = (bool)$modx->getOption("registerJs", $scriptProperties, true);

$user = $modx->user->get('id');
$resource =& $modx->resource;

// Output placeholders
$placeholders = [];

// Various useful properties array for easy access in twig
$placeholders['properties'] = [
    'resource' => $resource,
    'resource_id' => $resource->get('id'),
    'resource_url' => $modx->makeUrl($resource->get('id')),
    'view_path' => $viewPath,
    'edit_path' => $editPath,
    'delete_path' => $deletePath,
    'add_path' => $addPath,
];

// Load WishList class
$wishlist = $modx->getService('wishlist','Wishlist', $modx->getOption('commerce_wishlist.core_path', null, $modx->getOption('core_path').'components/commerce_wishlist/').'model/commerce_wishlist/', [$scriptProperties,  'user' => $user]);
if (!($wishlist instanceof Wishlist)) return '';

$wishlist->registerAssets($registerCss, $registerJs);

// Handle add/edit/delete
if ($type && $secret && is_array($values) && $user) {
    switch ($type) {
        case "add_list":
            $wishlist->addList($values);
            $modx->sendRedirect($modx->makeUrl($resource));
            break;
            
        case "edit_list":
            $wishlist->editList($values, $secret, true);
            $modx->sendRedirect($modx->makeUrl($resource) . '/' . $viewPath . '/' . $secret);
            break;
            
        case "delete_list":
            $wishlist->deleteList($values);
            $modx->sendRedirect($modx->makeUrl($resource));
            break;
    }
}

// Tries getting the list the user has requested
if ($secret) {
    $list = $wishlist->getList($secret, true);
    
    if (!$list) {
        $modx->sendForward($modx->getOption("error_page"));
    }
} else {
    $list = $wishlist->getList($wishlist->getDefaultList());
}

// If no lists on profile, invalid list, and user is logged in, show the no lists tpl
if (!$list && $user) {
    $output = $wishlist->commerce->twig->render('commerce_wishlist/no-lists.html.twig', $placeholders);
    return $wishlist->commerce->adapter->parseMODXTags($output);
} else if (!$list) {
    $modx->sendForward($modx->getOption("error_page"));
}

// Check if user has access to the requested list
$hasReadPermission = $wishlist->hasReadPermission($list->get('id'));
if (!$hasReadPermission) {
    $modx->sendUnauthorizedPage();
}

// Make list details available in the wrapper
$placeholders['list'] = $list->toArray();

// Fetch items in the list
if (isset($_REQUEST["add"])) {
    $placeholders['addlist'] = true;
} else {
    $getItems = $wishlist->getFormattedItems($list->get('id'));
    foreach ($getItems as $i) {
        $items[] = $i->toArray();
    }
    $placeholders['items'] = $items;
}

// Fetch all available lists
$getLists = $wishlist->getLists();
foreach ($getLists as $l) {
    $lists[] = $l->toArray();
}
$placeholders['lists'] = $lists;

// Render the twig file, then render MODX tags
$output = $wishlist->commerce->twig->render('commerce_wishlist/list-view.html.twig', $placeholders);
return $wishlist->commerce->adapter->parseMODXTags($output);