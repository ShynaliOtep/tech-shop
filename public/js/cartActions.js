const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function removeFromCart(e) {
    {
        const productId = this.dataset.productId;
        fetch('/remove-from-cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({product_id: productId}),
        })
            .then(() => {
                availableItemsLength--;
                enableAllOtherOptions(e.target.parentNode.parentNode.dataset.goodId, e.target.parentNode.parentNode.dataset.goodItemId)
                e.target.parentNode.parentNode.remove()
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
}

window.onload = function () {
    document.querySelector('.client-discount-holder').classList.remove('hide')
    document.querySelector('.main-loader').classList.add('hide')
};

const loaderElement = `
                                            <div class="col s12 center loader-holder">
                                                <div class="preloader-wrapper active">
                                                    <div class="spinner-layer spinner-orange-only">
                                                        <div class="circle-clipper left">
                                                            <div class="circle"></div>
                                                        </div><div class="gap-patch">
                                                            <div class="circle"></div>
                                                        </div><div class="circle-clipper right">
                                                            <div class="circle"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
`

function changeCart(e) {
    {
        e.preventDefault()
        const cartKey = this.dataset.cartKey;
        const additionalId = this.dataset.additionalId;
        const url = e.target.checked ? '/additional-add' : '/additional-remove'
        e.target.disabled = true
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                cart_key: cartKey,
                additional_id: additionalId,
            }),
        })
            .then(() => {
                const amountOfDays = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.amountOfDays ?? 1
                var discount = +document.querySelector('.client-discount-holder').dataset.discountPercent
                const controlSumNode = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.good-cost-holder');
                if (isNaN(discount)) {
                    discount = 0
                }
                if (e.target.checked) {
                    controlSumNode.innerHTML = (+controlSumNode.innerHTML + (+this.dataset.additionalCost / 100 * (100 - discount)) * amountOfDays)
                    changeTotalCost((+this.dataset.additionalCost / 100 * (100 - discount)) * amountOfDays, 'up')
                } else {
                    controlSumNode.innerHTML = (+controlSumNode.innerHTML - (+this.dataset.additionalCost / 100 * (100 - discount)) * amountOfDays)
                    changeTotalCost((+this.dataset.additionalCost / 100 * (100 - discount)) * amountOfDays, 'down')
                }
                e.target.disabled = false
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
}

function changeTotalCost(value, mode) {
    let currentValue = parseInt(totalCostHolderNumber.innerHTML, 10) ?? 0
    if (mode === 'up') {
        currentValue += value
    } else {
        currentValue -= value
    }

    totalCostHolderNumber.innerHTML = currentValue.toString()
}

const RENT_TIME_TYPE_ALL = 'all';
const RENT_TIME_TYPE_INDIVIDUAL = 'individual';
const ORDER_AVAILABLE = true;
const ORDER_NOT_AVAILABLE = false;
let rentTimeType = RENT_TIME_TYPE_ALL;
let isOrderAvailable = ORDER_NOT_AVAILABLE;
let selectedItems = [];
const modalTriggerButton = document.querySelector('.btn.orange.darken-4.auth-link.valign-wrapper.next-step-btn.modal-trigger')
const itemTotalCostTextElement = document.querySelectorAll('h5[class="inline"]');
const mainFormElement = document.querySelector('.col.s12.m9.goods-list')
const informationalTextElement = document.querySelector('.col.s12.m3.additional-info.white-text.hide-on-med-and-down')
const formRentTypeAll = document.querySelector('.col.s12.m9.goods-list-rent-type-all.hide')
let rentStartDateV2 = ''
let rentStartTimeV2 = ''
let rentEndDateV2 = ''
let rentEndTimeV2 = ''
let total_cost = 0;
const startDatepicker = document.querySelector('.datepicker.white-text.beginning-date-rent-type-all.hide')
const startTimePicker = document.querySelector('.white-text.left.rent-starttime-rent-type-all.hide');
const endDatePicker = document.querySelector('.datepicker.white-text.endingdate-rent-type-all.hide');
const endTimePicker = document.querySelector('.white-text.left.rent-end-time-rent-type-all.hide')
const startDatepickerDiv = document.querySelector('.col.s6.input-field.beginning-date-field-rent-type-all.hide')
const startTimePickerDiv = document.querySelector('.col.s6.input-field.white-text.rent-starttime-field-rent-type-all.hide');
const endDatePickerDiv = document.querySelector('.col.s6.input-field.ending-datefield-rent-type-all.hide');
const endTimePickerDiv = document.querySelector('.col.s6.input-field.white-text.rent-endtime-field-rent-type-all.hide')

let availableItemsLength = 0

document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="rent-type"]');
    changeStateIndividualCost('add')
    radioButtons.forEach(radio => {
        radio.addEventListener('change',  async function () {
            radioButtons.forEach(radio => {
                radio.disabled = true;
            })
            if (this.value === RENT_TIME_TYPE_ALL) {
                rentTimeType = RENT_TIME_TYPE_ALL;
                itemTotalCostTextElement.forEach(item => {
                    item.classList.add('hide')
                    changeStateIndividualCost('add')
                })
               // mainFormElement.classList.add('hide');
                informationalTextElement.classList.remove('hide');
                selectedItems = [];
                itemIdPickers = document.querySelectorAll('.item-id-selector')
                availableItemsLength = itemIdPickers.length
                formRentTypeAll.classList.remove('hide');

                await fillAllRentType()

            } else {
                rentTimeType = RENT_TIME_TYPE_INDIVIDUAL;
                itemTotalCostTextElement.forEach(item => {
                    item.classList.remove('hide')
                    changeStateIndividualCost('remove')
                })
                selectedItems = [];
              //  mainFormElement.classList.remove('hide');
                informationalTextElement.classList.remove('hide');
                formRentTypeAll.classList.add('hide');
                itemIdPickers = document.querySelectorAll('.item-id-selector')
                availableItemsLength = itemIdPickers.length

                fillIndividualRentType()
            }
        })
    })

    modalTriggerButton.addEventListener('click', (event) => {
        if (!isOrderAvailable) {
            event.preventDefault();
        }
    })

    updateButtonState();

})

