<?php

namespace app\repositories;

/**
 * Class Hydrator
 * Используется для работы с приватными полями сущностей, предварительно сделав
 * их доступными на изменение через рефлексию.
 * Устанавливаются значения для закрытых свойств переданного класса.
 * @package app\repositories
 */
class Hydrator
{
    public $reflectionClassMap;

    public function hydrate($class, array $data)
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getReflectionClass($class);
        $target = $reflection->newInstanceWithoutConstructor();
        foreach ($data as $name => $value) {
            $property = $reflection->getProperty($name);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $property->setValue($target, $value);
        }
        return $target;
    }


    private function getReflectionClass($className)
    {
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new \ReflectionClass($className);
        }
        return $this->reflectionClassMap[$className];
    }
}