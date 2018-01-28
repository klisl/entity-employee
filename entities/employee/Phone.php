<?php

namespace app\entities\employee;


use Assert\Assertion;

class Phone
{
    private $number;

    public function __construct(string $number)
    {
        Assertion::notEmpty($number);

        $this->number = $number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Проверка на совпадение номеров
     * @param Phone $phone
     * @return bool
     */
    public function isEqualTo(self $phone): bool
    {
        return $this->getNumber() === $phone->getNumber();
    }

}
