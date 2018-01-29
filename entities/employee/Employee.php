<?php

/*
 * Сущность Employee(сотрудник)
 * со свойствами в виде «объект-значение»
 *
 * @author Сергей Клименчук <ksl80@ukr.net>
 *
 * Основные действия с объектом Employee сопровождаются фиксацией в виде событий
 */

namespace app\entities\employee;


use app\entities\employee\domainException\RemoveIfStatusActiveException;
use app\entities\employee\domainException\StatusAlreadyExistsException;
use app\entities\employee\events\EmployeeChangedCodeEvent;
use app\entities\employee\events\EmployeeChangedSexEvent;
use app\entities\employee\events\EmployeeChangedStatusEvent;
use app\entities\employee\events\EmployeeCreatedEvent;
use app\entities\employee\events\EmployeePhoneAddedEvent;
use app\entities\employee\events\EmployeePhoneRemovedEvent;
use app\entities\employee\events\EmployeeRemovedEvent;
use app\entities\employee\events\EmployeeRenamedEvent;
use app\entities\EventTrait;
use DateTimeImmutable;

/**
 * Class Employee
 */
class Employee
{

    use EventTrait; //трейт для работы с событиями

    /** @var EmployeeId  id сотрудника */
    private $id;

    /** @var Name  ФИО сотрудника */
    private $name;

    /** @var Code  ИНН сотрудника */
    private $code;

    /** @var DateReceipt  Дата принятия на работу */
    private $dateReceipt;

    /** @var DateDismissal  Дата увольнения */
    private $dateDismissal;

    /** @var Phones  Номера телефонов */
    private $phones;

    /** @var Status[]  История изменения статусов сотрудника (active-активный, archived-в архиве) */
    private $statuses = [];

    /** @var DateOfBirth  Дата рождения */
    private $DoB;

    /** @var Sex  Пол */
    private $sex;

    /**
     * Дата создания записи.
     * Используется для сортировки вместо поля id, т.к. id - случайная строка (UUID)
     * @var DateTimeImmutable
     */
    private $createDate;


    /**
     * Employee constructor.
     * Заполняем значения свойств с помощью объекта EmployeeBuilder (паттерн Builder)
     * @param EmployeeBuilder $builder
     */
    public function __construct(EmployeeBuilder $builder)
    {

        $this->id = $builder->id;
        $this->name = $builder->name;
        $this->code = $builder->code;
        $this->dateReceipt = $builder->dateReceipt;
        $this->phones = $builder->phones;
        $this->createDate = $builder->createDate;
        $this->statuses = $builder->statuses;
        $this->DoB = $builder->DoB;
        $this->sex = $builder->sex;

        $this->addEvent(new EmployeeCreatedEvent($this->id));
    }

    /**
     * Добавить номер телефона
     * @param Phone $phone
     * @return void
     */
    public function addPhone(Phone $phone): void
    {
        $this->phones->add($phone);

        $this->addEvent(new EmployeePhoneAddedEvent($this->getId(), $phone));
    }

    /**
     * Удаление телефона по индексу его объекта в массиве
     * @param $index
     * @return void
     */
    public function removePhoneByIndex($index): void
    {
        $phone = $this->phones->removeByIndex($index);

        $this->addEvent(new EmployeePhoneRemovedEvent($this->getId(), $phone));
    }

    /**
     * Удаление телефона по его номеру
     * @param string $number
     * @return void
     */
    public function removePhoneByNumber(string $number): void
    {
        $phone = $this->phones->removeByNumber($number);

        $this->addEvent(new EmployeePhoneRemovedEvent($this->getId(), $phone));
    }


    /**
     * Изменение ФИО
     * @param Name $name
     * @return void
     */
    public function rename(Name $name): void
    {
        $oldName = $this->name;
        $this->name = $name;

        $this->addEvent(new EmployeeRenamedEvent($this->getId(), $oldName, $name));
    }

    /**
     * Изменение идентификационного кода
     * @param Code $value
     * @return void
     */
    public function changeCode(Code $value): void
    {
        $oldInn = $this->code;
        $this->code = $value;

        $this->addEvent(new EmployeeChangedCodeEvent($this->getId(), $oldInn, $value));
    }

    /**
     * Смена пола сотрудника
     * @param Sex $value
     */
    public function changeSex(Sex $value)
    {
        $oldSex = $this->sex;
        $this->sex = $value;

        $this->addEvent(new EmployeeChangedSexEvent($this->getId(), $oldSex, $this->sex));
    }

    /**
     * Смена статуса сотрудника на "archived" - в архиве
     * @param \DateTimeImmutable $date
     * @return void
     */
    public function archive(DateTimeImmutable $date): void
    {
        if($this->isArchived()){
            throw new StatusAlreadyExistsException('archived');
        }

        $this->addStatus(Status::ARCHIVED, $date);
        //Передача конструктору события id сотрудника и новый статус (последний элемент массива)
        $this->addEvent(new EmployeeChangedStatusEvent($this->getId(), end($this->statuses)));
    }

    /**
     * Смена статуса сотрудника на "active" - активный
     * @param \DateTimeImmutable $date
     * @return void
     */
    public function reinstate(DateTimeImmutable $date): void
    {
        if($this->isActive()){
            throw new StatusAlreadyExistsException('active');
        }

        $this->addStatus(Status::ACTIVE, $date);
        $this->addEvent(new EmployeeChangedStatusEvent($this->getId(), end($this->statuses)));
    }

    /**
     * Проверка - статус active
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getCurrentStatus()->isActive();
    }

    /**
     * Проверка - статус archive
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->getCurrentStatus()->isArchived();
    }

    /**
     * Добавление статуса сотрудника Status::ACTIVE/Status::ARCHIVED
     * @param $value
     * @return void
     */
    private function addStatus($value, DateTimeImmutable $date): void
    {
        $this->statuses[] = new Status($value, $date);
    }

    /**
     * Возвращает текущий статус (объект Status)
     * @return Status
     */
    private function getCurrentStatus(): Status
    {
        return end($this->statuses); //последний элемент массива
    }


    /**
     * Дата увольнения
     * @param DateTimeImmutable $date
     * @return void
     */
    public function dismissal(DateTimeImmutable $date): void
    {
        $this->dateDismissal = new DateDismissal($date);
    }

    /**
     * Удаление всех данных сотрудника
     * @return void
     */
    public function remove(): void
    {
        if(!$this->isArchived()){
            throw new RemoveIfStatusActiveException();
        }

        $this->addEvent(new EmployeeRemovedEvent($this->getId(), new DateTimeImmutable()));
    }


    public function getId(): EmployeeId { return $this->id; }
    public function getName(): Name { return $this->name; }
    public function getCode(): Code { return $this->code; }
    public function getPhones(): array { return $this->phones->getAll(); }
    public function getDateReceipt(): DateReceipt { return $this->dateReceipt; }
    public function getDateDismissal() { return $this->dateDismissal; }
    public function getDateOfBirth(): DateOfBirth { return $this->DoB; }
    public function getSex(): Sex { return $this->sex; }
    public function getCreateDate(): DateTimeImmutable { return $this->createDate; }
    public function getStatuses(): array { return $this->statuses; }

}

