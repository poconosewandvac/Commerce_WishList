// Wishlist edit list modal
var wishlistModal = document.querySelector("#wishlist-list-edit-dialog");
var wishlistEditListBtn = document.querySelector("#wishlist-modal-open");
var wishlistEditListCloseBtn = document.querySelector("#wishlist-list-dialog-edit-close");

if (wishlistEditListBtn) {
    wishlistEditListBtn.addEventListener("click", function () {
        wishlistModal.classList.toggle("active");
    });

    wishlistEditListCloseBtn.addEventListener("click", function () {
        wishlistModal.classList.toggle("active");
    })
}

document.querySelector("#wishlist-list-delete").addEventListener("click", function () {
    if (confirm("Are you sure you want to delete this list?")) {
        document.querySelector("#wishlist-list-delete-form").submit();
    }
});

document.querySelector("#wishlist-list-save").addEventListener("click", function () {
    document.querySelector("#wishlist-list-edit-form").submit();
});