entity-employee
=================


Реализация сущности "Employee"(сотрудник). 

Экземпляр класса Employee представляет из себя агрегат содержащий свойства-объекты.
Используются такие характеристики сущности сотрудникак:
*	id сотрудника;
*	ФИО;
*	ИНН;
*	Дата принятия на работу;
*	Номера телефонов;
*	Дата рождения;
*	Статус (активный/в архиве);
*	Дата увольнения.

Применен подход к проектированию «Code-First» - код написан без привязки к какому-либо фреймворку.
Для взаимодействия с сущностью, из нужного контроллера или любого участка кода вызывается сервис "EmployeeService", 
который перенаправляет на выбранный репозиторий (используется для взаимодействия с базой данных).
В настоящее время реализовано 2 репозитория:
*	YiiEmployeeRepository;
*	LaravelEmployeeRepository;

имеющие общий интерфейс. Для этих фреймворков, так же, созданы файлы миграций в папке "temp_files" для быстрого создания таблиц в БД.


Использован шаблон проектирования «Строитель»(Builder) для создания и заполнения конструктора основного объекта - Employee.

Осуществляется генерация отложенных событий. Все основные действия с сущностью генерируют отдельные события, 
которые затем могут быть обработаны диспетчером (реализующим интерфейс "EventDispatcher").
  
Для использования скопировать папки:
*	dispatchers;
*	entities;
*	repositories;
*	services;
в любой нужный каталог. Например для Laravel - в каталог "app", для Yii(basic) - в корень проекта, для Yii(advanced) в каталог "common" и тд.

  
  
Сторонние расширения:
------------------  
Перед использованием необходимо дополнительно установить следующие расширения:

* UUID (Universally Unique Ids) - для генерации уникального id сущности:
```
composer require ramsey/uuid
```

* Assert - проверка данных и генератор исключений
```
composer require beberlei/assert
```
  
------------------
  
Классы расположены в пространстве имен "app".
Перед использованием, возможно, потребуется указать путь для автозагрузчика Composer в файле composer.json:
```
"autoload": {
    "psr-4": {
        "app\\": "ВАШ КАТАЛОГ"
    }
}
```



Пример использования:
------------------  

```
//Yii-2
$db = Yii::$app->db;
$hydrator = new Hydrator();
$repository = new YiiEmployeeRepository($db, $hydrator);

/*
 * Laravel-5
 * $hydrator = new Hydrator();
 * $repository = new LaravelEmployeeRepository($hydrator);
 */


/*
 * Создание нового сотрудника
 */
$employee = (new EmployeeBuilder())
    ->setId($repository->nextId())
    ->setName(new Name('Иванов', 'Иван', 'Иванович'))
    ->setCode(new Code(2852408842))
    ->setDateReceipt(new DateReceipt(new \DateTimeImmutable('2018-01-21')))
    ->setPhones(new Phones([
        new Phone('+380671111111'),
        new Phone('+380632222222')]))
    ->setDoB()
    ->setSex()
    ->build();

$dispatcher = new DummyEventDispatcher(); //Диспетчер для обработки отложенных событий
$employeeService = new EmployeeService($repository, $dispatcher); //Сервис для работы с сущностью Employee (сотрудник)

$employeeService->create($employee); //Создание записи о новом сотруднике в БД




/*
 * Работа с существующими сотрудниками
 */
$employeeGotted = $employeeService->searchByName(new Name('Иванов', 'Иван', 'Иванович')); //Поиск по ФИО
$employeeService->rename($employeeGotted->getId(), new Name('Петров', 'Иван', 'Иванович')); //Изменение в ФИО

$employeeService->removePhoneByNumber($employeeGotted->getId(), '+380632222222'); //Удаление номера телефона
$employeeService->addPhone($employeeGotted->getId(), new Phone('+380683333333')); //Добавление номера телефона
$employeeService->dismissal($employeeGotted->getId(), new \DateTimeImmutable('2018-01-25')); //Отметка об увольнении

$dateArchive = new \DateTimeImmutable('2018-01-22');
$employeeService->archive($employeeGotted->getId(), $dateArchive); //Смена статуса (в архиве)
//$employeeService->remove($employeeGotted->getId()); //Удаление сотрудника из БД
```  
  

Мой блог: [klisl.com](http://klisl.com)  