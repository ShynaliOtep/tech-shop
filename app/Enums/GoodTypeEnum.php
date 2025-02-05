<?php

namespace App\Enums;

enum GoodTypeEnum: string
{
    case CAMERAS = 'Камеры';
    case LENSES = 'Объективы';
    case LIGHT = 'Свет';
    case SOUND = 'Звук';
    case STABILIZERS = 'Стабилизаторы';
    case BATTERIES = 'Аккумуляторы';
    case DRONES = 'Дроны';
    case DATA_CARDS = 'Карты памяти';
    case CAGES = 'Клетки';
    case DISPLAYS = 'Мониторы';
    case MISCELLANEOUS = 'Разное';
    case SOFTBOXES = 'Софтбоксы';
    case FILTERS = 'Фильтры';
    case STANDS = 'Штативы/стойки';
    case KITS = 'Наборы';
}
