<?php

namespace app\repositories;


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
use Ramsey\Uuid\Uuid;
use yii\db\Connection;
use yii\db\Query;


class YiiEmployeeRepository implements EmployeeRepository
{
    private $items = [];
    private $db;
    private $hydrator;

    public function __construct(Connection $db, Hydrator $hydrator)
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
    }


    public function nextId(): EmployeeId
    {
        return new EmployeeId(Uuid::uuid4()->toString());
    }


    /**
     * Поиск сущности в БД по id
     * Из полученных данных создает и возвращает новый объект
     * @param EmployeeId $id
     * @return Employee
     */
    public function get(EmployeeId $id): Employee
    {
        $employee = (new Query())->select('*')
            ->from('{{%sql_employees}}')
            ->andWhere(['id' => $id->getId()])
            ->one($this->db);

        if (!$employee) {
            throw new NotFoundException('Employee not found.');
        }

        $phones = (new Query())->select('*')
            ->from('{{%sql_employee_phones}}')
            ->andWhere(['employee_id' => $id->getId()])
            ->orderBy('id')
            ->all($this->db);

        $statuses = (new Query())->select('*')
            ->from('{{%sql_employee_statuses}}')
            ->andWhere(['employee_id' => $id->getId()])
            ->orderBy('id')
            ->all($this->db);

        $result =  $this->hydrator->hydrate(Employee::class, [
            'id' => new EmployeeId($employee['id']),
            'name' => new Name(
                $employee['name_last'],
                $employee['name_first'],
                $employee['name_middle']
            ),
            'code' => new Code($employee['code']),
            'createDate' => new \DateTimeImmutable($employee['create_date']),
            'dateReceipt' => new DateReceipt(new \DateTimeImmutable($employee['receipt_date'])),
            'dateDismissal' => $employee['dismissal_date'] ? new DateDismissal(new \DateTimeImmutable($employee['dismissal_date'])) : null,
            'phones' => new Phones(array_map(function ($phone) {
                return new Phone(
                    $phone['number']
                );
            }, $phones)),
            'statuses' => array_map(function ($status) {
                return new Status(
                    $status['value'],
                    new \DateTimeImmutable($status['date'])
                );
            }, $statuses),
            'DoB' => new DateOfBirth (new \DateTimeImmutable($employee['DoB'])),
            'sex' => new Sex($employee['sex']),
        ]);

        /** @var Employee $result */
        return $result;
    }



    public function add(Employee $employee): void
    {

        $this->db->transaction(function () use ($employee) {
            $this->db->createCommand()
                ->insert('{{%sql_employees}}', self::extractEmployeeData($employee))
                ->execute();
            $this->updatePhones($employee);
            $this->updateStatuses($employee);
        });
    }

    public function save(Employee $employee): void
    {
        $this->db->transaction(function () use ($employee) {
            $this->db->createCommand()
                ->update(
                    '{{%sql_employees}}',
                    self::extractEmployeeData($employee),
                    ['id' => $employee->getId()->getId()]
                )->execute();
            $this->updatePhones($employee);
            $this->updateStatuses($employee);
        });
    }

    public function remove(Employee $employee): void
    {
        $this->db->createCommand()
            ->delete('{{%sql_employees}}', ['id' => $employee->getId()->getId()])
            ->execute();
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
            'dismissal_date' => $employee->getDateDismissal() ? $employee->getDateDismissal()->getDate()->format('Y-m-d H:i:s') : '',
            'current_status' => end($statuses)->getValue(),
        ];
    }


    private function updatePhones(Employee $employee): void
    {
        $this->db->createCommand()
            ->delete('{{%sql_employee_phones}}', ['employee_id' => $employee->getId()->getId()])
            ->execute();

        if ($employee->getPhones()) {
            $this->db->createCommand()
                ->batchInsert('{{%sql_employee_phones}}', ['employee_id', 'number'],
                    array_map(function (Phone $phone) use ($employee) {
                        return [
                            'employee_id' => $employee->getId()->getId(),
                            'number' => $phone->getNumber(),
                        ];
                    }, $employee->getPhones()))
                ->execute();
        }
    }


    private function updateStatuses(Employee $employee): void
    {
        $this->db->createCommand()
            ->delete('{{%sql_employee_statuses}}', ['employee_id' => $employee->getId()->getId()])
            ->execute();

        if ($employee->getStatuses()) {
            $this->db->createCommand()
                ->batchInsert('{{%sql_employee_statuses}}', ['employee_id', 'value', 'date'],
                    array_map(function (Status $status) use ($employee) {
                        return [
                            'employee_id' => $employee->getId()->getId(),
                            'value' => $status->getValue(),
                            'date' => $status->getDate()->format('Y-m-d H:i:s'),
                        ];
                    }, $employee->getStatuses()))
                ->execute();
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
        /** @var Employee $employee */
        $employee = (new Query())->select('id')
            ->from('{{%sql_employees}}')
            ->andWhere(['name_last' => $name->getLast()])
            ->andWhere(['name_first' => $name->getFirst()])
            ->andWhere(['name_middle' => $name->getMiddle()])
            ->one($this->db);

        if (!$employee) {
            throw new NotFoundException('Сотрудник не найден.');
        }

        return $this->get(new EmployeeId($employee['id']));
    }
}