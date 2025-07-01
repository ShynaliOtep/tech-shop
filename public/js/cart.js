document.addEventListener('DOMContentLoaded', function () {
    updateAllCartUI();

    document.addEventListener('click', function (event) {
        const target = event.target;
        const button = target.closest('.add-to-cart-btn');


        if (!button) return;

        const productId = button.dataset.productId;

        console.log(target.classList)

        if (target.classList.contains('add-to-cart-btn-plus')) {

            const maxCount = parseInt(button.dataset.maxCount || '0'); // ⬅️ получаем max count
            const cart = Cart.getCart();
            const item = cart[productId];
            if (item.quantity >= maxCount) {
                return;
            }

            Cart.addToCart(productId);
        } else if (target.classList.contains('add-to-cart-btn-minus')) {
            Cart.decreaseQuantity(productId);
        } else if (target.classList.contains('add-to-cart-btn-delete')) {
            Cart.removeFromCart(productId);
        } else if (target.classList.contains('add-to-cart-btn-text') ||
            target.classList.contains('good-add-to-card-text')) {
            Cart.addToCart(productId);
        } else {
            const width = window.innerWidth;
            console.log(width)
            if (width <= 600) {
                console.log(width)
                Cart.addToCart(productId);
            }
            return;
        }

        updateCartUI(productId);
    });
});

function updateCartUI(productId) {
    const cart = Cart.getCart();
    const buttons = document.querySelectorAll(`.add-to-cart-btn[data-product-id="${productId}"]`);

    buttons.forEach(button => {
        const countElement = button.querySelector('.add-to-cart-btn-count');
        const deleteBtn = button.querySelector('.add-to-cart-btn-delete');
        const minusBtn = button.querySelector('.add-to-cart-btn-minus');
        const plusBtn = button.querySelector('.add-to-cart-btn-plus'); // ⬅️ добавляем
        const cartControls = button.querySelector('.cart-controls');
        const defaultText = button.querySelector('.add-to-cart-btn-text');

        const item = cart[productId];
        const maxCount = parseInt(button.dataset.maxCount || '0'); // ⬅️ получаем max count
        const screenWidth = window.innerWidth;
        if (item) {
            button.classList.add('cart-active');
            countElement.textContent = item.quantity;
            cartControls.style.display = "flex";
            defaultText.style.display = "none";

            if (screenWidth <= 600) {
                button.style.width = '100%'
                button.style.borderBottomLeftRadius = '7px'
            }

            // Логика показа кнопок -
            if (item.quantity > 1) {
                minusBtn.style.display = "flex";
                deleteBtn.style.display = "none";
            } else {
                minusBtn.style.display = "none";
                deleteBtn.style.display = "flex";
            }

            // // ⬇️ Логика скрытия кнопки +
            // if (item.quantity >= maxCount) {
            //     plusBtn.style.color = "grey";
            // } else {
            //     plusBtn.style.opacity = "#ffffff";
            // }

        } else {
            button.classList.remove('cart-active');
            countElement.textContent = "0";
            deleteBtn.style.display = "none";
            minusBtn.style.display = "none";
            plusBtn.style.opacity = "1"; // сбрасываем видимость +
            cartControls.style.display = "none";
            defaultText.style.display = "flex";

            if (screenWidth <= 600) {
                button.style.width = '50%'
                button.style.borderBottomLeftRadius = '0'
            }

        }
    });

    updateCartCounter();
}


function updateCartCounter() {
    const cartCountContainer = document.querySelector('.in-cart-item-counter');
    const headerCart =  document.querySelector('.header-cart');
    const quantity =  Cart.getTotalItems();
    console.log(quantity)
    if (quantity) {
        headerCart.style.color = '#ffffff'
        cartCountContainer.style.display = 'block'
        cartCountContainer.textContent = quantity
        headerCart.style.backgroundColor = '#FF962E'
    } else {
        cartCountContainer.style.display = 'none'
    }
}

function updateAllCartUI() {
    Object.keys(Cart.getCart()).forEach(updateCartUI);
}

const Cart = {
    CART_KEY: "cart_front",

    getCart() {
        return JSON.parse(localStorage.getItem(this.CART_KEY)) || {};
    },

    addToCart(productId) {
        let cart = this.getCart();
        if (!cart[productId]) {
            cart[productId] = { quantity: 1 };
        } else {
            cart[productId].quantity += 1;
        }
        localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
        setTimeout(sendCartData, 1000);
    },

    removeFromCart(productId) {
        let cart = this.getCart();
        delete cart[productId];
        localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
        setTimeout(sendCartData, 1000);
    },

    decreaseQuantity(productId) {
        let cart = this.getCart();
        if (cart[productId]) {
            if (cart[productId].quantity > 1) {
                cart[productId].quantity -= 1;
            } else {
                delete cart[productId];
            }
            localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
        }
        setTimeout(sendCartData, 1000);
    },

    getTotalItems() {
        return Object.values(this.getCart()).reduce((sum, item) => sum + item.quantity, 0);
    },
};




function sendCartData() {
    const cart = localStorage.getItem("cart_front");
    if (!cart) return; // Если корзина пуста, ничего не делаем
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    console.log(cart);
    fetch("/cart/sync", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: cart
    });
}
