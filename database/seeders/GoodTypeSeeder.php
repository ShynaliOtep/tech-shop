<?php

namespace Database\Seeders;

use App\Enums\GoodTypeEnum;
use App\Models\GoodType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GoodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GoodType::query()->delete();
        foreach ($this->goodTypes() as $goodType => $goodTypeData) {
            GoodType::factory()->create([
                'name' => $goodType,
                'code' => Str::lower($goodTypeData['code']),
                'icon' => $goodTypeData['icon'],
                'description' => $goodTypeData['description'],
            ]);
        }
    }

    public function goodTypes(): array
    {
        return [
            GoodTypeEnum::CAMERAS->value => [
                'code' => mb_strtolower(GoodTypeEnum::CAMERAS->name),
                'description' => 'Фото и видео камеры',
                'icon' => 'camera_alt',
            ],
            GoodTypeEnum::LENSES->value => [
                'code' => mb_strtolower(GoodTypeEnum::LENSES->name),
                'description' => 'Объективы для камер',
                'icon' => 'camera',
            ],
            GoodTypeEnum::LIGHT->value => [
                'code' => mb_strtolower(GoodTypeEnum::LIGHT->name),
                'description' => 'Лампы, доп. освещение',
                'icon' => 'highlight',
            ],
            GoodTypeEnum::SOUND->value => [
                'code' => mb_strtolower(GoodTypeEnum::SOUND->name),
                'description' => 'Микрофоны, звукопульты, рекордеры',
                'icon' => 'mic',
            ],
            GoodTypeEnum::STABILIZERS->value => [
                'code' => mb_strtolower(GoodTypeEnum::STABILIZERS->name),
                'description' => 'Стабилизаторы для камер',
                'icon' => 'multiline_chart',
            ],
            GoodTypeEnum::BATTERIES->value => [
                'code' => mb_strtolower(GoodTypeEnum::BATTERIES->name),
                'description' => 'Аккумуляторы для оборудования',
                'icon' => 'battery_charging_full',
            ],
            GoodTypeEnum::DRONES->value => [
                'code' => mb_strtolower(GoodTypeEnum::DRONES->name),
                'description' => 'Квадрокоптеры на пульте управления',
                'icon' => 'flight',
            ],
            GoodTypeEnum::DATA_CARDS->value => [
                'code' => mb_strtolower(GoodTypeEnum::DATA_CARDS->name),
                'description' => 'Расширяемая память для устройств',
                'icon' => 'sd_storage',
            ],
            GoodTypeEnum::CAGES->value => [
                'code' => mb_strtolower(GoodTypeEnum::CAGES->name),
                'description' => 'Клетки для оборудования',
                'icon' => 'developer_board',
            ],
            GoodTypeEnum::DISPLAYS->value => [
                'code' => mb_strtolower(GoodTypeEnum::DISPLAYS->name),
                'description' => 'Мониторы для лучшего обзора съемочных объектов',
                'icon' => 'airplay',
            ],
            GoodTypeEnum::MISCELLANEOUS->value => [
                'code' => mb_strtolower(GoodTypeEnum::MISCELLANEOUS->name),
                'description' => 'Дополнительные инструменты для оборудования',
                'icon' => 'add_circle',
            ],
            GoodTypeEnum::SOFTBOXES->value => [
                'code' => mb_strtolower(GoodTypeEnum::SOFTBOXES->name),
                'description' => 'Софтбоксы для наладки света',
                'icon' => 'lightbulb_outline',
            ],
            GoodTypeEnum::FILTERS->value => [
                'code' => mb_strtolower(GoodTypeEnum::FILTERS->name),
                'description' => 'Фильтры для камер',
                'icon' => 'filter',
            ],
            GoodTypeEnum::STANDS->value => [
                'code' => mb_strtolower(GoodTypeEnum::STANDS->name),
                'description' => 'Штативы и стойки для удобной установки камер и оборудования',
                'icon' => 'filter_hdr',
            ],
            GoodTypeEnum::KITS->value => [
                'code' => mb_strtolower(GoodTypeEnum::KITS->name),
                'description' => 'Наборы инструментов, тщательно подобранные для комфортной работы друг с другом',
                'icon' => 'devices_other',
            ],
        ];
    }
}
