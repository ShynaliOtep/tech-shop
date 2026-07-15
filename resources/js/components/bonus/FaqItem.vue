<template>
    <div class="faq-item">
        <div class="faq-question" :class="{ open: isOpen }" @click="toggle">
            {{ question }}
            <span class="arrow" :class="{ open: isOpen }">
                <svg
                    width="24"
                    height="15"
                    viewBox="0 0 24 15"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M24 0L14.148 15L9.73747 15L0 -1.09278e-06L4.4105 -8.91963e-07L11.9141 11.9193L19.5895 -2.00822e-07L24 0Z"
                        fill="currentColor"
                    />
                </svg>
            </span>
        </div>
        <transition name="faq">
            <div class="faq-answer" v-if="isOpen" v-html="answer"></div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    question: String,
    answer: String,
});

const isOpen = ref(false);

function toggle() {
    isOpen.value = !isOpen.value;
}
</script>
