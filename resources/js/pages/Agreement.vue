<template>
    <div class="pdf-page">
        <!-- PDF -->
        <!--        <iframe-->
        <!--            :src="url"-->
        <!--            class="frame-agreement"-->
        <!--            style="border: none;"-->
        <!--        ></iframe>-->

        <object :data="url" type="application/pdf" width="100%" height="200%">
            <p>
                Ваш браузер не поддерживает PDF. <a :href="url">Скачать PDF</a>
            </p>
        </object>

        <!--        <PdfViewer v-if="url" :pdf-url="url" />-->

        <!-- Кнопка -->
        <div class="actions" style="margin-top: 20px; text-align: center">
            <a
                v-if="state === 1"
                class="auth-main-btn mb-20"
                style="
                    background: #ff962e;
                    color: #fff;
                    width: 200px;
                    margin: 0 auto;
                    display: block;
                    text-decoration: none;
                    padding-top: 20px;
                "
                href="/admin/orders"
                >Список заказов</a
            >
            <button
                v-if="state === 0"
                @click="signPdf"
                class="auth-main-btn mb-20"
                style="
                    background: #ff962e;
                    color: #fff;
                    width: 200px;
                    margin: 0 auto;
                "
            >
                Подписать
            </button>
        </div>
    </div>
</template>

<script>
// :src="'/pdfjs/web/viewer.html?file=' + url +'#zoom=page-width'"
import axios from "axios";
import PdfViewer from "./PdfViewer.vue";

export default {
    name: "PdfPage",
    components: { PdfViewer },
    data() {
        return {
            url: "",
            state: 0,
        };
    },
    mounted() {
        this.getAgreement();
        const params = new URLSearchParams(window.location.search);
        if (params.get("state")) {
            this.state = 1;
        }
    },
    methods: {
        signPdf() {
            const params = new URLSearchParams(window.location.search);
            const orderId = params.get("id");
            window.location.href = "/order/signature?id=" + orderId;
        },
        async getAgreement() {
            const params = new URLSearchParams(window.location.search);
            const orderId = params.get("id");
            const response = await axios.request({
                method: "GET",
                url: "/api/order/agreement/" + orderId,
            });
            this.url = response.data.attachment.url;
        },
    },
};
</script>

<style>
.pdf-page {
    height: 100vh;
}
.btn {
    padding: 12px 20px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}
.btn:hover {
    background: #2563eb;
}
.frame-agreement {
    width: 100%;
    height: 1000px;
}
@media (max-width: 1024px) {
    .frame-agreement {
        width: 100%;
        height: 100vh;
    }
}

@media (max-width: 768px) {
    .frame-agreement {
        height: 100dvh;
    }
}
</style>
