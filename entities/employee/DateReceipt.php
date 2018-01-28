<?php

namespace app\entities\employee;

use DateTimeImmutable;


class DateReceipt
{
    private $date;

    public function __construct(DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function changeDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

}
