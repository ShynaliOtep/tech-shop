<template>
    <div class="rpp">
        <div class="rpp-presets" role="group" aria-label="Быстрый выбор срока">
            <button
                v-for="preset in presets"
                :key="preset.key"
                type="button"
                class="rpp-chip"
                :class="{ 'rpp-chip-active': activePreset === preset.key }"
                @click="applyPreset(preset.key)"
            >
                {{ preset.label }}
            </button>
        </div>

        <div class="rpp-calendar">
            <div class="rpp-calendar-head">
                <button
                    type="button"
                    class="rpp-nav"
                    :disabled="!canGoBack"
                    aria-label="Предыдущий месяц"
                    @click="shiftMonth(-1)"
                >
                    ‹
                </button>
                <span class="rpp-month">{{ monthTitle }}</span>
                <button
                    type="button"
                    class="rpp-nav"
                    aria-label="Следующий месяц"
                    @click="shiftMonth(1)"
                >
                    ›
                </button>
            </div>
            <div class="rpp-weekdays">
                <span v-for="day in weekdays" :key="day">{{ day }}</span>
            </div>
            <div class="rpp-grid">
                <span
                    v-for="n in leadingBlanks"
                    :key="'b' + n"
                    class="rpp-blank"
                ></span>
                <button
                    v-for="day in monthDays"
                    :key="day.ymd"
                    type="button"
                    class="rpp-day"
                    :class="dayClasses(day)"
                    :disabled="day.past"
                    :title="day.busy ? busyTitle : ''"
                    :aria-label="dayAriaLabel(day)"
                    :aria-pressed="isEdge(day.ymd)"
                    @click="pickDay(day.ymd)"
                >
                    {{ day.number }}
                </button>
            </div>
            <p v-if="busyDays.length" class="rpp-legend">
                <span class="rpp-legend-mark"></span>
                {{ busyTitle }}
            </p>
        </div>

        <p class="rpp-hint">{{ hint }}</p>

        <div class="rpp-times">
            <label class="rpp-time">
                <span class="rpp-time-label">Выдача</span>
                <select
                    v-model="local.timeStart"
                    :disabled="!local.dateStart"
                    @change="emitChange"
                >
                    <option v-for="time in times" :key="time" :value="time">
                        {{ time }}
                    </option>
                </select>
            </label>
            <label class="rpp-time">
                <span class="rpp-time-label">Возврат</span>
                <select
                    v-model="local.timeEnd"
                    :disabled="!local.dateEnd"
                    @change="emitChange"
                >
                    <option v-for="time in times" :key="time" :value="time">
                        {{ time }}
                    </option>
                </select>
            </label>
        </div>

        <p v-if="complete && !valid" class="rpp-error" role="alert">
            Время возврата должно быть позже выдачи
        </p>
        <p v-else-if="complete" class="rpp-summary">
            {{ summary }}
        </p>
    </div>
</template>

<script>
import {
    addDays,
    copyPeriod,
    defaultStartTime,
    formatDayShort,
    isPeriodComplete,
    isPeriodValid,
    parseYmd,
    pluralDays,
    rentDays,
    timeOptions,
    todayLocal,
    toYmd,
} from "../utils/rent.js";

const MONTHS = [
    "Январь",
    "Февраль",
    "Март",
    "Апрель",
    "Май",
    "Июнь",
    "Июль",
    "Август",
    "Сентябрь",
    "Октябрь",
    "Ноябрь",
    "Декабрь",
];

