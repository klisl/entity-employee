<?php

namespace app\entities\employee;

use Assert\Assertion;


class Sex
{

    const MALE = 'male';
    const FEMALE = 'female';

    private $sex;


    public function __construct(string $sex)
    {
        Assertion::inArray($sex, [
            self::MALE,
            self::FEMALE
        ]);

        $this->sex = $sex;
    }

    public function getValue(){
        return $this->sex;
    }

}