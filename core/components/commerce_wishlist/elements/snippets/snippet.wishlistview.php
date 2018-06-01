<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

// Template options
$tplWrapper = $modx->getOption("tplWrapper", $scriptProperties, "WishlistWrap");
$addListTpl = $modx->getOption("addListTpl", $scriptProperties, "WishlistAddList");
$listTpl = $modx->getOption("listTpl", $scriptProperties, "WishlistList");
$listHeaderTpl = $modx->getOption("listHeaderTpl", $scriptProperties, "WishlistListHeader");
$emptyListTpl = $modx->getOption("emptyListTpl", $scriptProperties, "WishlistEmptyList");
$noListTpl = $modx->getOption("noListTpl", $scriptProperties, "WishlistNoList");
$itemTpl = $modx->getOption("itemTpl", $scriptProperties, "WishlistItem");

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

// Output placeholders
$placeholders = [];

$user = $modx->user->get('id');
$resource = $modx->resource->get('id');

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
    return $modx->getChunk($noListTpl, ['addListTpl' => $addListTpl]);
} else if (!$list) {
    $modx->sendForward($modx->getOption("error_page"));
}

// Check if user has access to the requested list
$hasReadPermission = $wishlist->hasReadPermission($list->get('id'));
if (!$hasReadPermission) {
    $modx->sendUnauthorizedPage();
}

// Make list details available in the wrapper
$listArr = $list->toArray();
foreach($listArr as $key => $val) {
    $listArr['list'.$key] = $val;
    unset($listArr[$key]);
}
$placeholders = array_merge($placeholders, $listArr);

// Fetch items in the list
if (isset($_REQUEST["add"])) {
    $placeholders['addlist'] = $modx->getChunk($addListTpl);
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
$output = $wishlist->commerce->twig->render('commerce_wishlist/base.twig', $placeholders);
return $wishlist->commerce->adapter->parseMODXTags($output);