export default {
    name: "RentPeriodPicker",
    props: {
        modelValue: { type: Object, default: null },
        busyDays: { type: Array, default: () => [] },
    },
    emits: ["update:modelValue"],
    data() {
        const local = copyPeriod(this.modelValue);
        const anchor = parseYmd(local.dateStart || todayLocal());
        return {
            local,
            viewYear: anchor.getFullYear(),
            viewMonth: anchor.getMonth(),
            today: todayLocal(),
            times: timeOptions(),
            weekdays: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
            presets: [
                { key: "day", label: "Сутки" },
                { key: "weekend", label: "Выходные" },
                { key: "week", label: "Неделя" },
            ],
            activePreset: null,
            busyTitle: "Все экземпляры заняты — уточните время",
        };
    },
    computed: {
        monthTitle() {
            return `${MONTHS[this.viewMonth]} ${this.viewYear}`;
        },
        canGoBack() {
            const now = parseYmd(this.today);
            return (
                this.viewYear > now.getFullYear() ||
                (this.viewYear === now.getFullYear() &&
                    this.viewMonth > now.getMonth())
            );
        },
        leadingBlanks() {
            // Неделя начинается с понедельника.
            const first = new Date(this.viewYear, this.viewMonth, 1).getDay();
            return (first + 6) % 7;
        },
        monthDays() {
            const count = new Date(
                this.viewYear,
                this.viewMonth + 1,
                0,
            ).getDate();
            const busy = new Set(this.busyDays);
            const days = [];
            for (let d = 1; d <= count; d++) {
                const ymd = toYmd(new Date(this.viewYear, this.viewMonth, d));
                days.push({
                    ymd,
                    number: d,
                    past: ymd < this.today,
                    busy: busy.has(ymd),
                });
            }
            return days;
        },
        complete() {
            return isPeriodComplete(this.local);
        },
        valid() {
            return isPeriodValid(this.local);
        },
        hint() {
            if (!this.local.dateStart) return "Выберите день выдачи";
            if (!this.local.dateEnd) return "Теперь выберите день возврата";
            return "Можно изменить даты или время";
        },
        summary() {
            const p = this.local;
            return `${formatDayShort(p.dateStart)}, ${p.timeStart} → ${formatDayShort(p.dateEnd)}, ${p.timeEnd} · ${pluralDays(rentDays(p))}`;
        },
    },
    watch: {
        modelValue: {
            deep: true,
            handler(value) {
                const next = copyPeriod(value);
                if (JSON.stringify(next) !== JSON.stringify(this.local)) {
                    this.local = next;
                }
            },
        },
    },
    methods: {
        shiftMonth(delta) {
            const date = new Date(this.viewYear, this.viewMonth + delta, 1);
            this.viewYear = date.getFullYear();
            this.viewMonth = date.getMonth();
        },
        isEdge(ymd) {
            return ymd === this.local.dateStart || ymd === this.local.dateEnd;
        },
        dayClasses(day) {
            const { dateStart, dateEnd } = this.local;
            return {
                "rpp-day-past": day.past,
                "rpp-day-busy": day.busy && !day.past,
                "rpp-day-today": day.ymd === this.today,
                "rpp-day-start": day.ymd === dateStart,
                "rpp-day-end": day.ymd === dateEnd,
                "rpp-day-in-range":
                    dateStart &&
                    dateEnd &&
                    day.ymd > dateStart &&
                    day.ymd < dateEnd,
            };
        },
        dayAriaLabel(day) {
            const parts = [formatDayShort(day.ymd)];
            if (day.ymd === this.local.dateStart) parts.push("выдача");
            if (day.ymd === this.local.dateEnd) parts.push("возврат");
            if (day.busy) parts.push("все экземпляры заняты");
            return parts.join(", ");
        },
        pickDay(ymd) {
            this.activePreset = null;
            const p = this.local;

            // Новый выбор: первый клик — выдача, второй — возврат.
            if (!p.dateStart || p.dateEnd || ymd < p.dateStart) {
                p.dateStart = ymd;
                p.dateEnd = null;
                p.timeStart =
                    p.timeStart && ymd !== this.today
                        ? p.timeStart
                        : defaultStartTime(ymd);
                p.timeEnd = null;
            } else {
                p.dateEnd = ymd;
                // По умолчанию возврат в то же время, что выдача — ровно N суток.
                p.timeEnd = p.timeEnd || p.timeStart;
            }
            this.emitChange();
        },
        applyPreset(key) {
            let start =
                this.local.dateStart && this.local.dateStart >= this.today
                    ? this.local.dateStart
                    : this.today;
            let end;

            if (key === "day") {
                end = addDays(start, 1);
            } else if (key === "week") {
                end = addDays(start, 7);
            } else {
                // Ближайшие выходные: суббота → понедельник.
                const weekday = parseYmd(this.today).getDay();
                const toSaturday = (6 - weekday + 7) % 7;
                start = addDays(this.today, toSaturday);
                end = addDays(start, 2);
            }

            const time =
                this.local.timeStart && start !== this.today
                    ? this.local.timeStart
                    : defaultStartTime(start);

            this.local = {
                dateStart: start,
                timeStart: time,
                dateEnd: end,
                timeEnd: time,
            };
            this.activePreset = key;

            const anchor = parseYmd(start);
            this.viewYear = anchor.getFullYear();
            this.viewMonth = anchor.getMonth();

            this.emitChange();
        },
        emitChange() {
            this.$emit("update:modelValue", copyPeriod(this.local));
        },
    },
};
</script>

