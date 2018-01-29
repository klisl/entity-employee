<?php

namespace app\entities\employee;


class Code
{
    private $inn;

    public function __construct(string $code)
    {
        $this->inn = $code;
    }

    public function getInn(): string
    {
        return $this->inn;
    }


    /**
     * Парсит ИНН человека (Украина)
     * https://biznesguide.ru/coding/173.html
     * @param string $inn
     * @return array|bool
     */
    public static function parse_inn(string $inn){

        //$id must contain 10 digits
        if (empty($inn) || !preg_match('/^\d{10}$/',$inn)) return false;

        $months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

        $result = array();
        $result['inn'] = $inn;
        $result['sex'] = (substr($inn, 8, 1) % 2) ? 'm' : 'f';

        $split = str_split($inn);

        $summ = $split[0]*(-1) + $split[1]*5 + $split[2]*7 + $split[3]*9 + $split[4]*4 + $split[5]*6 + $split[6]*10 + $split[7]*5 + $split[8]*7;

        $result['control'] = (int)($summ - (11 * (int)($summ/11)));

        $result['status'] = ($result['control'] == (int)$split[9]) ? true : false;

        $inn = substr($inn, 0, 5);

        $normal_date = date('d.m.Y', strtotime('01/01/1900 + ' . $inn . ' days - 1 days'));

        list($result['day'], $result['month'], $result['year']) = explode('.', $normal_date);

        $result['str_month'] = $months[$result['month'] - 1];

        return $result;
    }

    /**
     * Дата рождения по ИНН
     * @param string $inn
     * @return string
     */
    public static function getDateOfBirth(string $inn)
    {
        if($parsing = self::parse_inn($inn)){
//            return $parsing['day'].'.'.$parsing['month'].'.'.$parsing['year'];
            return $parsing['year'].'-'.$parsing['month'].'-'.$parsing['day'];
        }
        return false;
    }

    /**
     * Пол сотрудника по ИНН
     * @param string $inn
     * @return string
     */
    public static function getSex(string $inn)
    {
        if($parsing = self::parse_inn($inn)){
            $sex = $parsing['sex'];
            return $sex === 'm' ? 'male' : 'female';
        }
        return false;
    }


}