const fillAllRentType = async () => {
    await fillTimepickers()
}

const fillTimepickers = async ()=> {
    startDatepickerDiv.classList.remove('hide');
    startDatepicker.classList.remove('hide')
    startDatepicker.value = null
    startDatepicker.placeholder = 'Дата начала'
    try {
        startTimePickerDiv.classList.add('hide')
        startTimePicker.classList.add('hide')
        startTimePicker.placeholder = 'Время начала'
        startTimePicker.value = null;
    } catch (e) {}
    try {
        endDatePickerDiv.classList.add('hide')
        endDatePicker.classList.add('hide')
        endDatePicker.placeholder = 'Дата конца'
        endDatePicker.value = null;
    } catch (e) {}
    try {
        endTimePickerDiv.classList.add('hide')
        endTimePicker.classList.add('hide')
        endTimePicker.placeholder = 'Время конца'
        endTimePicker.value = null;
    } catch (e) {}
    let instance = M.Datepicker.init(startDatepicker, {
        i18n: {
            months:
                [
                    'Январь',
                    'Февраль',
                    'Март',
                    'Апрель',
                    'Май',
                    'Июнь',
                    'Июль',
                    'Август',
                    'Сентябрь',
                    'Октябрь',
                    'Ноябрь',
                    'Декабрь'
                ],

            monthsShort: [
                'Янв',
                'Фев',
                'Мар',
                'Апр',
                'Май',
                'Июн',
                'Июл',
                'Авг',
                'Сен',
                'Окт',
                'Ноя',
                'Дек'
            ],
            weekdays:
                [
                    'Воскресенье',
                    'Понедельник',
                    'Вторник',
                    'Среда',
                    'Четверг',
                    'Пятница',
                    'Суббота'
                ],
            weekdaysShort:
                [
                    'Вс',
                    'Пн',
                    'Вт',
                    'Ср',
                    'Чт',
                    'Пт',
                    'Сб'
                ],
            weekdaysAbbrev: [
                'Вс',
                'Пн',
                'Вт',
                'Ср',
                'Чт',
                'Пт',
                'Сб'
            ],
            cancel: 'Отменить',
            clear: 'Очистить',
            done: 'ОК'
        },
        firstDay: 1,
        format: 'dd/mm/yyyy',
        minDate: new Date(),
        defaultDate: new Date(),
        autoClose: true
    });
    instance.options.onSelect = async (e) => {
       // mainFormElement.classList.add('hide')
        startTimePickerDiv.classList.remove('hide')
        startTimePicker.classList.remove('hide')
        startTimePicker.value = null

        try {
            endDatePickerDiv.classList.add('hide')
            endDatePicker.classList.add('hide')
            endDatePicker.value = null;
        } catch (e) {}
        try {
            endTimePickerDiv.classList.add('hide')
            endTimePicker.classList.add('hide')
            endTimePicker.value = null;
        } catch (e) {}

        console.log(e);
        const day = e.getDate().toString().padStart(2, '0');
        const month = (e.getMonth() + 1).toString().padStart(2, '0');
        const year = e.getFullYear();
        rentStartDateV2 = `${year}-${month}-${day}`;

        const responseData = await fetch('/item/get-default-times', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                'start_date': rentStartDateV2,
            })
        })
            .then(async resp => {
                return await resp.json()
            })
        const availableTimes = responseData.availableTimes
        startTimePicker.innerHTML = '<option value="" disabled selected>Выберите время:</option>';
        availableTimes.forEach(time => {
            startTimePicker.innerHTML += `<option value="${time}" class="black-text">${time}</option>`
        })
        M.FormSelect.init(startTimePicker, {});
        startTimePicker.onchange = async (e) => {
         //   mainFormElement.classList.add('hide')
            endDatePickerDiv.classList.remove('hide')
            endDatePicker.classList.remove('hide')
            endDatePicker.value = null

            try {
                endTimePickerDiv.classList.add('hide')
                endTimePicker.classList.add('hide')
                endTimePicker.value = null;
            } catch (e) {}

            rentStartTimeV2 = e.target.value
            const secondDatepickerInstance = M.Datepicker.init(endDatePicker, {
                i18n: {
                    months:
                        [
                            'Январь',
                            'Февраль',
                            'Март',
                            'Апрель',
                            'Май',
                            'Июнь',
                            'Июль',
                            'Август',
                            'Сентябрь',
                            'Октябрь',
                            'Ноябрь',
                            'Декабрь'
                        ],

                    monthsShort: [
                        'Янв',
                        'Фев',
                        'Мар',
                        'Апр',
                        'Май',
                        'Июн',
                        'Июл',
                        'Авг',
                        'Сен',
                        'Окт',
                        'Ноя',
                        'Дек'
                    ],
                    weekdays:
                        [
                            'Воскресенье',
                            'Понедельник',
                            'Вторник',
                            'Среда',
                            'Четверг',
                            'Пятница',
                            'Суббота'

                        ],
                    weekdaysShort:
                        [
                            'Вс',
                            'Пн',
                            'Вт',
                            'Ср',
                            'Чт',
                            'Пт',
                            'Сб'
                        ],

                    weekdaysAbbrev: [
                        'Вс',
                        'Пн',
                        'Вт',
                        'Ср',
                        'Чт',
                        'Пт',
                        'Сб'
                    ],
                    cancel: 'Отменить',
                    clear: 'Очистить',
                    done: 'ОК'
                },
                firstDay: 1,
                format: 'dd/mm/yyyy',
                minDate: new Date(rentStartDateV2),
                defaultDate: new Date(),
                autoClose: true,
                onSelect: async (e) => {
                //    mainFormElement.classList.add('hide')
                    endTimePickerDiv.classList.remove('hide')
                    endTimePicker.classList.remove('hide')
                    endTimePicker.value = null
                    const day = e.getDate().toString().padStart(2, '0');
                    const month = (e.getMonth() + 1).toString().padStart(2, '0');
                    const year = e.getFullYear();
                    rentEndDateV2 = `${year}-${month}-${day}`;
                    const responseData = await fetch('/item/get-default-times', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            'start_date': rentEndDateV2,
                        })
                    })
                        .then(async resp => {
                            return await resp.json()
                        })
                    const availableTimesV2 = responseData.availableTimes
                    endTimePicker.innerHTML = '<option value="" disabled selected>Выберите время:</option>';
                    availableTimesV2.forEach(time => {
                        endTimePicker.innerHTML += `<option value="${time}" class="black-text">${time}</option>`
                    })
                    M.FormSelect.init(endTimePicker, {});
                    endTimePicker.onchange = async (e) => {
                        try {
                            selectedItems = [];
                            isOrderAvailable = selectedItems.length === availableItemsLength
                            updateButtonState()
                            const additionalsWrapper = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-wrapper');
                            additionalsWrapper.innerHTML = ''
                        } catch (e) {
                        }

                        rentEndTimeV2 = e.target.value
                       // mainFormElement.classList.remove('hide');
                        let itemIdPickers = document.querySelectorAll('.item-id-selector')
                        itemIdPickers.forEach(async item => {
                            if (item.parentNode.parentNode.classList.contains('select-wrapper')){
                                const baseWrapper = item.parentNode.parentNode.parentNode
                                baseWrapper.innerHTML = item.outerHTML;
                                item = baseWrapper.firstChild
                            }
                            item.parentNode.classList.remove('hide')
                            const availableItems = await fetch('item/get-available-items/' + item.parentNode.parentNode.parentNode.dataset.goodId, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({
                                    'start_date': rentStartDateV2,
                                    'start_time': rentStartTimeV2,
                                    'end_date': rentEndDateV2,
                                    'end_time': rentEndTimeV2,
                                })
                            }).then(async resp => {
                                return resp.json()
                            }).then(response => {
                                return response.available_items
                            })
                            if (availableItems.length == 0) {
                                item.innerHTML = '<option value="" disabled selected>На выбранную дату нет свободных вариантов</option>'
                            } else {
                                item.innerHTML = '<option value="" disabled selected>Выберите вариант товара</option>'
                                availableItems.forEach(avItem => {
                                    item.innerHTML += `<option value="${avItem.id}">${avItem.good.name} (${avItem.id})</option>`
                                })
                            }
                            var itemSelect = M.FormSelect.init(item, {})
                            itemSelect.el.onchange = async (e) => {
                                const cost = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodCost

                                var startDate = new Date(rentStartDateV2 + ' ' + rentStartTimeV2);
                                var endDate = new Date(rentEndDateV2 + ' ' + rentEndTimeV2);

                                var differenceMs = Math.abs(endDate.getTime() - startDate.getTime());

                                var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24)) ?? 1;
                                e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.amountOfDays = differenceDays;
                                const discount = +document.querySelector('.client-discount-holder').dataset.discountPercent
                                if (discount) {
                                    changeTotalCost((+cost * differenceDays) / 100 * (100 - discount), 'up');
                                } else {
                                    changeTotalCost(+cost * differenceDays, 'up');
                                }

                                let selectedItemText = e.target.selectedOptions[0].text;
                                const selectedItemId = e.target.value
                                await changeCartKey(e.target.dataset.goodId, selectedItemId, e.target.dataset.oldItemId)
                                await changeFieldsItemId(e.target, e.target.dataset.goodId, selectedItemId)
                                e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodItemId = selectedItemId
                                if (e.target.dataset.oldItemId) {
                                    await enableAllOtherOptions(e.target.dataset.goodId, e.target.dataset.oldItemId);
                                }
                                await disableAllOtherOptions(e.target.dataset.goodId, e.target.value)
                                e.target.dataset.oldItemId = selectedItemId;
                                if (!selectedItems.includes(selectedItemText)) {
                                    let indexArray = 0
                                    let classValue = '';
                                    let classValueItem = '';
                                    for (let i = 0; i < selectedItems.length; i++) {
                                        const index = selectedItems[i].indexOf('|')
                                        const valuee = selectedItems[i].substring(0, index)
                                        if (valuee == e.target.parentNode.parentNode.parentNode.className) {
                                            indexArray = i
                                            classValue = valuee
                                            classValueItem = selectedItems[i].split('|')
                                            classValueItem = classValueItem[1].trim()
                                        }
                                    }
                                    if (removeAfterParenthesis(classValueItem) == removeAfterParenthesis(selectedItemText)) {
                                        selectedItems[indexArray] = e.target.parentNode.parentNode.parentNode.className + '|' + selectedItemText
                                    } else {
                                        selectedItems.push(e.target.parentNode.parentNode.parentNode.className + '|' + selectedItemText);
                                    }
                                }
                                isOrderAvailable = selectedItems.length === availableItemsLength
                                document.querySelector('beginning-date-field-rent-type-all')
                                updateButtonState();

                                try {

                                    const additionalsWrapper = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-wrapper');
                                    additionalsWrapper.innerHTML = ''
                                    const additionalsOuterWrapper = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-outer-wrapper');
                                    e.target.parentNode.parentNode.parentNode.insertAdjacentHTML('afterEnd', loaderElement)
                                    const additionalsResponse = await fetch('/get-available-additions', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken,
                                        },
                                        body: JSON.stringify({
                                            startDate: rentStartDateV2,
                                            startTime: rentStartTimeV2,
                                            endDate: rentEndDateV2,
                                            endTime: rentEndTimeV2,
                                            goodId: e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodId,
                                            cartKey: `${e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodId}pixelrental${selectedItemId}`
                                        }),
                                    })
                                        .then(async resp => {
                                            e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.loader-holder').remove()
                                            return await resp.json()
                                        })
                                        .catch(e => {
                                            console.log(e)
                                        })

                                    additionalsResponse.additionals.forEach(additional => {
                                        if (additional.available) {
                                            additionalsWrapper.innerHTML += `<p>
                                    <label>
                                        <input type="checkbox"
                                               class="orange-text additional-checkbox"
                                               data-cart-key="${e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodId}pixelrental${selectedItemId}"
                                               data-additional-id="${additional.id}"
                                               data-additional-cost="${(additional.good.additional_cost > 0 && additional.good.additional_cost != null) ? additional.good.additional_cost : additional.good.cost}"
                                               />
                                        <span>${additional.good.name_ru} <span
                                            class="white-text">(+ ${(additional.good.additional_cost > 0 && additional.good.additional_cost != null) ? additional.good.additional_cost : additional.good.cost}тг)</span></span>
                                    </label>
                                </p>`
                                        } else {
                                            additionalsWrapper.innerHTML += `<p>
                                    <label>
                                        <input type="checkbox"
                                               class="orange-text additional-checkbox"
                                               disabled
                                               data-cart-key="${e.target.parentNode.parentNode.parentNode.parentNode.dataset.goodId}pixelrental${selectedItemId}"
                                               data-additional-id="${additional.id}"
                                               data-additional-cost="${(additional.good.additional_cost > 0 && additional.good.additional_cost != null) ? additional.good.additional_cost : additional.good.cost}"
                                               />
                                        <span>${additional.good.name_ru} <span
                                            class="white-text">(Недоступно на выбранные даты и время)</span></span>
                                    </label>
                                </p>`
                                        }
                                    })
                                    if (additionalsResponse.additionals.length > 0) {
                                        additionalsOuterWrapper.classList.remove('hide')

                                        additionalsWrapper.querySelectorAll('.additional-checkbox').forEach(el => {
                                            el.onchange = changeCart
                                        })
                                    }
                                } catch (e) {
                                    console.log(e)
                                }
                            }
                        })
                    }
                }
            })
        }
    }
}
let totalCostHolder = document.querySelector('#total-sum-of-items')
let totalCostHolderNumber = document.querySelector('.total-cost-holder')
const changeStateIndividualCost = (action) => {
    if (action === 'add') {
        itemTotalCostTextElement.forEach(item => {
            item.classList.add('hide')
        })
    } else {
        itemTotalCostTextElement.forEach(item => {
            item.classList.remove('hide')
        })
    }
}

