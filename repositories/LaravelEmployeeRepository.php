<?php

namespace app\repositories;

use DB;
use app\entities\employee\Code;
use app\entities\employee\DateDismissal;
use app\entities\employee\DateOfBirth;
use app\entities\employee\DateReceipt;
use app\entities\employee\Employee;
use app\entities\employee\EmployeeId;
use app\entities\employee\Name;
use app\entities\employee\Phone;
use app\entities\employee\Phones;
use app\entities\employee\Sex;
use app\entities\employee\Status;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;


/**
 * Репозиторий для сущности Employee(сотрудник)
 *
 * @package app\repositories
 */
class LaravelEmployeeRepository implements EmployeeRepository
{
    /** @var Hydrator  */
    private $hydrator;


    public function __construct(Hydrator $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Создание объекта EmployeeId с идентификатором текущего сотрудника
     * Используется расширение UUID
     * @return EmployeeId
     */
    public function nextId(): EmployeeId
    {
        return new EmployeeId(Uuid::uuid4()->toString());
    }


    /**
     * Поиск сотрудника в БД по id
     * Из полученных данных создает и возвращает новый объект
     * @param EmployeeId $id
     * @return Employee
     */
    public function get(EmployeeId $id): Employee
    {
        $employee = DB::table('sql_employees')->select('*')
            ->where('id', $id->getId())
            ->first();

        if (!$employee) {
            throw new NotFoundException('Employee not found.');
        }

        /** @var Collection $phones */
        $phones =  DB::table('sql_employee_phones')->select('*')
            ->where('employee_id', $id->getId())
            ->orderBy('id')
            ->get();

        /** @var Collection $statuses */
        $statuses = DB::table('sql_employee_statuses')->select('*')
            ->where('employee_id', $id->getId())
            ->orderBy('id')
            ->get();

        $result =  $this->hydrator->hydrate(Employee::class, [
            'id' => new EmployeeId($employee->id),
            'name' => new Name(
                $employee->name_last,
                $employee->name_first,
                $employee->name_middle
            ),
            'code' => new Code($employee->code),
            'createDate' => new \DateTimeImmutable($employee->create_date),
            'dateReceipt' => new DateReceipt(new \DateTimeImmutable($employee->receipt_date)),
            'dateDismissal' => $employee->dismissal_date ? new DateDismissal(new \DateTimeImmutable($employee->dismissal_date)) : null,
            'phones' => new Phones(array_map(function ($phone) {
                return new Phone(
                    $phone->number
                );
            }, $phones->all())),
            'statuses' => array_map(function ($status) {
                return new Status(
                    $status->value,
                    new \DateTimeImmutable($status->date)
                );
            }, $statuses->all()),
            'DoB' => new DateOfBirth (new \DateTimeImmutable($employee->DoB)),
            'sex' => new Sex($employee->sex),
        ]);

        /** @var Employee $result */
        return $result;
    }

    /**
     * Добавление нового сотрудника в БД
     * @param Employee $employee
     * @return void
     */
    public function add(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            DB::table('sql_employees')->insert(
                self::extractEmployeeData($employee)
            );

            $this->updatePhones($employee);
            $this->updateStatuses($employee);
        });
    }

    /**
     * Обновление данных сотрудника в БД
     * @param Employee $employee
     * @return void
     */
    public function save(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {

            DB::table('sql_employees')
                ->where(['id' => $employee->getId()->getId()])
                ->update(
                    self::extractEmployeeData($employee)
                );
            $this->updatePhones($employee);
            $this->updateStatuses($employee);
        });
    }

    /**
     * Удаление сотрудника из БД
     * @param Employee $employee
     * @return void
     */
    public function remove(Employee $employee): void
    {
        DB::table('sql_employees')->where('id', $employee->getId()->getId())->delete();
    }


    /**
     * Раскладывает объект сущности на части,
     * возвращает массив со структурой как в таблице из БД
     * @param Employee $employee
     * @return array
     */
    private static function extractEmployeeData(Employee $employee): array
    {
        $statuses = $employee->getStatuses();

        return [
            'id' => $employee->getId()->getId(),
            'create_date' => $employee->getCreateDate()->format('Y-m-d H:i:s'),
            'receipt_date' => $employee->getDateReceipt()->getDate()->format('Y-m-d H:i:s'),
            'name_last' => $employee->getName()->getLast(),
            'name_middle' => $employee->getName()->getMiddle(),
            'name_first' => $employee->getName()->getFirst(),
            'code' => $employee->getCode()->getInn(),
            'DoB' => $employee->getDateOfBirth()->getDate()->format('Y-m-d H:i:s'),
            'sex' => $employee->getSex()->getValue(),
            'dismissal_date' => $employee->getDateDismissal() ? $employee->getDateDismissal()->getDate()->format('Y-m-d H:i:s') : null,
            'current_status' => end($statuses)->getValue(),
        ];
    }

    /**
     * Обновляет номера телефонов сотрудника
     * Удаляет те, что были/добавляет имеющийся у объекта Employee
     * @param Employee $employee
     * @return void
     */
    private function updatePhones(Employee $employee): void
    {
        DB::table('sql_employee_phones')->where('employee_id', $employee->getId()->getId())->delete();

        if ($employee->getPhones()) {
            DB::table('sql_employee_phones')->insert(

                array_map(function (Phone $phone) use ($employee) {
                    return [
                        'employee_id' => $employee->getId()->getId(),
                        'number' => $phone->getNumber(),
                    ];
                }, $employee->getPhones()));
        }
    }

    /**
     * Обновляет статус сотрудника
     * Удаляет тот, что был/добавляет имеющийся у объекта Employee
     * @param Employee $employee
     * @return void
     */
    private function updateStatuses(Employee $employee): void
    {
        DB::table('sql_employee_statuses')->where('employee_id', $employee->getId()->getId())->delete();

        if ($employee->getStatuses()) {
            DB::table('sql_employee_statuses')->insert(

                array_map(function (Status $status) use ($employee) {
                    return [
                        'employee_id' => $employee->getId()->getId(),
                        'value' => $status->getValue(),
                        'date' => $status->getDate()->format('Y-m-d H:i:s'),
                    ];
                }, $employee->getStatuses()));
        }
    }


    /**
     * Поиск в БД по ФИО
     * @param Name $name
     * @return Employee
     * @throws NotFoundException
     */
    public function getByName(Name $name): Employee
    {
        /** @var \stdClass $employee */
        $employee = DB::table('sql_employees')
            ->select('id')
            ->where(['name_last' => $name->getLast()])
            ->where(['name_first' => $name->getFirst()])
            ->where(['name_middle' => $name->getMiddle()])
            ->first();

        if (!$employee) {
            throw new NotFoundException('Сотрудник не найден.');
        }

        return $this->get(new EmployeeId($employee->id));
    }


    public function dismissed()
    {

    }

}