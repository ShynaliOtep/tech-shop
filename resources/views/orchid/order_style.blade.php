<script>
    (function () {

        function colorize() {
            colorizeRows('.order-status-cancelled', '#ff8383');
            colorizeRows('.order-status-in_rent', '#6161ff');
            colorizeRows('.order-status-returned', '#ffdba5');
        }

        function colorizeRows(selector, color) {
            document.querySelectorAll(selector).forEach(element => {
                const tr = element.closest('tr');
                if (!tr) return;

                [...tr.children].forEach(td => {
                    td.style.backgroundColor = color;
                });
            });
        }

        // 🔥 Orchid / Turbo SPA переходы
        document.addEventListener('turbo:load', colorize);

        // 🔥 на случай первого захода без turbo
        if (document.readyState === 'complete') {
            colorize();
        }

    })();
</script>
