<?php

namespace app\entities\employee;


use Assert\Assertion;

class Name
{

    private $last;

    private $first;

    private $middle = '';


    public function __construct(string $last, string $first, string $middle=null)
    {
        Assertion::notEmpty($last);
        Assertion::notEmpty($first);

        $this->last = $last;
        $this->first = $first;
        if($middle) $this->middle = $middle;

    }

    public function getFull(): string
    {
        return trim($this->last . ' ' . $this->first . ' ' . $this->middle);
    }


    public function getLast(): string
    {
        return $this->last;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getMiddle(): string
    {
        return $this->middle;
    }

}
