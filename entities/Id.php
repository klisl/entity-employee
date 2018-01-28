<?php

namespace app\entities;

/**
 * Class Id
 * Вынос в абстрактный класс т.к. может испоьзоваться для других сущностей,
 * не только Employee
 */
abstract class Id
{
    private $id;

    public function __construct($id=null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->getId() === $other->getId();
    }

}

