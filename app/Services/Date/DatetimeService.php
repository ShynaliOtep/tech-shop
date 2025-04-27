<?php

namespace App\Services\Date;

use IntlDateFormatter;

class DatetimeService
{
    public static function textFormat(\Datetime $datetime): string
    {
        $formatter = new IntlDateFormatter(
            'ru_RU', // Локаль
            IntlDateFormatter::LONG, // Длинный формат даты
            IntlDateFormatter::NONE, // Без времени
            'Europe/Moscow', // Часовой пояс
            IntlDateFormatter::GREGORIAN // Календарь
        );

        return $formatter->format($datetime);
    }
}
