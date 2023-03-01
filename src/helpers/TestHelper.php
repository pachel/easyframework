<?php

namespace Pachel\EasyFrameWork\Helpers;

final class MethodInvoker
{
    public function invoke($object, string $methodName, array $args=[]) {
        $privateMethod = $this->getMethod(get_class($object), $methodName);

        return $privateMethod->invokeArgs($object, $args);
    }

    public function invokeconstant($object, string $methodName, array $args=[]) {
        $class = new \ReflectionClass($object);
        $method = $class->getProperty($methodName);
        $method->setAccessible(true);
        return $method->getValue($class);
    }

    private function getMethod(string $className, string $methodName) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }
}