<?php

namespace app\entities\employee\events;

use app\entities\employee\EmployeeId;
use app\entities\employee\Name;


class EmployeeRenamedEvent
{
    private $employeeId;
    private $oldName;
    private $newName;


    public function __construct(EmployeeId $employeeId, Name $oldName, Name $newName)
    {

        $this->employeeId = $employeeId;
        $this->oldName = $oldName;
        $this->newName = $newName;
    }

    public function getId()
    {
        return $this->employeeId;
    }

    public function getOldName()
    {
        return $this->oldName;
    }

    public function getNewName()
    {
        return $this->newName;
    }

}
