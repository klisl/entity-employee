<?php

namespace app\dispatchers;

/**
 * Диспетчер для обработки отложенных событий.
 * Пока не используется ставим заглушку - запись в лог.
 * @package app\dispatchers
 */
class DummyEventDispatcher implements EventDispatcher
{
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
//            \Yii::info('Dispatch event ' . \get_class($event)); //for Yii-2
//            \Log::info('Dispatch event ' . \get_class($event)); //for Laravel-5
        }
    }
}