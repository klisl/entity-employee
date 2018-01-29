<?php

namespace app\entities\employee\domainException;

use app\entities\employee\Phone;


class PhoneAlreadyExistsException extends \DomainException
{
    public function __construct(Phone $phone)
    {
        parent::__construct('Номер телефона ' . $phone->getNumber() . ' уже закреплен за данным сотрудником.');
    }
}
