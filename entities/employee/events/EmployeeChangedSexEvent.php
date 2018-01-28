<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;
use app\entities\employee\Sex;


class EmployeeChangedSexEvent
{
    public $employeeId;
    public $oldSex;
    public $newSex;


    public function __construct(EmployeeId $employeeId, Sex $oldSex, Sex $newSex)
    {

        $this->employeeId = $employeeId;
        $this->oldSex = $oldSex;
        $this->newSex = $newSex;
    }

    public function getId()
    {
        return $this->employeeId;
    }

    public function getOldSex()
    {
        return $this->oldSex;
    }

    public function getNewSex()
    {
        return $this->newSex;
    }

}
