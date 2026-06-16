<?php

namespace App\Services\Date;

use IntlDateFormatter;

class DatetimeService
{
    private const RU_MONTHS = [
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    ];

    public static function textFormat(\Datetime $datetime): string
    {
        // Use the intl extension when available, otherwise fall back to a
        // manual Russian formatting so the page does not crash on servers
        // where the intl extension is not installed.
        if (class_exists(IntlDateFormatter::class)) {
            $formatter = new IntlDateFormatter(
                'ru_RU', // Локаль
                IntlDateFormatter::LONG, // Длинный формат даты
                IntlDateFormatter::NONE, // Без времени
                'Europe/Moscow', // Часовой пояс
                IntlDateFormatter::GREGORIAN // Календарь
            );

            return $formatter->format($datetime);
        }

        $month = self::RU_MONTHS[(int) $datetime->format('n')];

        return sprintf('%d %s %d г.', (int) $datetime->format('j'), $month, (int) $datetime->format('Y'));
    }
}
