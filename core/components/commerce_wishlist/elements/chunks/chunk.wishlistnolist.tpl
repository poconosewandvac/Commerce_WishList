<h4 class="wishlist-list-none text-center">Save &amp; share products you want by adding a wish-list</h4>
<form class="wishlist-list-none-add" action="[[~[[*id]]]]/[[++commerce_wishlist.add_uri]]" method="POST">
    <input type="text" name="values[name]" placeholder="Name" required>
    <textarea name="values[description]" placeholder="Optional description about this list"></textarea>
    <!-- <label>Share? <input type="checkbox" name="values[share]"></label> -->
    <input type="hidden" name="type" value="add_list">
    <input type="hidden" name="secret" value="1">
    <button class="button" type="submit">Add</button>
</form>