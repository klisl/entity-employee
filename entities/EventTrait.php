<?php

namespace app\entities;

/**
 * Trait EventTrait
 * Для работы с событиями сущностей
 */
trait EventTrait
{
    /** @var array Массив событий */
    private $events = [];

    /**
     * Добавление события
     * @param $event
     * @return void
     */
    public function addEvent($event): void
    {
        $this->events[] = $event;
    }

    /**
     * Возвращает массив событий
     * с очисткой свойства
     * @return array
     */
    public function getEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

}
