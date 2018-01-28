<?php

namespace app\repositories;

use app\entities\employee\Employee;
use app\entities\employee\EmployeeId;
use app\entities\employee\Name;


interface EmployeeRepository
{
    public function get(EmployeeId $id): Employee;

    public function getByName(Name $name): Employee;

    public function add(Employee $employee): void;

    public function save(Employee $employee): void;

    public function remove(Employee $employee): void;

    public function nextId(): EmployeeId;
}