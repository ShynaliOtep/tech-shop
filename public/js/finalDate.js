var startDate = document.getElementById("start_date");
var endDate = document.getElementById("end_date");
var totalCount = document.getElementById('total_count');
var totalPrice = document.getElementById('total_price');
var confirmHolder = document.getElementById('confirm-holder');

startDate.onchange = () => {
    calculateDays();
};

endDate.onchange = () => {
    calculateDays();
};

function parseDateString(dateString) {
    var parts = dateString.split('/');
    var day = parts[0];
    var month = parts[1] - 1;
    var year = parts[2];
    return new Date(year, month, day);
}

function calculateDays() {
    var startDateInput = document.getElementById("start_date").value;
    var endDateInput = document.getElementById("end_date").value;
    if (startDateInput && endDateInput){
        var startDate = parseDateString(startDateInput);
        var endDate = parseDateString(endDateInput);
        var timeDifference = endDate - startDate;
        var diff = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
        if (diff > 0) {
            document.getElementById("final-date-counter").innerHTML = 'Итоговое количество дней аренды: ' + diff;
            document.getElementById("final-item-counter").innerHTML = 'Общее количество товаров: ' + totalCount.value;
            document.getElementById("final-price-counter").innerHTML = 'Общее количество товаров: ' + totalPrice.value * diff;
            confirmHolder.innerHTML =
                `<a href="#confirm-modal"
                   class="btn orange darken-4 auth-link valign-wrapper next-step-btn modal-trigger">
                    Оформить заказ <i class="material-icons">done</i>
                </a>`
        } else {
            document.getElementById("final-date-counter").innerHTML = 'Неверная дата!';
            document.getElementById("final-item-counter").innerHTML = '';
            document.getElementById("final-price-counter").innerHTML = '';
        }
    } else {
        document.getElementById("final-date-counter").innerHTML = '';
        document.getElementById("final-item-counter").innerHTML = '';
        document.getElementById("final-price-counter").innerHTML = '';

    }
}
