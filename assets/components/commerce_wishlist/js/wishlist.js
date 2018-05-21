// List edit modal
var wishlistModal = document.querySelector("#wishlist-list-edit-dialog");
var wishlistEditListBtn = document.querySelector("#wishlist-modal-open");
var wishlistEditListCloseBtn = document.querySelector("#wishlist-list-dialog-edit-close");
var wishlistListDeleteBtn = document.querySelector("#wishlist-list-delete");
var wishlistListSaveBtn = document.querySelector("#wishlist-list-save");

if (wishlistEditListBtn) {
    wishlistEditListBtn.addEventListener("click", function () {
        wishlistModal.classList.toggle("active");
    });

    wishlistEditListCloseBtn.addEventListener("click", function () {
        wishlistModal.classList.toggle("active");
    });

    wishlistListDeleteBtn.addEventListener("click", function () {
        if (confirm("Are you sure you want to delete this list? All items will be lost.")) {
            document.querySelector("#wishlist-list-delete-form").submit();
        }
    });

    wishlistListSaveBtn.addEventListener("click", function () {
        document.querySelector("#wishlist-list-edit-form").submit();
    });
}

// Item edit
var wishlistEditItemBtn = document.querySelector("#wishlist-item-edit");

if (wishlistEditItemBtn) {
    wishlistEditItemBtn.addEventListener("click", function () {
        document.querySelector(".wishlist-item-edit-wrap").classList.toggle("active");
    });
}