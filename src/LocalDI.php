<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\ContainerNotImplementsException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodInvalidException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodNotAllowedException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodNotFoundException;
use Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException;
use Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException;

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
     * @var array
     */
    protected static $instances = [];

    /**
     * @var string
     */
    protected $interfaceName;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var bool
     */
    protected $isStatic;

    /**
     * @var string
     */
    protected $factoryMethodName;

    /**
     * @var string|callable
     */
    protected $container;

    /**
     * LocalDI constructor.
     *
     * @param string $interfaceName
     * @param array $args
     * @param bool $isStatic
     * @param string|null $factoryMethodName
     *
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     */
    public function __construct(
        string $interfaceName,
        array $args = [],
        \bool $isStatic = false,
        string $factoryMethodName = null
    )
    {
        $this->interfaceName = $interfaceName;
        $this->args = $args;
        $this->isStatic = $isStatic;
        $this->factoryMethodName = $factoryMethodName;

        $this->checkInterface();
        $this->container = DependencyInjection::getContainers()[$interfaceName] ?? null;
    }

    /**
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     */
    protected function checkInterface() : void
    {
        if (!interface_exists($this->interfaceName) || !class_exists($this->interfaceName)) {
            throw new ParameterMustBeInterfaceNameOrClassNameException(
                "Parameter {$this->interfaceName} must be interface name or class name"
            );
        }
    }

    /**
     * @return mixed
     *
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    protected function getContainerFromCallable()
    {
        $refCallable = new \ReflectionFunction($this->container);
        $returnType = $refCallable->getReturnType();
        if ($returnType && ($returnType->allowsNull() || $returnType->isBuiltin())) {
            throw new ReturnTypeMustImplementsInterfaceException();
        }

        $object = $refCallable->invokeArgs($this->args);
        if (!is_subclass_of($object, $this->interfaceName)) {
            throw new ReturnTypeMustImplementsInterfaceException();
        }

        return $object;
    }

    /**
     * @return mixed
     *
     * @throws DependencyInjectionException
     * @throws \ReflectionException
     */
    protected function getContainerByClassName()
    {
        [$containerClassName, $containerFactoryMethodName] = explode('::', $this->container);
        if (!class_exists($containerClassName) || !is_subclass_of($containerClassName, $this->interfaceName)) {
            throw new ContainerNotImplementsException();
        }

        if ($this->isStatic) {
            return $containerClassName;
        }

        $refClass = new \ReflectionClass($containerClassName);
        $factoryMethodName = $this->factoryMethodName ?? $containerFactoryMethodName;
        if ($factoryMethodName) {
            return $this->getContainerByFactory($refClass, $factoryMethodName);
        }

        if ($refClass->isInstantiable()) {
            return $refClass->newInstanceArgs($this->args);
        }

        return $this->getContainerByFactory($refClass, static::FACTORY_METHOD_NAME);
    }

    /**
     * @return object|string
     *
     * @throws DependencyInjectionException
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public function getContainer()
    {
        if (empty($this->container)) {
            return null;
        }

        if (is_callable($this->container)) {
            return $this->getContainerFromCallable();
        }

        return $this->getContainerByClassName();
    }

    /**
     * @param \ReflectionClass $refClass
     * @param string $factoryMethodName
     *
     * @return mixed
     *
     * @throws FactoryMethodInvalidException
     * @throws FactoryMethodNotAllowedException
     * @throws FactoryMethodNotFoundException
     * @throws \ReflectionException
     */
    protected function getContainerByFactory(
        \ReflectionClass $refClass,
        string $factoryMethodName
    )
    {
        if (!$refClass->hasMethod($factoryMethodName)) {
            throw new FactoryMethodNotFoundException(
                'Class without public constructor must have a factory method ' . static::FACTORY_METHOD_NAME
            );
        }

        $method = $refClass->getMethod($factoryMethodName);
        if (!$method->isStatic() || !$method->isPublic()) {
            throw new FactoryMethodNotAllowedException();
        }

        if (!$method->getReturnType()
            || !(($object = $method->invokeArgs(null, $this->args)) instanceof $this->interfaceName)) {
            throw new FactoryMethodInvalidException(
                "The object returned by the factory method must implement the interface {$this->interfaceName}."
            );
        }

        return $object;
    }
}
