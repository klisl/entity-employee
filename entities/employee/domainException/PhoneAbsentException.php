<?php

namespace app\entities\employee\domainException;


class PhoneAbsentException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Необходимо указать хотя бы один номер телефона.');
    }
}
