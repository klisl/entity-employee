<?php

namespace app\entities\employee\domainException;


class RemoveIfStatusActiveException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Нельзя удалить сотрудника со статусом "active"');
    }
}
