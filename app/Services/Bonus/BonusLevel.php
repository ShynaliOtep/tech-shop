<?php

namespace App\Services\Bonus;

class BonusLevel
{
    public int $level;
    public int $percent;
    public string $name;
    public int $bonusSum;

    public function __construct(
        int $level,
        int $percent,
        string $name,
        int $bonusSum
    )
    {
        $this->level = $level;
        $this->percent = $percent;
        $this->name = $name;
        $this->bonusSum = $bonusSum;
    }
}
