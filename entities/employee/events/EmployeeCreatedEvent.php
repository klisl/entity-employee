<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;


class EmployeeCreatedEvent
{
    public $employeeId;

    public function __construct(EmployeeId $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function getId()
    {
        return $this->employeeId;
    }



}
