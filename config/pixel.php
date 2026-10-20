<?php

/*
|--------------------------------------------------------------------------
| Контакты и пункты выдачи по городам
|--------------------------------------------------------------------------
|
| Единый источник для футера, блока «Условия аренды» на странице товара
| и в корзине. Ключ — id города (1 — Алматы, 2 — Астана).
| Тексты — ключи translations.*, чтобы работала английская версия.
|
*/

return [
    'whatsapp_url' => 'http://wa.link/pms0of',
    'instagram_url' => 'https://www.instagram.com/pixel_rental.kz/?hl=en',

    'cities' => [
        1 => [
            'address' => 'translations.Tole BI street, 176',
            'hours' => 'translations.Open 24 hours',
            'map_url' => 'https://2gis.kz/almaty/firm/70000001069136996',
        ],
        2 => [
            'address' => 'translations.Bokeikhan street, 6',
            'hours' => 'translations.Daily from 09:00 to 21:00',
            'map_url' => 'https://2gis.kz/astana/firm/70000001104590118',
        ],
    ],
];
