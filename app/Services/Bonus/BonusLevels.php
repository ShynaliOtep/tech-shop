<?php

namespace App\Services\Bonus;

use function Laravel\Prompts\select;

class BonusLevels
{
    /**
     * @return array | BonusLevel[]
     */
    public static function levels():array
    {
        return [
            new BonusLevel(
              0,
              0,
              'Без уровня',
              0,
            ),
            new BonusLevel(
                1,
                5,
                'Black',
                50000

            ),
            new BonusLevel(
                2,
                10,
                'Bronze',
                500000
            ),
            new BonusLevel(
                3,
                15,
                'Emerald',
                1500000
            ),
            new BonusLevel(
                4,
                30,
                'Amethyst',
                5000000
            )
        ];
    }

    public static function levelMatch(int $levelId): BonusLevel
    {
        $levels = self::levels();
        foreach ($levels as $level){
            if ($level->level = $levelId) {
                return $level;
            }
        }
        return self::levels()[0];
    }

    public static function getLevelFromEarned(int $totalEarned): BonusLevel
    {
        foreach (array_reverse(self::levels()) as $level){
            if ($totalEarned >= $level->bonusSum) {
                return $level;
            }
        }
        return self::levels()[0];
    }
}
