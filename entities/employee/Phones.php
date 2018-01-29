<?php

namespace app\entities\employee;

use app\entities\employee\domainException\PhoneAbsentException;
use app\entities\employee\domainException\PhoneAlreadyExistsException;
use app\entities\employee\domainException\PhoneDontExistsException;
use app\entities\employee\domainException\PhoneRemoveOnlyException;


class Phones
{
    /** @var Phone[] */
    private $phones = [];


    public function __construct(array $phones=[])
    {
        if(!$phones) throw new PhoneAbsentException();

        foreach ($phones as $phone){
            $this->add($phone);
        }
    }

    /**
     * Получить список номеров телефонов (коллекцию объектов)
     * @return Phone[]
     */
    public function getAll(){
        return $this->phones;
    }


    /**
     * Добавить номер телефона в коллекцию
     * @param Phone $phone
     * @return void
     * @throws PhoneAlreadyExistsException
     */
    public function add(Phone $phone): void
    {
        foreach ($this->phones as $item){
            /** @var Phone $phone*/
            if($item->isEqualTo($phone)){
                throw new PhoneAlreadyExistsException($phone);
            }
        }
        $this->phones[] = $phone;
    }


    /**
     * Удалить по индексу
     * @param mixed $index Ключ массива объектов Phone
     * @return Phone
     * @throws PhoneDontExistsException
     * @throws PhoneRemoveOnlyException
     */
    public function removeByIndex($index): Phone
    {

        if(!isset($this->phones[$index])){
            throw new PhoneDontExistsException();
        }
        if(count($this->phones) === 1){
            throw new PhoneRemoveOnlyException();
        }

        $phone = $this->phones[$index];
        unset($this->phones[$index]);
        return $phone;
    }


    /**
     * Удалить по номеру
     * @param string $number
     * @return Phone
     * @throws PhoneRemoveOnlyException
     * @throws PhoneDontExistsException
     */
    public function removeByNumber(string $number): Phone
    {
        if(count($this->phones) === 1){
            throw new PhoneRemoveOnlyException();
        }

        foreach ($this->phones as $key => $phone){
            if($phone->getNumber() === $number){

                $phone = $this->phones[$key];
                unset($this->phones[$key]);
                return $phone;
            }
        }
        throw new PhoneDontExistsException();
    }

}
