<div class="wishlist-item-row">
    <div class="wishlist-item-row-inner">
        <div class="wishlist-item-image">
            <img src="[[+image:default=`https://placehold.it/250x250`]]" alt="[[+name]]">
        </div>
        <div class="wishlist-item-desc">
            <h4 class="wishlist-item-name"><a href="[[~[[+target]]]]">[[+name]]</a> <small>Added on [[+date:date=`%B %e, %Y`]]</small></h4>
            <p>[[+description]]</p>
            <p><strong>Price:</strong> [[+price]]</p>
            <p><strong>Note:</strong> [[+note:htmlent]]</p>
            
            <div class="wishlist-item-delete-wrap">
                <form id="wishlist-item-delete-form" class="wishlist-item-delete-form" action="[[~[[*id]]]]/delete/item">
                    <input type="hidden" name="type" value="delete_item">
                    <input type="hidden" name="secret" value="[[+id]]">
                    <button type="submit" id="wishlist-item-remove" class="wishlist-item-remove">Remove</button>
                </form>
            </div>
            
            <button id="wishlist-item-edit" class="wishlist-item-add-note">Edit this item</button>
            <div id="wishlist-item-edit-wrap" class="wishlist-item-edit-wrap">
                <form id="wishlist-item-edit-form" class="wishlist-item-edit-form" action="[[~[[*id]]]]/edit/item" method="POST">
                    <label for="wishlist-item-note">Item Note</label>
                    <textarea class="wishlist-item-note" id="wishlist-item-note" name="values[note]" placeholder="Notes on this item">[[+note]]</textarea>
                    
                    <input type="hidden" name="type" value="edit_item">
                    <input type="hidden" name="secret" value="[[+id]]">
                    <button type="submit" class="wishlist-item-save">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>