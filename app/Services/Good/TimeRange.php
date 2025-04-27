<?php

namespace App\Services\Good;

use Illuminate\Support\Carbon;

class TimeRange
{
    public Carbon $start;
    public Carbon $end;
    public function __construct(
        Carbon $start,
        Carbon $end
    )
    {
        $this->start = $start;
        $this->end = $end;
    }

    public static function fromRequest(
        string $startDate,
        string $endDate,
        string $startTime,
        string $endTime
    ): self
    {
        $start = Carbon::parse($startDate . ' ' . $startTime . ':00');
        $end = Carbon::parse($endDate . ' ' . $endTime . ':00');
        return new self($start, $end);
    }
}
