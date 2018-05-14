<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

$tplWrapper = $modx->getOption("tplWrapper", $scriptProperties, "WishlistWrap");
$addListTpl = $modx->getOption("addListTpl", $scriptProperties, "WishlistAddList");
$listTpl = $modx->getOption("listTpl", $scriptProperties, "WishlistList");
$itemTpl = $modx->getOption("itemTpl", $scriptProperties, "WishlistItem");
$values = $modx->getOption("values", $scriptProperties, $_REQUEST["values"]);
$registerCss = (bool)$modx->getOption("registerCss", $scriptProperties, true);
$registerJs = (bool)$modx->getOption("registerJs", $scriptProperties, true);
$placeholders = [];

// Check if user is logged in
$user = $modx->user->get('id');

// Load WishList class
$wishlist = $modx->getService('wishlist','Wishlist', $modx->getOption('commerce_wishlist.core_path', null, $modx->getOption('core_path').'components/commerce_wishlist/').'model/commerce_wishlist/', [$scriptProperties,  'user' => $user]);
if (!($wishlist instanceof Wishlist)) return '';

$wishlist->registerAssets($registerCss, $registerJs);

if (isset($_REQUEST["add"]) && isset($_REQUEST["type"]) && is_array($values) && $user) {
    switch ($_REQUEST["type"]) {
        case "list":
            $wishlist->addList($values);
            break;
        
        case "item":
            $wishlist->addItem($values);
            break;
    }
} else if (isset($_REQUEST["add"])) {
    $addList = $modx->getChunk($addListTpl);
}

// Guest viewing of wish list
$hasPermission = $wishlist->hasPermission($_REQUEST["secret"], true);
if (isset($_REQUEST["secret"]) && $hasPermission) {
    $getItems = $wishlist->getFormattedItems($_REQUEST["secret"], true);
    $getList = $wishlist->getList($_REQUEST["secret"], true);
} else if ($hasPermission) {
    $getItems = $wishlist->getFormattedItems($wishlist->getDefaultList());
} else {
    $modx->sendUnauthorizedPage();
}

foreach ($getItems as $item) {
    $items .= $modx->getChunk($itemTpl, $item->toArray());
}

// Fetch all available lists
$getLists = $wishlist->getLists();
foreach ($getLists as $list) {
    $lists .= $modx->getChunk($listTpl, $list->toArray());
}

return $modx->getChunk($tplWrapper, [
    'lists' => $lists,
    'listname' => 'Test',
    'items' => $items,
    'addlist' => $addList
]);