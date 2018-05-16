<?php
/**
 * Wish List for Commerce
 * 
 * Made by Tony Klapatch <tony@klapatch.net>
 */

$tplWrapper = $modx->getOption("tplWrapper", $scriptProperties, "WishlistWrap");
$addListTpl = $modx->getOption("addListTpl", $scriptProperties, "WishlistAddList");
$listTpl = $modx->getOption("listTpl", $scriptProperties, "WishlistList");
$emptyListTpl = $modx->getOption("emptyListTpl", $scriptProperties, "WishlistEmptyList");
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

// Tries getting the list the user has requested
if (isset($_REQUEST["secret"])) {
    $list = $wishlist->getList($_REQUEST["secret"], true);

} else if ($user) {
    $list = $wishlist->getList($wishlist->getDefaultList());
}

// Deny on unknown list
if (!$list) {
    $modx->sendUnauthorizedPage();
}

// Check if user has access to the requested list
$hasReadPermission = $wishlist->hasReadPermission($list->get('id'));
if (!$hasReadPermission) {
    $modx->sendUnauthorizedPage();
}

if (isset($_REQUEST["add"]) && isset($_REQUEST["type"]) && is_array($values) && $user) {
    switch ($_REQUEST["type"]) {
        case "list":
            $wishlist->addList($values);
            break;
        
        case "item":
            $wishlist->addItem($values);
            break;
    }
}

// Fetch items in the list
if (isset($_REQUEST["add"])) {
    $placeholders['addlist'] = $modx->getChunk($addListTpl);
} else {
    $getItems = $wishlist->getFormattedItems($list->get('id'));
    foreach ($getItems as $i) {
        $items .= $modx->getChunk($itemTpl, $i->toArray());
    }
    
    if ($items) {
        $placeholders['items'] = $items;
    } else {
        $placeholders['items'] = $modx->getChunk($emptyListTpl);
    }
}

// Fetch all available lists
$getLists = $wishlist->getLists();
foreach ($getLists as $l) {
    $lists .= $modx->getChunk($listTpl, $l->toArray());
}
$placeholders['lists'] = $lists;

// Make list details available in the wrapper
$listArr = $list->toArray();
foreach($listArr as $key => $val) {
    $listArr['list'.$key] = $val;
    unset($listArr[$key]);
}
$placeholders = array_merge($placeholders, $listArr);

$wishlist->registerAssets($registerCss, $registerJs);
return $modx->getChunk($tplWrapper, $placeholders);