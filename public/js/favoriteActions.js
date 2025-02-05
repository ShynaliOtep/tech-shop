const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('.add-to-favorites-btn').forEach(btn => {
    btn.addEventListener('click', addToFavorite)
})


document.querySelectorAll('.remove-from-favorites-btn').forEach(btn => {
    btn.addEventListener('click', deleteFromFavorite)
})

function deleteFromFavorite(e) {
    const productId = this.dataset.productId;
    fetch('/profile/favorite/' + productId + '/remove', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        }
    )
        .then(response => response.json())
        .then(data => {
            if (data.hasOwnProperty('error')) {
                M.toast({html: `<i class="material-icons orange-text text-darken-4">error_outline</i>` + data.error});
            } else {
                if (data.success) {
                    let url = location.href;
                    if (!isGoodViewRoute(url)){
                        e.target.innerHTML = 'favorite_outline'
                        e.target.parentNode.classList.remove('remove-from-favorites-btn')
                        e.target.parentNode.classList.add('add-to-favorites-btn')
                        e.target.parentNode.removeEventListener('click', deleteFromFavorite)
                        e.target.parentNode.addEventListener('click', addToFavorite)
                    } else {
                        e.target.innerHTML = `В любимое
                                    <i class="large material-icons">
                                        favorite_border
                                    </i>`
                        e.target.classList.remove('remove-from-favorites-btn')
                        e.target.classList.add('add-to-favorites-btn')
                        e.target.removeEventListener('click', deleteFromFavorite)
                        e.target.addEventListener('click', addToFavorite)
                    }
                    if (url === 'http://pixelrental.loc/favorite' || url === 'https://pixelrental.loc/favorite'){
                        e.target.parentNode.parentNode.parentNode.parentNode.remove()
                    }
                    M.toast({html: 'Продукт успешно удалён из любимых!'});
                } else {
                    M.toast({html: 'Не удалось удалить товар из любимых.'});
                }
            }
        })
        .catch(error => {
            M.toast({html: 'Не удалось удалить товар из любимых.'});
        });
}

function addToFavorite(e) {
    const productId = this.dataset.productId;
    fetch('/profile/favorite/' + productId + '/add', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        }
    )
        .then(response => response.json())
        .then(data => {
            if (data.hasOwnProperty('error')) {
                M.toast({html: `<i class="material-icons orange-text text-darken-4">error_outline</i>` + data.error});
            } else {
                if (data.success) {
                    let url = location.href;
                    if (!isGoodViewRoute(url)){
                        e.target.innerHTML = 'favorite'
                        e.target.parentNode.classList.remove('add-to-favorites-btn')
                        e.target.parentNode.classList.add('remove-from-favorites-btn')
                        e.target.parentNode.removeEventListener('click', addToFavorite)
                        e.target.parentNode.addEventListener('click', deleteFromFavorite)
                    } else {
                        e.target.innerHTML = `Удалить из любимого
                                    <i class="material-icons">
                                        favorite
                                    </i>`
                        e.target.classList.remove('add-to-favorites-btn')
                        e.target.classList.add('remove-from-favorites-btn')
                        e.target.removeEventListener('click', addToFavorite)
                        e.target.addEventListener('click', deleteFromFavorite)
                    }
                    M.toast({html: 'Продукт успешно добавлен в любимые!'});
                } else {
                    M.toast({html: 'Не удалось добавить товар в любимые.'});
                }
            }
        })
        .catch(error => {
            M.toast({html: 'Не удалось добавить товар в любимые.'});
        });
}

const httpPattern = /^http:\/\/pixelrental\.loc\/\d+$/;
const httpsPattern = /^https:\/\/pixelrental\.loc\/\d+$/;

function isGoodViewRoute(route) {
    return httpPattern.test(route) || httpsPattern.test(route);
}
