<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;
use app\entities\employee\Status;


class EmployeeChangedStatusEvent
{
    public $employeeId;
    public $status;


    public function __construct(EmployeeId $employeeId, Status $status)
    {
        $this->employeeId = $employeeId;
        $this->status = $status;
    }

    public function getId(): EmployeeId
    {
        return $this->employeeId;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

}
