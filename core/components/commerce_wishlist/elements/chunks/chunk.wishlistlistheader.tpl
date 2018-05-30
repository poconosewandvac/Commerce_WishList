<h4 class="wishlist-list-title">[[+listname:htmlent]] <small><button id="wishlist-modal-open" class="wishlist-modal-open">(Edit)</button></small></h4>
<p id="wishlist-list-desc" class="wishlist-list-desc">[[+listdescription:htmlent]]</p>

<div id="wishlist-list-edit-dialog" class="wishlist-list-edit-dialog">
    <div>
        <header>
            <h4>Edit List <span id="wishlist-list-dialog-edit-close" class="wishlist-list-dialog-edit-close">&times;</span></h4>
        </header>
        <section>
            <form id="wishlist-list-edit-form" class="wishlist-list-edit-form" action="[[~[[*id]]]]/edit/list" method="POST">
                <label for="wishlist-list-name">List Name</label>
                <input type="text" name="values[name]" id="wishlist-list-name" placeholder="List name" value="[[+listname:htmlent]]" required>
                
                <label for="wishlist-list-desc">List Description (optional)</label>
                <textarea name="values[description]" id="wishlist-list-desc" placeholder="Optional list description">[[+listdescription:htmlent]]</textarea>
                
                <label>Share this list? 
                    <input type="hidden" name="values[share]" value="0">
                    <input id="wishlist-list-share" class="wishlist-list-share" type="checkbox" name="values[share]" value="1" [[+listshare:is=`1`:then=`checked`:default=``]]>
                </label>
                    
                <input type="hidden" name="secret" value="[[+listsecret]]">
                <input type="hidden" name="type" value="edit_list">
            </form>
            
            <form id="wishlist-list-delete-form" class="wishlist-list-delete-form" action="[[~[[*id]]]]/[[++commerce_wishlist.delete_uri]]" method="POST">
                <input type="hidden" name="type" value="delete_list">
                <input type="hidden" name="secret" value="[[+listsecret]]">
            </form>
        </section>
        <footer>
            <button class="wishlist-list-delete" id="wishlist-list-delete">Delete List</button>
            <button class="wishlist-list-save" id="wishlist-list-save">Save Edits</button>
        </footer>
    </div>
</div>