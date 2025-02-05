const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function cancelOrder(e) {
    const orderId = document.querySelector('#order-id-holder').value;
    await fetch('/profile/orders/' + orderId + '/cancel', {
        method: 'GET', headers: {
            'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => response.json())
        .then(data => {
            if (data.hasOwnProperty('error')) {
                M.toast({html: `<i class="material-icons orange-text text-darken-4">error_outline</i>` + data.error});
            } else {
                if (data.success) {
                    M.toast({html: 'Заказ успешно отменён'});
                    window.location.replace('/profile/orders')
                }
            }
        })

}
