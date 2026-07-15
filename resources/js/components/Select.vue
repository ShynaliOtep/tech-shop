<template>
    <div
        class="custom-select"
        @click="toggleDropdown"
        ref="selectWrapper"
        tabindex="0"
        @blur="closeDropdown"
    >
        <div class="select-selected">
            <span>{{
                selectedOption ? selectedOption : "Выберите время"
            }}</span>
            <span class="arrow">
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 20 20"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M18.1818 10C18.1818 5.48131 14.5187 1.81818 10 1.81818C5.48131 1.81818 1.81818 5.48131 1.81818 10C1.81818 14.5187 5.48131 18.1818 10 18.1818C14.5187 18.1818 18.1818 14.5187 18.1818 10ZM9.09091 4.54545C9.09091 4.04338 9.49792 3.63636 10 3.63636C10.5021 3.63636 10.9091 4.04338 10.9091 4.54545V9.43803L14.043 11.005L14.1238 11.0511C14.513 11.2978 14.6601 11.8038 14.4496 12.2248C14.2391 12.6458 13.7458 12.8321 13.315 12.6687L13.2298 12.6314L9.5934 10.8132C9.28541 10.6592 9.09091 10.3443 9.09091 10V4.54545ZM20 10C20 15.5228 15.5228 20 10 20C4.47715 20 0 15.5228 0 10C0 4.47715 4.47715 0 10 0C15.5228 0 20 4.47715 20 10Z"
                        fill="#404040"
                    />
                </svg>
            </span>
        </div>
        <ul v-if="isOpen" class="select-options">
            <li
                v-for="option in options"
                :key="option"
                :class="{ selected: option === selectedValue }"
                @click.stop="selectOption(option)"
            >
                {{ option }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Select",
    props: {
        modelValue: [String, Number, null], // для v-model
        options: {
            type: Array,
            required: true,
            // формат: [{ value: 'val1', label: 'Label 1' }, ...]
        },
    },
    data() {
        return {
            isOpen: false,
            selectedValue: this.modelValue,
        };
    },
    computed: {
        selectedOption() {
            const selected = this.options.find((opt) =>
                typeof opt === "object"
                    ? opt.value === this.selectedValue
                    : opt === this.selectedValue,
            );
            return typeof selected === "object"
                ? selected.label
                : selected || null;
        },
    },
    watch: {
        modelValue(newVal) {
            this.selectedValue = newVal;
        },
        selectedValue(newVal) {
            this.$emit("update:modelValue", newVal);
        },
    },
    methods: {
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        selectOption(option) {
            this.selectedValue =
                typeof option === "object" ? option.value : option;
            this.$emit("update:modelValue", this.selectedValue);
            this.$emit("change", this.selectedValue);
            this.isOpen = false;
        },
        closeDropdown() {
            this.isOpen = false;
        },
    },
};
</script>

<style scoped>
.custom-select {
    width: 100%;
    position: relative;
    user-select: none;
    border: 1px solid #404040;
    border-radius: 15px;
    background-color: #191919;
    cursor: pointer;
    outline: none;
    height: 60px;
}
.select-selected {
    padding: 10px 14px;
    font-size: 16px;
    font-weight: 400;
    color: #404040;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
}

.select-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #191919;
    border: 1px solid #404040;
    border-top: none;
    margin-top: 2px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 10;
    border-radius: 15px;
    transition: opacity 0.2s ease;
}
.select-options li {
    padding: 10px 14px;
    font-size: 16px;
    color: #333;
}
.select-options li:hover {
    background-color: #191919;
}
.select-options li.selected {
    background-color: #191919;
    font-weight: 600;
}
</style>
