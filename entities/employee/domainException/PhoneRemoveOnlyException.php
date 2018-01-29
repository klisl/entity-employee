<?php

namespace app\entities\employee\domainException;


class PhoneRemoveOnlyException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Нельзя удалить единственный номер телефона сотрудника.');
    }
}
