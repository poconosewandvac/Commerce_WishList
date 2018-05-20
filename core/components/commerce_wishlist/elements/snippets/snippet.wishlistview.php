<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

$tplWrapper = $modx->getOption("tplWrapper", $scriptProperties, "WishlistWrap");
$addListTpl = $modx->getOption("addListTpl", $scriptProperties, "WishlistAddList");
$listTpl = $modx->getOption("listTpl", $scriptProperties, "WishlistList");
$listHeaderTpl = $modx->getOption("listHeaderTpl", $scriptProperties, "WishlistListHeader");
$emptyListTpl = $modx->getOption("emptyListTpl", $scriptProperties, "WishlistEmptyList");
$noListTpl = $modx->getOption("noListTpl", $scriptProperties, "WishlistNoList");
$itemTpl = $modx->getOption("itemTpl", $scriptProperties, "WishlistItem");
$values = $modx->getOption("values", $scriptProperties, $_REQUEST["values"]);
$registerCss = (bool)$modx->getOption("registerCss", $scriptProperties, true);
$registerJs = (bool)$modx->getOption("registerJs", $scriptProperties, true);
$placeholders = [];

$user = $modx->user->get('id');
$resource = $modx->resource->get('id');

// Load WishList class
$wishlist = $modx->getService('wishlist','Wishlist', $modx->getOption('commerce_wishlist.core_path', null, $modx->getOption('core_path').'components/commerce_wishlist/').'model/commerce_wishlist/', [$scriptProperties,  'user' => $user]);
if (!($wishlist instanceof Wishlist)) return '';

// Handle adding
if (isset($_REQUEST["type"]) && isset($_REQUEST["secret"]) && is_array($values) && $user) {
    switch ($_REQUEST["type"]) {
        case "add":
            $wishlist->addList($values);
            $modx->sendRedirect($modx->makeUrl($resource));
            break;
            
        case "edit":
            $wishlist->editList($values, $_REQUEST["secret"], true);
            $modx->sendRedirect($modx->makeUrl($resource));
            break;
            
        case "delete":
            $wishlist->deleteList($values);
            $modx->sendRedirect($modx->makeUrl($resource));
            break;
    }
}

// Tries getting the list the user has requested
$defaultList = $wishlist->getDefaultList();
if (isset($_REQUEST["secret"])) {
    $list = $wishlist->getList($_REQUEST["secret"], true);

} else if ($user) {
    $list = $wishlist->getList($defaultList);
}

// Deny on unknown list
if (!$list && !$user) {
    $modx->sendUnauthorizedPage();
} else if (!$defaultList) {
    return $modx->getChunk($noListTpl, ['addListTpl' => $addListTpl]);
} else if (!$list) {
    $modx->sendRedirect($modx->makeUrl($resource));
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
        $items .= $modx->getChunk($itemTpl, $i->toArray());
    }
    
    $placeholders['items'] = $modx->getChunk($listHeaderTpl, $listArr);
    if ($items) {
        $placeholders['items'] .= $items;
    } else {
        $placeholders['items'] .= $modx->getChunk($emptyListTpl);
    }
}

// Fetch all available lists
$getLists = $wishlist->getLists();
foreach ($getLists as $l) {
    $lists .= $modx->getChunk($listTpl, $l->toArray());
}
$placeholders['lists'] = $lists;

$wishlist->registerAssets($registerCss, $registerJs);
return $modx->getChunk($tplWrapper, $placeholders);