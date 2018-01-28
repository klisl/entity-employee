<?php

namespace app\services;

use app\dispatchers\EventDispatcher;
use app\entities\Employee\Employee;
use app\entities\Employee\EmployeeId;
use app\entities\Employee\Name;
use app\entities\Employee\Phone;
use app\repositories\EmployeeRepository;
use DateTimeImmutable;


/**
 * Сервис для работы с сущностью Employee(сотрудник)
 *
 * @package app\services
 */
class EmployeeService
{
    /** @var EmployeeRepository Репозиторий (работа с БД */
    private $employees;

    /** @var EventDispatcher Диспетчер - работа с отложенными событиями */
    private $dispatcher;


    public function __construct(EmployeeRepository $employees, EventDispatcher $dispatcher)
    {
        $this->employees = $employees;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Создание записи сотрудника в БД
     * @param Employee $employee
     * @return void
     */
    public function create(Employee $employee): void
    {
        $this->employees->add($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Поиск сотрудника по ФИО
     * @param Name $name
     * @return Employee
     */
    public function searchByName(Name $name): Employee
    {
        return $this->employees->getByName($name);
    }

    /**
     * Переименование сутрудника
     * @param EmployeeId $id
     * @param Name $name
     * @return void
     */
    public function rename(EmployeeId $id, Name $name): void
    {
        $employee = $this->employees->get($id);
        $employee->rename($name);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Добавление номера телефона
     * @param EmployeeId $id
     * @param Phone $phone
     * @return void
     */
    public function addPhone(EmployeeId $id, Phone $phone): void
    {
        $employee = $this->employees->get($id);
        $employee->addPhone($phone);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Удаление номера телефона сотрудника по индексу массива
     * @param EmployeeId $id
     * @param $index
     * @return void
     */
    public function removePhoneByIndex(EmployeeId $id, $index): void
    {
        $employee = $this->employees->get($id);
        $employee->removePhoneByIndex($index);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Удаление номера телефона сотрудника по строке с номером
     * @param EmployeeId $id
     * @param string $number
     * @return void
     */
    public function removePhoneByNumber(EmployeeId $id, string $number): void
    {
        $employee = $this->employees->get($id);
        $employee->removePhoneByNumber($number);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Добавить статус "archived"
     * @param EmployeeId $id
     * @param DateTimeImmutable $date
     * @return void
     */
    public function archive(EmployeeId $id, DateTimeImmutable $date): void
    {
        $employee = $this->employees->get($id);
        $employee->archive($date);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Добавить статус "active"
     * @param EmployeeId $id
     * @param DateTimeImmutable $date
     * @return void
     */
    public function reinstate(EmployeeId $id, DateTimeImmutable $date): void
    {
        $employee = $this->employees->get($id);
        $employee->reinstate($date);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Удаление сотрудника из БД
     * @param EmployeeId $id
     * @return void
     */
    public function remove(EmployeeId $id): void
    {
        $employee = $this->employees->get($id);
        $employee->remove();
        $this->employees->remove($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

    /**
     * Увольнение сотрудника (дата в поле dismissal_date)
     * @param EmployeeId $id
     * @param DateTimeImmutable $date
     */
    public function dismissal(EmployeeId $id, DateTimeImmutable $date): void
    {
        $employee = $this->employees->get($id);
        $employee->dismissal($date);
        $this->employees->save($employee);
        $this->dispatcher->dispatch($employee->getEvents());
    }

}