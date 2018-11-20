<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;

/**
 * Class LocalDI
 *
 * @package Scaleplan\DependencyInjection
 */
class LocalDI
{
    public const FABRIC_METHOD_NAME = 'getInstance';

    /**
     * @param string $interfaceName
     * @param array $args
     * @param string|null $fabricMethodName
     *
     * @return object
     *
     * @throws DependencyInjectionException
     * @throws \ReflectionException
     */
    public static function getLocalContainer(
        string $interfaceName,
        array $args = [],
        string $fabricMethodName = self::FABRIC_METHOD_NAME
    ) : object
    {
        if (!interface_exists($interfaceName) || !class_exists($interfaceName)) {
            throw new DependencyInjectionException("Parameter $interfaceName must be interface name or class name");
        }

        if (empty($containerClassName = DependencyInjection::getContainers()[$interfaceName] ?? null)) {
            return null;
        }

        $refClass = new \ReflectionClass($containerClassName);
        if (!$refClass->implementsInterface($interfaceName)) {
            throw new DependencyInjectionException("Object must implements or extends $interfaceName");
        }

        if ($refClass->isInstantiable()) {
            return $refClass->newInstanceArgs($args);
        }

        return static::getContainerByFabric($refClass, $interfaceName, $fabricMethodName, $args);
    }

    /**
     * @param \ReflectionClass $refClass
     * @param string $interfaceName
     * @param array $args
     * @param string $fabricMethodName
     *
     * @return object
     *
     * @throws DependencyInjectionException
     */
    protected static function getContainerByFabric(
        \ReflectionClass $refClass,
        string $interfaceName,
        string $fabricMethodName,
        array $args = []
    ) : object
    {
        if (!$refClass->hasMethod($fabricMethodName)) {
            throw new DependencyInjectionException(
                "Class without public constructor must have a fabric method "
                . static::FABRIC_METHOD_NAME
            );
        }

        $method = $refClass->getMethod($fabricMethodName);
        if (!$method->isStatic() || !$method->isPublic()) {
            throw new DependencyInjectionException('Fabric method not allowed');
        }

        if (!$method->getReturnType()
            || !(($object = $method->invokeArgs(null, $args)) instanceof $interfaceName))
        {
            throw new DependencyInjectionException(
                "Объект возвращаемый фабричным методом должен реализовывать интерфейс $interfaceName"
            );
        }

        return $object;
    }
}