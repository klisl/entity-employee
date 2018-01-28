<?php

namespace app\entities\employee;

use DateTimeImmutable;

class DateOfBirth
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

}