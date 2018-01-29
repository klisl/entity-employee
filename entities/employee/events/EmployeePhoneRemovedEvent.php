<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;
use app\entities\employee\Phone;


class EmployeePhoneRemovedEvent
{
    public $employeeId;
    public $phone;

    public function __construct(EmployeeId $employeeId, Phone $phone)
    {
        $this->employeeId = $employeeId;
        $this->phone = $phone;
    }

    public function getId()
    {
        return $this->employeeId;
    }

    public function getPhone()
    {
        return $this->phone;
    }

}
