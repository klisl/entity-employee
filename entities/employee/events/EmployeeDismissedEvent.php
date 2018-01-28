<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;
use DateTimeImmutable;


class EmployeeDismissedEvent
{

    public $employeeId;
    public $date;

    public function __construct(EmployeeId $employeeId, DateTimeImmutable $date)
    {
        $this->employeeId = $employeeId;
        $this->date = $date;
    }

    public function getId()
    {
        return $this->employeeId;
    }

    public function getDate()
    {
        return $this->date;
    }

}