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

    getQuantity(productId) {
        let cart = this.getCart();
        if (cart[productId]) {
           return cart[productId].quantity;
        }
        return 0;
    }
};

export default Cart;
