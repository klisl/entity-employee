entity-employee
=================


Реализация сущности "Employee"(сотрудник). 

Класс Employee представляет из себя объект-агрегат содержащий свойства-объекты.
Использованы такие характеристики объекта как:
*	id сотрудника;
*	ФИО;
*	ИНН;
*	Дата принятия на работу;
*	Номера телефонов;
*	Дата рождения;
*	Статус (активный/в архиве);
*	Дата увольнения.

Использован подход к проектированию «Code-First» - код написан без привязки к какому-либо фреймворку.
Для взаимодействия с сущностью, из нужного контроллера или любого участка кода вызывается сервис "EmployeeService", 
который перенаправляет на выбранный репозиторий (используется для взаимодействия с базой данных).
В настоящее время реализовано 2 репозитория:
*	YiiEmployeeRepository;
*	LaravelEmployeeRepository;
имеющие общий интерфейс. Для этих фреймворков, так же, созданы файлы миграций в папке "temp_files".


Использован шаблон проектирования «Строитель»(Builder) для создания и заполнения конструктора основного объекта - Employee.

Осуществляется генерация отложенных событий. Все основные действия с сущностью генерируют отдельные события, 
которые затем могут быть обработаны диспетчером (реализующим интерфейс "EventDispatcher").
  
Для использования скопировать папки:
*	dispatchers;
*	entities;
*	repositories;
*	services;
в любой нужный каталог. Например каталог "app" в Laravel, в корень проекта Yii(basic), в каталог common Yii(advanced) и тд.

  
  
Используемые сторонние расширения:
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
  
  
Классы расположены в пространстве имен "app".
Перед использованием, возможно, потребуется указать путь для автозагрузчика Composer в файле composer.json:
```
"autoload": {
    "psr-4": {
        "app\\": "ВАШ КАТАЛОГ"
    }
```



Пример использования:
------------------  
$hydrator = new Hydrator();
$repository = new LaravelEmployeeRepository($hydrator);

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


//Диспетчер для обработки отложенных событий
$dispatcher = new DummyEventDispatcher();
//Сервис для работы с сущностью Employee (сотрудник)
$employeeService = new EmployeeService($repository, $dispatcher);

$employeeService->create($employee); //Создание записи о новом сотруднике в БД

//Удаление номера телефона
$employeeService->removePhoneByNumber($employee->getId(), '+380632222222');



/*
 * Работа с существующими сотрудниками
 */
$employeeGotted = $employeeService->searchByName(new Name('Иванов', 'Иван', 'Иванович')); //Поиск по ФИО
$employeeService->rename($employeeGotted->getId(), new Name('Петров', 'Иван', 'Иванович')); //Изменение в ФИО
$employeeService->addPhone($employeeGotted->getId(), new Phone('+380683333333')); //добавить номер телефона
$employeeService->dismissal($employeeGotted->getId(), new \DateTimeImmutable('2018-01-25')); //увольнение

//        $dateArchive = new \DateTimeImmutable('2018-01-22');
//        $employeeService->archive($employeeGotted->getId(), $dateArchive); //Смена статуса (в архиве)
//        $employeeService->remove($employeeGotted->getId()); //Удаление сотрудника из БД
  
  

Мой блог: [klisl.com](http://klisl.com)  