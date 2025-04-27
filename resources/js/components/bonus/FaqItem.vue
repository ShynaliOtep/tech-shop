<template>
    <div class="faq-item">
        <div class="faq-question" @click="toggle">
            {{ question }}
            <span class="arrow" :class="{ open: isOpen }">▼</span>
        </div>
        <transition name="faq">
            <div class="faq-answer" v-if="isOpen">
                {{ answer }}
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    question: String,
    answer: String
})

const isOpen = ref(false)

function toggle() {
    isOpen.value = !isOpen.value
}
</script>

<style scoped>
.faq-item {
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.faq-question {
    font-weight: bold;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #E37509;
}

.arrow {
    transition: transform 0.3s ease;
}
.arrow.open {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 8px 0;
    color: #E37509;
}

/* Анимация */
.faq-enter-active, .faq-leave-active {
    transition: all 0.3s ease;
}
.faq-enter-from, .faq-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
