<template>
    <div class="datepicker-wrapper" @click="focusInput">
        <div class="datepicker-input">
            <input
                ref="dateInput"
                type="date"
                v-model="selectedDate"
                :min="minDate"
                @change="validateDate"
                class="custom-date picker"
                @click.stop
            />
            <svg class="calendar-icon" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.3636 9.09091H1.81818V17.2727C1.81818 17.7748 2.2252 18.1818 2.72727 18.1818H15.4545C15.9566 18.1818 16.3636 17.7748 16.3636 17.2727V9.09091ZM11.8182 4.54545V3.63636H6.36364V4.54545C6.36364 5.04753 5.95662 5.45455 5.45455 5.45455C4.95247 5.45455 4.54545 5.04753 4.54545 4.54545V3.63636H2.72727C2.2252 3.63636 1.81818 4.04338 1.81818 4.54545V7.27273H16.3636V4.54545C16.3636 4.04338 15.9566 3.63636 15.4545 3.63636H13.6364V4.54545C13.6364 5.04753 13.2294 5.45455 12.7273 5.45455C12.2252 5.45455 11.8182 5.04753 11.8182 4.54545ZM18.1818 17.2727C18.1818 18.779 16.9608 20 15.4545 20H2.72727C1.22104 20 0 18.779 0 17.2727V4.54545C0 3.03922 1.22104 1.81818 2.72727 1.81818H4.54545V0.909091C4.54545 0.407014 4.95247 0 5.45455 0C5.95662 0 6.36364 0.407014 6.36364 0.909091V1.81818H11.8182V0.909091C11.8182 0.407014 12.2252 0 12.7273 0C13.2294 0 13.6364 0.407014 13.6364 0.909091V1.81818H15.4545C16.9608 1.81818 18.1818 3.03922 18.1818 4.54545V17.2727Z" fill="#404040"/>
            </svg>
            <div
                v-if="error"
                class="tooltip-error"
            >
                 Дата должна быть не раньше {{ minDate }}
                <span class="tooltip-arrow"></span>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    name: "CustomDatePicker",
    props: {
        modelValue: String,
        minDate: String // 👈 минимальная дата как пропс
    },
    data() {
        return {
            selectedDate: this.modelValue || null,
            error: false
        };
    },
    watch: {
        modelValue(val) {
            this.selectedDate = val;
        },
        selectedDate(val) {
            this.$emit("update:modelValue", val);
        }
    },
    methods: {
        focusInput() {
            if (this.$refs.dateInput) {
                this.$refs.dateInput.focus();
            }
        },
        validateDate(e) {
            console.log('eee')
            const value = e.target.value

            if (value < this.minDate) {
                this.error = true
                this.selectedDate = "" // сбросить дату
            } else {
                this.error = false
            }
        }
    },
};
</script>

<style scoped>
/* Флажка */
.tooltip-error {
    position: absolute;
    top: 110%;
    left: 0;
    background: #f44336;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    animation: fadeIn 0.2s ease-in-out;
}

.tooltip-arrow {
    position: absolute;
    top: -5px;
    left: 10px;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 5px solid #f44336;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
.datepicker-wrapper {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-family: Arial, sans-serif;
    user-select: none;
}

.label {
    font-weight: 600;
    color: #333;
}

.datepicker-input {
    position: relative;
    cursor: pointer;
}

.custom-date {
    width: 100%;
    padding: 10px 40px 10px 14px;
    font-size: 16px;
    border: 1px solid #404040;
    border-radius: 15px;
    background-color: #191919;
    color: #404040;
    height: 60px;
    cursor: pointer;
    -webkit-appearance: none;
    appearance: none;
}

.custom-date::-webkit-calendar-picker-indicator {
    opacity: 0;
    cursor: pointer;
}

.calendar-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    color: #ff962e;
    /* Теперь pointer-events разрешены */
}
input.picker[type="date"] {
    position: relative;
}

input.picker[type="date"]::-webkit-calendar-picker-indicator {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    padding: 0;
    color: transparent;
    background: transparent;
}
</style>
