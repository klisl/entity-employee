<?php

/**
 * Вспомогательный класс для формирования свойств и создания основного объекта
 * app\entities\employee\Employee (паттерн Builder)
 */
namespace app\entities\employee;

use DateTimeImmutable;


class EmployeeBuilder
{

    /** @var EmployeeId  id сотрудника */
    public $id;

    /** @var Name  ФИО сотрудника */
    public $name;

    /** @var Code  ИНН сотрудника */
    public $code;

    /** @var DateReceipt  Дата принятия на работу */
    public $dateReceipt;

    /** @var DateDismissal  Дата увольнения */
    public $dateDismissal;

    /** @var Phones  Номера телефонов */
    public $phones;

    /** @var Status[]  История изменения статусов сотрудника (active-активный, archived-в архиве) */
    public $statuses = [];

    /** @var DateOfBirth  Дата рождения */
    public $DoB;

    /** @var Sex  Пол */
    public $sex;

    /** @var DateTimeImmutable  Дата создания карточки сотрудника */
    public $createDate;


    /**
     * EmployeeBuilder constructor.
     * @param EmployeeId $id
     */
    public function __construct()
    {
        $this->createDate = new DateTimeImmutable();
        //Сразу добавляем статус active
        $this->statuses[] = new Status(Status::ACTIVE, $this->createDate);
    }

    /**
     * @param EmployeeId $id
     * @return $this
     */
    public function setId(EmployeeId $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param Name $name
     * @return $this
     */
    public function setName(Name $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param Code $code
     * @return $this
     */
    public function setCode(Code $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param DateReceipt $dateReceipt
     * @return $this
     */
    public function setDateReceipt(DateReceipt $dateReceipt)
    {
        $this->dateReceipt = $dateReceipt;
        return $this;
    }

    /**
     * @param Phones $phones
     * @return $this
     */
    public function setPhones(Phones $phones)
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * @param DateOfBirth|null $DoB
     * @return $this
     */
    public function setDoB(DateOfBirth $DoB = null)
    {
        if($DoB){
            $this->DoB = $DoB;

            //дата рождения по ИНН
        } else if($DoB = Code::getDateOfBirth($this->code->getInn())){
                $date = new DateTimeImmutable($DoB);
                $this->DoB = new DateOfBirth($date);
        }

        return $this;
    }

    /**
     * @param Sex|null $sex
     * @return $this
     */
    public function setSex(Sex $sex = null)
    {
        if($sex){
            $this->sex = $sex;

            //пол по ИНН
        } else if($sex = Code::getSex($this->code->getInn())){
            $this->sex = new Sex($sex);
        }

        return $this;
    }

    /**
     * Создание основного объекта (сущности)
     * @return Employee
     */
    public function build(): Employee
    {
        return new Employee($this);
    }

}

