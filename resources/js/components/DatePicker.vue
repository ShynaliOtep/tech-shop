<template>
    <DatePicker
        v-model="selectedDate"
        :language="ru"
        input-class="custom-input"
        placeholder="Выберите дату"
    >
        <template #calendar-icon>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
            >
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
        </template>
    </DatePicker>
    <p>Выбранная дата: {{ selectedDate }}</p>
</template>

<script>
import DatePicker from "vue-datepicker-next";
import ru from "vue-datepicker-next/locale/ru";

export default {
    components: { DatePicker },
    data() {
        return {
            selectedDate: null,
            ru,
        };
    },
};
</script>

<style>
.custom-input {
    font-size: 16px;
    font-weight: 400;
    color: var(--text-muted, #8f8f8f);
    background-color: #191919;
    border: 1px solid var(--line-input, #4a4a4a);
    padding: 8px 12px;
    border-radius: 15px;
    width: 100%;
    height: 60px;
    cursor: pointer;
}
.mx-icon-calendar {
    color: var(--text-muted, #8f8f8f) !important;
}
</style>
