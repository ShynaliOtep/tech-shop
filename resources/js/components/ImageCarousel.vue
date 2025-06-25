<template>
    <div class="swiper-container">
        <br>
        <swiper
            :modules="[Navigation, Autoplay]"
            :slides-per-view="1"
            :space-between="0"
            :navigation="true"
            :loop="true"
            :autoplay="{ delay: 3000 }"
            class="articles-slider"
        >
            <swiper-slide v-for="(image, index) in images" :key="index" class="articles-slider-slide">
                <img
                    class="image"
                    :src="image.image"
                    :alt="image.image"
                    draggable="false"
                    @click="openModal(image)"
                    style="object-fit: cover;"
                />
<!--                <div class="carousel_block">-->
<!--                    <h4>-->
<!--                        {{image.title}}-->
<!--                    </h4>-->
<!--                    <p>{{image.short_text}}</p>-->
<!--                    <button class="button-carousel"  @click="openModal(image)">-->
<!--                        <span class="circle">-->
<!--                            <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">-->
<!--                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />-->
<!--                            </svg>-->
<!--                        </span>-->
<!--                        <span class="text">Learn more</span>-->
<!--                    </button>-->
<!--                </div>-->
            </swiper-slide>
        </swiper>

<!--        <div v-if="modal.open && !isAuthenticated" id="modal" class="modal">-->
<!--            <div class="black-block simple-centred-block modal-block ">-->
<!--                <span class="close" onclick="closeModal()">-->
<!--                   <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                    <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>-->
<!--                    <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>-->
<!--                    </svg>-->
<!--                </span>-->
<!--                <p class="modal-title big-white-title mb-20">-->
<!--                    Войдите в аккаунт-->
<!--                </p>-->
<!--                <p class="grey-s-light-text mb-20">-->
<!--                    Для того, чтобы создать заявку на аренду – Войдите или Создайте аккаунт-->
<!--                </p>-->
<!--                <a href="/auth/login" class="orange-btn mb-20">Войти</a>-->
<!--                <a href="/auth/register" class="black-btn">Создать аккаунт</a>-->
<!--            </div>-->
<!--        </div>-->

        <!-- Модальное окно -->
        <div v-if="selectedImage" class="modal" id="modal" @click="closeModal" style="display: block">
            <div class="black-block simple-centred-block modal-block" @click.stop>
                <img :src="selectedImage.image" :alt="selectedImage.image" class="modal-image" />
                <p class="modal-title big-white-title mb-20">{{selectedImage.title}}</p>
                <p class="grey-s-light-text mb-20" v-html="selectedImage.text"></p>
                <button class="orange-btn" @click="closeModal">Закрыть</button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Autoplay } from 'swiper/modules';
import "swiper/css";
import "swiper/css/navigation";

export default {
    components: { Swiper, SwiperSlide },
    setup() {
        const images = ref([]);
        const selectedImage = ref(null);

        const fetchImages = async () => {
            try {
                const response = await axios.get("/api/carousels");
                console.log(response.data.data)
                images.value = response.data.data;
            } catch (error) {
                console.error("Ошибка загрузки изображений:", error);
            }
        };

        onMounted(fetchImages);

        const openModal = (image) => {
            selectedImage.value = image;
        };

        const closeModal = () => {
            selectedImage.value = null;
        };

        return { images, selectedImage, openModal, closeModal, Navigation, Autoplay };
    }
};
</script>


<style>
.swiper-container {
    width: 100%;
    margin: auto;
}
.articles-slider-slide {
    width: 400px;
    cursor: pointer;
    overflow: hidden;
}
.image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 10px;
    transition: transform 0.3s ease-in-out;
}
.image:hover {
    transform: scale(1.05);
}

/* Модальное окно */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
.modal-image {
    width: 100%;
    max-width: 1000px;
    border-radius: 10px;
}
.modal-text {
    margin-top: 10px;
}
.close-button {
    margin-top: 10px;
    padding: 10px;
    border: none;
    background: red;
    color: white;
    cursor: pointer;
}
.swiper-button-prev,
.swiper-button-next {
    color: white;
    opacity: 1 !important;
    transition: none;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
    color: #f97316;
}

.carousel_block {
    position: absolute;
    top: 60%;
    left: 15px;
    color: #ffffff;
}
 .button-carousel {
     display: flex;
     align-items: center;
     gap: 12px;
     background: transparent;
     color: white;
     font-weight: 600;
     cursor: pointer;
     border: none;
     outline: none;
 }

.button-carousel .circle {
    width: 20px;
    height: 20px;
    border: 2px solid white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    background-color: #ffffff;
}

.button-carousel .circle svg {
    width: 10px;
    height: 10px;
    stroke: #3b82f6;
}

.button-carousel span.text {
    transition: all 0.3s ease;
}

.button-carousel:hover .circle {
    transform: scale(1.1);
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

.button-carousel:hover .text {
    text-decoration: underline;
    opacity: 0.8;
}

</style>
