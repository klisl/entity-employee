<?php

namespace app\entities\employee\events;

use app\entities\employee\Code;
use app\entities\employee\EmployeeId;


class EmployeeChangedCodeEvent
{
    public $employeeId;
    public $oldInn;
    public $newInn;


    public function __construct(EmployeeId $employeeId, Code $oldInn, Code $newInn)
    {

        $this->employeeId = $employeeId;
        $this->oldInn = $oldInn;
        $this->newInn = $newInn;
    }

    public function getId()
    {
        return $this->employeeId;
    }

    public function getOldInn()
    {
        return $this->oldInn;
    }

    public function getNewInn()
    {
        return $this->newInn;
    }

}
