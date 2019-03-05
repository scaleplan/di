<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\ContainerNotImplementsException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodInvalidException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodNotAllowedException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodNotFoundException;
use Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException;

/**
 * Class LocalDI
 *
 * @package Scaleplan\DependencyInjection
 */
class LocalDI
{
    public const FACTORY_METHOD_NAME = 'getInstance';

    public const CONTAINER_DATA_SEPARATOR = '::';

    /**
     * @param string $interfaceName
     * @param array $args
     * @param bool $isStatic
     * @param string|null $factoryMethodName
     *
     * @return \object|null|string
     *
     * @throws ContainerNotImplementsException
     * @throws DependencyInjectionException
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     * @throws \ReflectionException
     */
    public static function getLocalContainer(
        string $interfaceName,
        array $args = [],
        \bool $isStatic = false,
        string $factoryMethodName = null
    ) {
        if (!interface_exists($interfaceName) || !class_exists($interfaceName)) {
            throw new ParameterMustBeInterfaceNameOrClassNameException(
                "Parameter $interfaceName must be interface name or class name"
            );
        }

        if (empty($container = DependencyInjection::getContainers()[$interfaceName] ?? null)) {
            return null;
        }

        if (!class_exists($container) || !is_subclass_of($container, $interfaceName)) {
            throw new ContainerNotImplementsException("Object must implements or extends $interfaceName");
        }

        [$containerClassName, $containerFactoryMethodName] = explode('::', $container);

        if ($isStatic) {
            return $containerClassName;
        }

        $refClass = new \ReflectionClass($containerClassName);
        $factoryMethodName = $factoryMethodName ?? $containerFactoryMethodName;
        if ($factoryMethodName) {
            return static::getContainerByFactory($refClass, $interfaceName, $factoryMethodName, $args);
        }

        if ($refClass->isInstantiable()) {
            return $refClass->newInstanceArgs($args);
        }

        return static::getContainerByFactory($refClass, $interfaceName, static::FACTORY_METHOD_NAME, $args);
    }

    /**
     * @param \ReflectionClass $refClass
     * @param string $interfaceName
     * @param string $factoryMethodName
     * @param array $args
     *
     * @return \object
     *
     * @throws DependencyInjectionException
     * @throws \ReflectionException
     */
    protected static function getContainerByFactory(
        \ReflectionClass $refClass,
        string $interfaceName,
        string $factoryMethodName,
        array $args = []
    ) : \object {
        if (!$refClass->hasMethod($factoryMethodName)) {
            throw new FactoryMethodNotFoundException(
                "Class without public constructor must have a factory method " . static::FACTORY_METHOD_NAME
            );
        }

        $method = $refClass->getMethod($factoryMethodName);
        if (!$method->isStatic() || !$method->isPublic()) {
            throw new FactoryMethodNotAllowedException();
        }

        if (!$method->getReturnType() || !(($object = $method->invokeArgs(null, $args)) instanceof $interfaceName)) {
            throw new FactoryMethodInvalidException(
                "The object returned by the factory method must implement the interface $interfaceName."
            );
        }

        return $object;
    }
}
