<?php

namespace app\entities\employee\domainException;


class StatusAlreadyExistsException extends \DomainException
{
    public function __construct(string $status)
    {
        parent::__construct('Статус ' . $status . ' уже является текущим для данного сотрудника.');
    }
}
