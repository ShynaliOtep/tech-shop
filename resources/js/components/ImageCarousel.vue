<template>
    <div class="swiper-container">
        <br>
        <swiper
            :slides-per-view="1.5"
            :space-between="12"
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
                    style="object-fit: cover;"
                />
                <div class="carousel_block">
                    <h4>
                        {{image.title}}
                    </h4>
                    <p>{{image.short_text}}</p>
                    <button class="button-carousel"  @click="openModal(image)">
                        <span class="circle">
                            <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                        <span class="text">Learn more</span>
                    </button>
                </div>
            </swiper-slide>
        </swiper>

        <!-- Модальное окно -->
        <div v-if="selectedImage" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <img :src="selectedImage.image" :alt="selectedImage.image" class="modal-image" />
                <p class="modal-text" v-html="selectedImage.text"></p>
                <button class="close-button orange" @click="closeModal">Закрыть</button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

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

        return { images, selectedImage, openModal, closeModal };
    }
};
</script>


<style>
.swiper-container {
    width: 100%;
    max-width: 1200px;
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
    opacity: 0;
    transition: opacity 0.3s;
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
