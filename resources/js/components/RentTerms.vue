<template>
    <section v-if="info" class="rent-terms" :aria-label="info.labels.title">
        <h3 class="rent-terms-title">{{ info.labels.title }}</h3>
        <ul class="rent-terms-list">
            <li v-for="term in info.terms" :key="term">{{ term }}</li>
        </ul>

        <div class="rent-terms-pickup">
            <p class="rent-terms-label">{{ info.labels.pickup }}</p>
            <a
                :href="info.pickup.mapUrl"
                target="_blank"
                rel="noopener"
                class="rent-terms-address"
            >
                {{ info.pickup.address }}
            </a>
            <p class="rent-terms-hours">{{ info.pickup.hours }}</p>
            <p class="rent-terms-hours">{{ info.labels.delivery }}</p>
        </div>

        <a
            :href="info.whatsappUrl"
            target="_blank"
            rel="noopener"
            class="rent-terms-whatsapp"
        >
            {{ info.labels.whatsapp }}
        </a>
    </section>
</template>

<script>
export default {
    name: "RentTerms",
    data() {
        // Заполняется в _v2.layouts.base из config/pixel.php и переводов.
        return { info: window.PIXEL_INFO || null };
    },
};
</script>

<style scoped>
.rent-terms {
    background: #191919;
    border-radius: 20px;
    padding: 20px;
    color: #ffffff;
}
.rent-terms-title {
    font-size: 18px;
    font-weight: 500;
    margin-bottom: 12px;
}
.rent-terms-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}
.rent-terms-list li {
    position: relative;
    padding-left: 20px;
    font-size: 14px;
    line-height: 1.4;
    color: #c8c8c8;
}
.rent-terms-list li::before {
    content: "";
    position: absolute;
    left: 2px;
    top: 7px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #ff962e;
}
.rent-terms-pickup {
    border-top: 1px solid #2a2a2a;
    padding-top: 14px;
    margin-bottom: 16px;
}
.rent-terms-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #8a8a8a;
    margin-bottom: 6px;
}
.rent-terms-address {
    display: inline-block;
    color: #ff962e;
    font-size: 16px;
    text-decoration: none;
    margin-bottom: 4px;
}
.rent-terms-address:hover {
    text-decoration: underline;
}
.rent-terms-hours {
    font-size: 14px;
    color: #c8c8c8;
}
.rent-terms-whatsapp {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 48px;
    border-radius: 15px;
    border: 1px solid #2e2e2e;
    color: #ffffff;
    font-size: 15px;
    text-decoration: none;
}
.rent-terms-whatsapp:hover {
    border-color: #25d366;
}
.rent-terms-address:focus-visible,
.rent-terms-whatsapp:focus-visible {
    outline: 2px solid #ff962e;
    outline-offset: 2px;
}
</style>
