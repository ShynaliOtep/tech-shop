<template>
    <div class="signature-container">
        <canvas
            ref="canvas"
            width="400"
            height="200"
            style="border:1px solid #000; border-radius: 6px;">
        </canvas>

        <div class="buttons">
            <button class="auth-main-btn mb-20" @click="clearSignature">🧹 Очистить</button>
            <button  class="auth-main-btn mb-20" style="background: #FF962E; color: #fff" @click="saveSignature">💾 Сохранить</button>
        </div>
    </div>
</template>

<script>
import SignaturePad from "signature_pad";
import axios from "axios";

export default {
    name: "Signature",
    data() {
        return {
            signaturePad: null,
        };
    },
    mounted() {
        const canvas = this.$refs.canvas;
        this.signaturePad = new SignaturePad(canvas, {
            backgroundColor: "rgb(255,255,255)", // белый фон, чтобы PNG был чистым
            penColor: "rgb(65,105,225)",              // цвет ручки (чёрный)
        });
    },
    methods: {
        clearSignature() {
            this.signaturePad.clear();
        },
        async saveSignature() {
            if (this.signaturePad.isEmpty()) {
                alert("Подпись пуста!");
                return;
            }

            const dataUrl = this.signaturePad.toDataURL("image/png");
            const params = new URLSearchParams(window.location.search)
            const orderId = params.get('id')
            const response = await axios.request({
                method: 'POST',
                url: '/api/order/signature',
                data: {
                    signature: dataUrl,
                    order_id: orderId
                }
            })

            if (response.status === 200) {
                window.location.href = '/order/agreement?id=' + orderId + '&state=1'
            }

            // // Отправляем на Laravel backend
            // fetch("/api/order/signature", {
            //     method: "POST",
            //     headers: {
            //         "Content-Type": "application/json",
            //     },
            //     data: { signature: dataUrl }
            // })
            //     .then(res => res.json())
            //     .then(data => {
            //         alert("Подпись сохранена: " + data.path);
            //     });
        },
    },
};
</script>

<style scoped>
.signature-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.buttons {
    display: flex;
    gap: 10px;
}
button {
    padding: 8px 16px;
    cursor: pointer;
}
</style>
