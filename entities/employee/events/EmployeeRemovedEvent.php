<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;


class EmployeeRemovedEvent
{

    public $employeeId;
    public $date;

    public function __construct(EmployeeId $employeeId, \DateTimeImmutable $date)
    {
        $this->employeeId = $employeeId;
        $this->date = $date;
    }

}