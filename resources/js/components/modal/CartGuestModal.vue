<template>
    <div :id="modalClass" class="modal bottom-sheet custom-modal">
        <div class="modal-content container center">
            <h4>{{ title }}</h4>
            <p>{{ subTitle }}</p>
            <p>
                <a :href="link">{{ linkCaption }}</a>
            </p>
            <p>{{ downTitle }}</p>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <div class="row center">
                <p><b class="error-text red-text"></b></p>
                <button class="btn-large nav-link orange darken-4 auth-link confirm-order-btn" @click="btnAction">
                    {{ btnCaption }}
                    <i class="material-icons">done</i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            modalClass: String,
            title: $t('translations.Are you sure you are ready to place an order?'),
            subTitle:  $t('translations.After confirming your order, your order will be transferred to the manager and you will be able to receive your goods at the pick-up point at') + ":",
            link: 'https://2gis.kz/almaty/firm/70000001069136996',
            linkCaption: $t('translations.Tole BI street, 176') + '.',
            downTitle: $t('translations.The manager will double-check your contact details and photographs of your ID, after which an equipment rental agreement will be signed') + '.',
            btnCaption: $t('translations.Place order'),
            btnAction: Function
        }
    }
};
</script>

<style>
.modal {
    display: none;
}
</style>
