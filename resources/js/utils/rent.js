// Общие правила аренды для страницы товара и корзины.
// Расчёт суток совпадает с Api\OrderController: неполные сутки округляются вверх, минимум одни.

const DAY_MS = 24 * 60 * 60 * 1000;

/** Сегодняшняя дата в часовом поясе браузера, а не UTC (toISOString сдвигает на сутки ночью). */
export function todayLocal() {
    return toYmd(new Date());
}

export function toYmd(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
}

export function parseYmd(ymd) {
    const [y, m, d] = ymd.split("-").map(Number);
    return new Date(y, m - 1, d);
}

export function addDays(ymd, days) {
    const date = parseYmd(ymd);
    date.setDate(date.getDate() + days);
    return toYmd(date);
}

/** Шаг 30 минут: 00:00 … 23:30. */
export function timeOptions() {
    const options = [];
    for (let h = 0; h < 24; h++) {
        for (const m of [0, 30]) {
            options.push(
                `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`,
            );
        }
    }
    return options;
}

/** Время выдачи по умолчанию: 10:00, а если сегодня это уже прошло — ближайшие полчаса. */
export function defaultStartTime(dateStart) {
    if (dateStart !== todayLocal()) {
        return "10:00";
    }
    const now = new Date();
    const minutes = now.getHours() * 60 + now.getMinutes();
    const next = Math.ceil((minutes + 1) / 30) * 30;
    if (next >= 24 * 60) {
        return "23:30";
    }
    return `${String(Math.floor(next / 60)).padStart(2, "0")}:${String(next % 60).padStart(2, "0")}`;
}

export function isPeriodComplete(period) {
    return Boolean(
        period &&
        period.dateStart &&
        period.timeStart &&
        period.dateEnd &&
        period.timeEnd,
    );
}

function periodBounds(period) {
    return [
        new Date(`${period.dateStart}T${period.timeStart}:00`),
        new Date(`${period.dateEnd}T${period.timeEnd}:00`),
    ];
}

/** Конец позже начала. */
export function isPeriodValid(period) {
    if (!isPeriodComplete(period)) {
        return false;
    }
    const [start, end] = periodBounds(period);
    return end > start;
}

export function rentDays(period) {
    if (!isPeriodComplete(period)) {
        return 0;
    }
    const [start, end] = periodBounds(period);
    return Math.max(1, Math.ceil((end - start) / DAY_MS));
}

/** 5000 → «5 000 ₸» */
export function formatPrice(amount) {
    const value = Math.round(Number(amount) || 0);
    return `${value.toLocaleString("ru-RU").replace(/\s/g, " ")} ₸`;
}

const MONTHS_SHORT = [
    "янв",
    "фев",
    "мар",
    "апр",
    "мая",
    "июн",
    "июл",
    "авг",
    "сен",
    "окт",
    "ноя",
    "дек",
];

/** «2026-10-20» → «20 окт» */
export function formatDayShort(ymd) {
    const date = parseYmd(ymd);
    return `${date.getDate()} ${MONTHS_SHORT[date.getMonth()]}`;
}

/** 1 сутки, 2 суток, 5 суток */
export function pluralDays(n) {
    const mod10 = n % 10;
    const mod100 = n % 100;
    if (mod10 === 1 && mod100 !== 11) return `${n} сутки`;
    return `${n} суток`;
}

/** 1 товар, 2 товара, 5 товаров */
export function pluralGoods(n) {
    const mod10 = n % 10;
    const mod100 = n % 100;
    if (mod10 === 1 && mod100 !== 11) return `${n} товар`;
    if (mod10 >= 2 && mod10 <= 4 && (mod100 < 12 || mod100 > 14))
        return `${n} товара`;
    return `${n} товаров`;
}

export function samePeriod(a, b) {
    return (
        isPeriodComplete(a) &&
        isPeriodComplete(b) &&
        a.dateStart === b.dateStart &&
        a.timeStart === b.timeStart &&
        a.dateEnd === b.dateEnd &&
        a.timeEnd === b.timeEnd
    );
}

export function copyPeriod(period) {
    return {
        dateStart: period?.dateStart ?? null,
        timeStart: period?.timeStart ?? null,
        dateEnd: period?.dateEnd ?? null,
        timeEnd: period?.timeEnd ?? null,
    };
}
