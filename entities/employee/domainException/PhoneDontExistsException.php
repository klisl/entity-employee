<?php

namespace app\entities\employee\domainException;


class PhoneDontExistsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Такой номер телефона не числится в базе.');
    }
}