const updateButtonState = () => {
    if (isOrderAvailable) {
        modalTriggerButton.disabled = ORDER_AVAILABLE;
        modalTriggerButton.classList.remove('disabled');

        totalCostHolder.classList.remove('hide')
    } else {
        modalTriggerButton.disabled = ORDER_NOT_AVAILABLE;
        modalTriggerButton.classList.add('disabled');
    }
};

function removeAfterParenthesis(inputString) {
    const index = inputString.indexOf('(');
    if (index === -1) {
        // Если символ '(' не найден, вернуть исходную строку
        return inputString;
    }
    // Возвращаем подстроку от начала до первого вхождения '(' (не включая его)
    return inputString.substring(0, index);
}

function removeAfterSquareParenthesis(inputString) {
    const index = inputString.indexOf('(');
    if (index === -1) {
        // Если символ '(' не найден, вернуть исходную строку
        return inputString;
    }
    // Возвращаем подстроку от начала до первого вхождения '(' (не включая его)
    return inputString.substring(0, index);
}

document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.onclick = removeFromCart
})

document.querySelectorAll('.field-label').forEach(el => {
    el.onclick = e => {
        e.target.previousSibling.previousSibling.click()
    }
})

document.querySelectorAll('.start_date').forEach(el => {
})



function fillIndividualRentType() {
    console.log("GGG");
    itemIdPickers.forEach(async item => {
        item.parentNode.insertAdjacentHTML('afterend', loaderElement)
        const availableItems = await fetch('/good/' + item.parentNode.parentNode.parentNode.dataset.goodId + '/get-items', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then(async resp => {
                return await resp.json()
            })
            .then(response => {
                item.parentNode.parentNode.querySelector('.loader-holder').remove()
                item.parentNode.classList.remove('hide')
                return response.available_items;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });

        item.innerHTML = '<option value="" disabled selected>Выберите вариант товара</option>'
        availableItems.forEach(avItem => {
            item.innerHTML += `<option value="${avItem.id}">${avItem.good.name} (${avItem.id})</option>`
        })
        var itemSelect = M.FormSelect.init(item, {})
        itemSelect.el.onchange = async (e) => {
            let selectedItemText = e.target.selectedOptions[0].text;
            const selectedItemId = e.target.value
            await changeCartKey(e.target.dataset.goodId, selectedItemId, e.target.dataset.oldItemId)
            await changeFieldsItemId(e.target, e.target.dataset.goodId, selectedItemId)
            e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodItemId = selectedItemId
            if (e.target.dataset.oldItemId) {
                await enableAllOtherOptions(e.target.dataset.goodId, e.target.dataset.oldItemId);
            }
            await disableAllOtherOptions(e.target.dataset.goodId, e.target.value)
            e.target.dataset.oldItemId = selectedItemId;
            const item = e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.begining-date')
            item.placeholder = 'Дата начала'

            if (rentTimeType === RENT_TIME_TYPE_INDIVIDUAL) {
                e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.begining-date-field').classList.remove('hide')
                item.value = null
                try {
                    const rentStartItem = e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.rent-start-time')
                    rentStartItem.value = null
                    e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.rent-start-time-field').classList.add('hide')
                } catch (e){
                }
                try {
                    const endingDateItem = e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.ending-date')
                    endingDateItem.placeholder = 'Дата конца'
                    endingDateItem.value = null
                    e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.ending-date-field').classList.add('hide')
                } catch (e) {}
                try {
                    const rentEndItem = e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.rent-end-time')
                    rentEndItem.value = null
                    e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.rent-end-time-field').classList.add('hide')
                } catch (e) {}
                try {
                    e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-outer-wrapper').classList.add('hide')
                } catch (e) {}
                var instance = M.Datepicker.init(item, {
                    i18n: {
                        months:
                            [
                                'Январь',
                                'Февраль',
                                'Март',
                                'Апрель',
                                'Май',
                                'Июнь',
                                'Июль',
                                'Август',
                                'Сентябрь',
                                'Октябрь',
                                'Ноябрь',
                                'Декабрь'
                            ],

                        monthsShort: [
                            'Янв',
                            'Фев',
                            'Мар',
                            'Апр',
                            'Май',
                            'Июн',
                            'Июл',
                            'Авг',
                            'Сен',
                            'Окт',
                            'Ноя',
                            'Дек'
                        ],
                        weekdays:
                            [
                                'Воскресенье',
                                'Понедельник',
                                'Вторник',
                                'Среда',
                                'Четверг',
                                'Пятница',
                                'Суббота'
                            ],
                        weekdaysShort:
                            [
                                'Вс',
                                'Пн',
                                'Вт',
                                'Ср',
                                'Чт',
                                'Пт',
                                'Сб'
                            ],
                        weekdaysAbbrev: [
                            'Вс',
                            'Пн',
                            'Вт',
                            'Ср',
                            'Чт',
                            'Пт',
                            'Сб'
                        ],
                        cancel: 'Отменить',
                        clear: 'Очистить',
                        done: 'ОК'
                    },
                    firstDay: 1,
                    format: 'dd/mm/yyyy',
                    minDate: new Date(),
                    defaultDate: new Date(),
                    autoClose: true
                });
                item.parentNode.insertAdjacentHTML('afterend', loaderElement)
                const forbiddenDates = await fetch('/item/' + selectedItemId + '/get-unavailable-dates', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                    .then(async resp => {
                        return await resp.json()
                    })
                    .then(response => {
                        item.parentNode.parentNode.querySelector('.loader-holder').remove()
                        item.parentNode.classList.remove('hide')
                        return response.forbiddenDates;
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                    });
                instance.options.disableDayFn = (date) => {
                    const day = date.getDate().toString().padStart(2, '0');
                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                    const year = date.getFullYear();
                    const dateString = `${year}-${month}-${day}`;
                    return forbiddenDates.includes(dateString);
                }
                instance.options.onSelect = async (e) => {
                    const rentStartItem = instance.el.parentNode.parentNode.querySelector('.rent-start-time')
                    rentStartItem.value = null;
                    instance.el.parentNode.parentNode.querySelector('.rent-start-time-field').classList.remove('hide')
                    try {
                        instance.el.parentNode.parentNode.querySelector('.ending-date-field').classList.add('hide')
                        instance.el.parentNode.parentNode.querySelector('.ending-date').value = null
                    } catch (e) {}
                    try {
                        instance.el.parentNode.parentNode.querySelector('.rent-end-time-field').classList.add('hide')
                        instance.el.parentNode.parentNode.querySelector('.rent-end-time').value = null
                    } catch (e) {}
                    try {
                        instance.el.parentNode.parentNode.querySelector('.additionals-outer-wrapper').classList.add('hide')
                    } catch (e) {}
                    const day = e.getDate().toString().padStart(2, '0');
                    const month = (e.getMonth() + 1).toString().padStart(2, '0');
                    const year = e.getFullYear();
                    const rentStartDate = `${year}-${month}-${day}`;
                    item.parentNode.insertAdjacentHTML('afterend', loaderElement)
                    const selector = instance.el.parentNode.parentNode.querySelector('.rent-start-time')

                    const responseData = await fetch('/item/' + selectedItemId + '/get-available-times', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },

                        body: JSON.stringify({
                            'start_date': rentStartDate
                        })
                    })
                        .then(async resp => {
                            return await resp.json()
                        })
                        .then(response => {
                            item.parentNode.parentNode.querySelector('.loader-holder').remove()
                            selector.parentNode.classList.remove('hide')
                            return response;
                        })

                    const availableTimes = responseData.availableTimes;
                    const nextUnavailableDate = responseData.nextUnavailableDate;
                    selector.innerHTML = '<option value="" disabled selected>Время начала</option>'
                    availableTimes.forEach(time => {
                        selector.innerHTML += `<option value="${time}" class="black-text">${time}</option>`
                    })
                    M.FormSelect.init(selector, {});
                    selector.onchange = async (e) => {
                        e.target.parentNode.parentNode.parentNode.insertAdjacentHTML('afterend', loaderElement)
                        const rentStartTime = e.target.value;
                        const secondDatepicker = e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.ending-date');
                        secondDatepicker.value = null
                        e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.ending-date-field').classList.remove('hide')
                        try {
                            e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.rent-end-time-field').classList.add('hide')
                        } catch (e) {}
                        try {
                            instance.el.parentNode.parentNode.querySelector('.additionals-outer-wrapper').classList.add('hide')
                        } catch (e) {}
                        const responseData = await fetch('/item/' + selectedItemId + '/get-rent-end-dates', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                'start_date': rentStartDate,
                            })
                        })
                            .then(async resp => {
                                return await resp.json()
                            })
                            .then(response => {
                                e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.loader-holder').remove();
                                secondDatepicker.parentNode.classList.remove('hide')
                                return response;
                            })
                        const availableRentEndDates = responseData.availableDates
                        const secondDatepickerInstance = M.Datepicker.init(secondDatepicker, {
                            i18n: {
                                months:
                                    [
                                        'Январь',
                                        'Февраль',
                                        'Март',
                                        'Апрель',
                                        'Май',
                                        'Июнь',
                                        'Июль',
                                        'Август',
                                        'Сентябрь',
                                        'Октябрь',
                                        'Ноябрь',
                                        'Декабрь'
                                    ],

                                monthsShort: [
                                    'Янв',
                                    'Фев',
                                    'Мар',
                                    'Апр',
                                    'Май',
                                    'Июн',
                                    'Июл',
                                    'Авг',
                                    'Сен',
                                    'Окт',
                                    'Ноя',
                                    'Дек'
                                ],
                                weekdays:
                                    [
                                        'Воскресенье',
                                        'Понедельник',
                                        'Вторник',
                                        'Среда',
                                        'Четверг',
                                        'Пятница',
                                        'Суббота'

                                    ],
                                weekdaysShort:
                                    [
                                        'Вс',
                                        'Пн',
                                        'Вт',
                                        'Ср',
                                        'Чт',
                                        'Пт',
                                        'Сб'
                                    ],

                                weekdaysAbbrev: [
                                    'Вс',
                                    'Пн',
                                    'Вт',
                                    'Ср',
                                    'Чт',
                                    'Пт',
                                    'Сб'
                                ],
                                cancel: 'Отменить',
                                clear: 'Очистить',
                                done: 'ОК'
                            },
                            firstDay: 1,
                            format: 'dd/mm/yyyy',
                            minDate: new Date(rentStartDate),
                            defaultDate: new Date(),
                            autoClose: true,
                            disableDayFn: (date => {
                                if (availableRentEndDates.length > 0) {
                                    const day = date.getDate().toString().padStart(2, '0');
                                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                                    const year = date.getFullYear();
                                    const dateString = `${year}-${month}-${day}`;
                                    return !availableRentEndDates.includes(dateString);
                                }
                                return false;
                            }),
                            onSelect: async (e) => {
                                const day = e.getDate().toString().padStart(2, '0');
                                const month = (e.getMonth() + 1).toString().padStart(2, '0');
                                const year = e.getFullYear();
                                const rentEndDate = `${year}-${month}-${day}`;
                                secondDatepickerInstance.el.parentNode.insertAdjacentHTML('afterend', loaderElement)
                                const endTimeSelector = instance.el.parentNode.parentNode.querySelector('.rent-end-time')
                                endTimeSelector.value = null
                                try {
                                    instance.el.parentNode.parentNode.querySelector('.rent-end-time-field').classList.remove('hide')
                                } catch (e) {}
                                try {
                                    instance.el.parentNode.parentNode.querySelector('.additionals-outer-wrapper').classList.add('hide')
                                } catch (e) {}
                                const responseData = await fetch('/item/' + selectedItemId + '/get-next-rent-times', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                    },

                                    body: JSON.stringify({
                                        'finish_date': rentEndDate,
                                        'start_date': rentStartDate,
                                        'start_time': rentStartTime,
                                    })
                                })
                                    .then(async resp => {
                                        return await resp.json()
                                    })
                                    .then(response => {
                                        secondDatepickerInstance.el.parentNode.parentNode.querySelector('.loader-holder').remove()
                                        endTimeSelector.parentNode.classList.remove('hide')
                                        return response;
                                    })
                                const nextAvailableTimes = responseData.nextAvailableTimes;
                                endTimeSelector.innerHTML = '<option value="" disabled selected>Время конца</option>'
                                nextAvailableTimes.forEach(time => {
                                    endTimeSelector.innerHTML += `<option value="${time}" class="black-text">${time}</option>`
                                })
                                M.FormSelect.init(endTimeSelector, {});
                                endTimeSelector.onchange = async (e) => {
                                    try {
                                        const additionalsWrapper = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-wrapper');
                                        additionalsWrapper.innerHTML = ''
                                    } catch (e) {
                                    }

                                    const rentEndTime = e.target.value
                                    var startDate = new Date(rentStartDate + ' ' + rentStartTime);
                                    var endDate = new Date(rentEndDate + ' ' + rentEndTime);

                                    var differenceMs = Math.abs(endDate.getTime() - startDate.getTime());

                                    var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24)) ?? 1;
                                    e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.amountOfDays = differenceDays;

                                    const sumHolder = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.good-cost-holder')

                                    const discount = +document.querySelector('.client-discount-holder').dataset.discountPercent

                                    const cost = +e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodCost

                                    if (discount) {
                                        sumHolder.innerHTML = (+cost * differenceDays) / 100 * (100 - discount);
                                        changeTotalCost((+cost * differenceDays) / 100 * (100 - discount), 'up');
                                    } else {
                                        sumHolder.innerHTML = +cost * differenceDays;
                                        changeTotalCost(+cost * differenceDays, 'up');
                                    }

                                    if (!selectedItems.includes(selectedItemText)) {
                                        let indexArray = 0
                                        let classValue = '';
                                        let classValueItem = '';
                                        for (let i = 0; i < selectedItems.length; i++) {
                                            const index = selectedItems[i].indexOf('|')
                                            const valuee = selectedItems[i].substring(0, index)
                                            if (valuee == e.target.parentNode.parentNode.parentNode.className) {
                                                indexArray = i
                                                classValue = valuee
                                                classValueItem = selectedItems[i].split('|')
                                                classValueItem = classValueItem[1].trim()
                                            }
                                        }
                                        if (removeAfterParenthesis(classValueItem) == removeAfterParenthesis(selectedItemText)) {
                                            selectedItems[indexArray] = e.target.parentNode.parentNode.parentNode.className + '|' + selectedItemText
                                        } else {
                                            selectedItems.push(e.target.parentNode.parentNode.parentNode.className + '|' + selectedItemText);
                                        }
                                    }
                                    isOrderAvailable = selectedItems.length === availableItemsLength
                                    updateButtonState();

                                    try {

                                        const additionalsWrapper = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-wrapper');
                                        const additionalsOuterWrapper = e.target.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('.additionals-outer-wrapper');
                                        e.target.parentNode.parentNode.parentNode.insertAdjacentHTML('afterEnd', loaderElement)
                                        const additionalsResponse = await fetch('/get-available-additions', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken,
                                            },
                                            body: JSON.stringify({
                                                startDate: rentStartDate,
                                                startTime: rentStartTime,
                                                endDate: rentEndDate,
                                                endTime: rentEndTime,
                                                goodId: e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodId,
                                                cartKey: `${e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodId}pixelrental${selectedItemId}`
                                            }),
                                        })
                                            .then(async resp => {
                                                e.target.parentNode.parentNode.parentNode.parentNode.querySelector('.loader-holder').remove()
                                                return await resp.json()
                                            })
                                            .catch(e => {
                                                console.log(e)
                                            })

                                        additionalsResponse.additionals.forEach(additional => {
                                            if (additional.available) {
                                                additionalsWrapper.innerHTML += `<p>
                                    <label>
                                        <input type="checkbox"
                                               class="orange-text additional-checkbox"
                                               data-cart-key="${e.target.parentNode.parentNode.parentNode.parentNode.parentNode.dataset.goodId}pixelrental${selectedItemId}"
                                               data-additional-id="${additional.id}"
                                               data-additional-cost="${(additional.good.additional_cost > 0 && additional.good.additional_cost != null) ? additional.good.additional_cost : additional.good.cost}"
                                               />
                                        <span>${additional.good.name_ru} <span
                                            class="white-text">(+ ${(additional.good.additional_cost > 0 && additional.good.additional_cost != null) ? additional.good.additional_cost : additional.good.cost}тг)</span></span>
                                    </label>
                                </p>`
                                            } else {
                                                additionalsWrapper.innerHTML += `<p>
                                    <label>
                                        <input type="checkbox"
                                               class="orange-text additional-checkbox"
                                               disabled
                                               data-cart-key="${e.target.parentNode.parentNode.parentNode.parentNode.dataset.goodId}pixelrental${selectedItemId}"
                                               data-additional-id="${additional.id}"
                                               data-additional-cost="${(additional.good.additional_cost > 0 && additional.good.additional_cost != null) ? additional.good.additional_cost : additional.good.cost}"
                                               />
                                        <span>${additional.good.name_ru} <span
                                            class="white-text">(Недоступно на выбранные даты и время)</span></span>
                                    </label>
                                </p>`
                                            }
                                        })
                                        if (additionalsResponse.additionals.length > 0) {
                                            additionalsOuterWrapper.classList.remove('hide')

                                            additionalsWrapper.querySelectorAll('.additional-checkbox').forEach(el => {
                                                el.onchange = changeCart
                                            })
                                        }
                                    } catch (e) {
                                        console.log(e)
                                    }
                                }
                            }
                        });
                    }
                }
            }
        }
    })
}



