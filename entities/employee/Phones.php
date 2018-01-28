<?php

namespace app\entities\employee;



class Phones
{
    /** @var Phone[] */
    private $phones = [];

    public function __construct(array $phones=[])
    {
        if(!$phones) throw new \DomainException('Необходимо указать хотя бы один номер телефона.');

        foreach ($phones as $phone){
            $this->add($phone);
        }
    }


    public function getAll(){
        return $this->phones;
    }


    public function add(Phone $phone): void
    {
        foreach ($this->phones as $item){
            /** @var Phone $phone*/
            if($item->isEqualTo($phone)){
                throw new \DomainException('Такой номер телефона уже существует');
            }
        }
        $this->phones[] = $phone;
    }


    /**
     * @param mixed $index Ключ массива объектов Phone
     * @return Phone
     * @throws \DomainException
     */
    public function removeByIndex($index): Phone
    {

        if(!isset($this->phones[$index])){
            throw new \DomainException('Такого номера телефона не существует.');
        }
        if(count($this->phones) === 1){
            throw new \DomainException('Нельзя удалить единственный номер телефона');
        }

        $phone = $this->phones[$index];
        unset($this->phones[$index]);
        return $phone;
    }


    public function removeByNumber(string $number): Phone
    {
        if(count($this->phones) === 1){
            throw new \DomainException('Нельзя удалить единственный номер телефона');
        }

        foreach ($this->phones as $key => $phone){
            if($phone->getNumber() === $number){

                $phone = $this->phones[$key];
                unset($this->phones[$key]);
                return $phone;
            }
        }

        throw new \DomainException('Такого номера телефона не существует.');
    }

}
