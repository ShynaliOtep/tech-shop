const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll(".good-add-to-favorites").forEach((btn) => {
    btn.addEventListener("click", addToFavorite);
});

document.querySelectorAll(".good-remove-to-favorites").forEach((btn) => {
    btn.addEventListener("click", deleteFromFavorite);
});

function deleteFromFavorite(e) {
    const productId = this.dataset.productId;
    fetch("/profile/favorite/" + productId + "/remove", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                let url = location.href;
                if (!isGoodViewRoute(url)) {
                    e.target.parentNode.parentNode.classList.remove(
                        "remove-from-favorites-btn",
                    );
                    e.target.parentNode.parentNode.classList.add(
                        "good-remove-to-favorites",
                    );
                    // e.target.style.display ='none'
                    // e.parentNode.children[1].style.display = 'block'
                    // e.target.parentNode.removeEventListener('click', deleteFromFavorite)
                    // e.target.parentNode.addEventListener('click', addToFavorite)
                } else {
                    e.target.classList.remove("remove-from-favorites-btn");
                    e.target.classList.add("good-add-to-favorites");
                    // e.target.children[0].style.display ='none'
                    // e.parentNode.children[1].style.display = 'block'
                    // e.target.removeEventListener('click', deleteFromFavorite)
                    // e.target.addEventListener('click', addToFavorite)
                }
                if (
                    url === "http://pixelrental.loc/favorite" ||
                    url === "https://pixelrental.loc/favorite"
                ) {
                    e.target.parentNode.parentNode.parentNode.parentNode.remove();
                }
                // alert('Продукт успешно удалён из любимых!');
            } else {
                alert("Не удалось удалить товар из любимых.");
            }
        })
        .catch((error) => {
            alert("Не удалось удалить товар из любимых.");
        });
}

function addToFavorite(e) {
    const productId = this.dataset.productId;
    fetch("/profile/favorite/" + productId + "/add", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                let url = location.href;
                if (!isGoodViewRoute(url)) {
                    e.target.parentNode.classList.remove(
                        "good-add-to-favorites",
                    );
                    e.target.parentNode.classList.add(
                        "good-remove-to-favorites",
                    );
                    // e.target.parentNode.removeEventListener('click', addToFavorite)
                    // e.target.parentNode.addEventListener('click', deleteFromFavorite)
                } else {
                    e.target.classList.remove("add-to-favorites-btn");
                    e.target.classList.add("remove-from-favorites-btn");
                    // e.target.removeEventListener('click', addToFavorite)
                    // e.target.addEventListener('click', deleteFromFavorite)
                }
                //alert('Продукт успешно добавлен в любимые!')
            } else {
                alert("Не удалось добавить товар в любимые.");
            }
        })
        .catch((error) => {
            M.toast({ html: "Не удалось добавить товар в любимые." });
        });
}

const httpPattern = /^http:\/\/pixelrental\.loc\/\d+$/;
const httpsPattern = /^https:\/\/pixelrental\.loc\/\d+$/;

function isGoodViewRoute(route) {
    return httpPattern.test(route) || httpsPattern.test(route);
}