function placeOrder(e) {
    const errorTextHolder = document.querySelector('.error-text')
    errorTextHolder.innerHTML = '';
    var flag = true;
    const formElement = (rentTimeType === RENT_TIME_TYPE_INDIVIDUAL)
        ? document.querySelector('#order-placement-form')
        : document.querySelector('#order-placement-form-rent-type-all')

    formElement.querySelectorAll('[required]').forEach(field => {
        if (!field.value) {
            flag = false;
            return null;
        }
    })
    if (flag) {
        document.querySelector((rentTimeType === RENT_TIME_TYPE_INDIVIDUAL) ? '#order-placement-form' : '#order-placement-form-rent-type-all')
            .submit()
        e.target.onclick = () => {
        }
    } else {
        errorTextHolder.innerHTML = 'Не все даты и время заполнены!'
    }
}

function disableAllOtherOptions(goodId, itemId) {
    var selects = document.querySelectorAll(`select[data-good-id="${goodId}"]`);
    selects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === itemId) {
                option.disabled = true;
            }
        })
        M.FormSelect.init(select, {})
    })
}

function enableAllOtherOptions(goodId, itemId) {
    var selects = document.querySelectorAll(`select[data-good-id="${goodId}"]`);
    selects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === itemId) {
                option.disabled = false;
            }
        })
        M.FormSelect.init(select, {})
    })
}

function changeFieldsItemId(node, goodId, itemId){
    node.parentNode.parentNode.parentNode.parentNode.querySelectorAll('.input-field').forEach(el => {
        const select = el.querySelector('select')
        if (select) {
            const nameSplitted = select.name.split('[');
            select.name = `${goodId}pixelrental${itemId}[${nameSplitted[1]}`
        }
        const dateInput = el.querySelector('input.datepicker')
        if (dateInput) {
            const nameSplitted = dateInput.name.split('[');
            dateInput.name = `${goodId}pixelrental${itemId}[${nameSplitted[1]}`
        }
    })
}

async function changeCartKey(goodId, itemId, oldItemId){
    fetch('/change-cart-key', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            key_to_remove: `${goodId}pixelrental${oldItemId}`,
            key_to_set: `${goodId}pixelrental${itemId}`
        }),
    })
}


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".quantity-btn").forEach(button => {
        button.addEventListener("click", function () {
            const productId = this.dataset.productId;
            const action = this.classList.contains("plus") ? "increase" : "decrease";

            fetch(`/cart/update-quantity`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ product_id: productId, action: action }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`quantity-${productId}`).innerText = data.quantity;
                    }
                })
                .catch(error => console.error("Error:", error));
        });
    });
});


