<?

namespace App\Orchid\DTO\QuickOrder;

class QuickOrderItemDates
{
    public string $rentStartDate;
    public string $rentEndDate;
    public string $rentStartTime;
    public string $rentEndTime;

    public function __construct(
         string $rentStartDate,
         string $rentEndDate,
         string $rentStartTime,
         string $rentEndTime,
    ) {
        $this->rentStartDate = $rentStartDate;
        $this->rentEndDate = $rentEndDate;
        $this->rentStartTime = $rentStartTime;
        $this->rentEndTime = $rentEndTime;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['rentStartDate'],
            $data['rentEndDate'],
            $data['rentStartTime'],
            $data['rentEndTime'],
        );
    }

}