<style scoped>
.rpp {
    display: flex;
    flex-direction: column;
    gap: 14px;
    color: #ffffff;
}
.rpp-presets {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.rpp-chip {
    background: #232323;
    color: #ffffff;
    border: 1px solid var(--line-input, #4a4a4a);
    border-radius: 20px;
    padding: 8px 16px;
    font: inherit;
    font-size: 14px;
    cursor: pointer;
}
.rpp-chip:hover {
    border-color: #ff962e;
}
.rpp-chip-active {
    background: #ff962e;
    border-color: #ff962e;
}
.rpp-calendar {
    background: #191919;
    border-radius: 20px;
    padding: 16px;
}
.rpp-calendar-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.rpp-month {
    font-weight: 500;
    font-size: 16px;
}
.rpp-nav {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #232323;
    color: #ffffff;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
}
.rpp-nav:disabled {
    opacity: 0.3;
    cursor: default;
}
.rpp-weekdays,
.rpp-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    text-align: center;
}
.rpp-weekdays {
    font-size: 12px;
    color: #8a8a8a;
    margin-bottom: 6px;
}
.rpp-day {
    height: 40px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #ffffff;
    font: inherit;
    font-size: 14px;
    cursor: pointer;
    position: relative;
}
.rpp-day:hover:not(:disabled) {
    background: #2a2a2a;
}
.rpp-day:focus-visible,
.rpp-chip:focus-visible,
.rpp-nav:focus-visible {
    outline: 2px solid #ff962e;
    outline-offset: 2px;
}
.rpp-day-past {
    color: #4a4a4a;
    cursor: default;
}
.rpp-day-today {
    box-shadow: inset 0 0 0 1px #5a5a5a;
}
.rpp-day-busy {
    color: #8a8a8a;
    text-decoration: line-through;
    background: repeating-linear-gradient(
        135deg,
        transparent 0 4px,
        rgba(255, 255, 255, 0.06) 4px 8px
    );
}
.rpp-day-in-range {
    background: rgba(255, 150, 46, 0.18);
    border-radius: 0;
}
.rpp-day-start,
.rpp-day-end {
    background: #ff962e !important;
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
}
.rpp-legend {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    font-size: 12px;
    color: #8a8a8a;
}
.rpp-legend-mark {
    width: 14px;
    height: 14px;
    border-radius: 4px;
    background: repeating-linear-gradient(
        135deg,
        transparent 0 3px,
        rgba(255, 255, 255, 0.25) 3px 6px
    );
}
.rpp-hint {
    font-size: 14px;
    color: #8a8a8a;
}
.rpp-times {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.rpp-time {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.rpp-time-label {
    font-size: 14px;
    color: #8a8a8a;
}
.rpp-time select {
    height: 50px;
    border-radius: 15px;
    border: 1px solid var(--line-input, #4a4a4a);
    background: #191919;
    color: #ffffff;
    font: inherit;
    font-size: 16px;
    padding: 0 14px;
}
.rpp-time select:disabled {
    color: #4a4a4a;
}
.rpp-summary {
    font-size: 15px;
    color: #ffffff;
}
.rpp-error {
    font-size: 14px;
    color: #ff6b5e;
}
</style>
