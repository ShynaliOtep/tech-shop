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
        this.notify();
    },

    removeFromCart(productId) {
        let cart = this.getCart();
        delete cart[productId];
        localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
        this.notify();
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
        this.notify();
    },

    getTotalItems() {
        return Object.values(this.getCart()).reduce(
            (sum, item) => sum + item.quantity,
            0,
        );
    },

    getQuantity(productId) {
        let cart = this.getCart();
        if (cart[productId]) {
            return cart[productId].quantity;
        }
        return 0;
    },
    clearCart() {
        localStorage.removeItem(this.CART_KEY);
        this.notify();
    },

    /**
     * Положить товар с выбранным периодом (страница товара → «Забронировать на эти даты»).
     * Количество заменяется, а не прибавляется: человек указал его явно.
     */
    setItem(productId, quantity, date) {
        let cart = this.getCart();
        cart[productId] = { quantity, date: date || null };
        localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
        this.notify();
    },

    getDate(productId) {
        return this.getCart()[productId]?.date || null;
    },

    /** Запомнить период, выбранный в корзине, чтобы он не терялся при возврате на страницу. */
    setDate(productId, date) {
        let cart = this.getCart();
        if (!cart[productId]) {
            return;
        }
        cart[productId].date = date || null;
        localStorage.setItem(this.CART_KEY, JSON.stringify(cart));
    },

    /** Обновить счётчики корзины в шапке и нижнем меню (см. bottom-navbar.blade.php). */
    notify() {
        if (typeof window.updateCartBadges === "function") {
            window.updateCartBadges();
        }
    },
};

export default Cart;
