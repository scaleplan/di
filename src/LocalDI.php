<?php
declare(strict_types=1);

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\ContainerNotImplementsException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodInvalidException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodNotAllowedException;
use Scaleplan\DependencyInjection\Exceptions\FactoryMethodNotFoundException;
use Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException;
use Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException;
use function Scaleplan\Translator\translate;

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
    protected static $containers = [];

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
     * @param array $containers
     */
    public static function init(array $containers) : void
    {
        static::$containers = $containers;
    }

    /**
     * @param array $containers
     */
    public static function addContainers(array $containers) : void
    {
        static::$containers = array_merge(static::$containers, $containers);
    }

    /**
     * @return array
     */
    public static function getContainers() : array
    {
        return static::$containers;
    }

    /**
     * LocalDI constructor.
     *
     * @param string $interfaceName
     * @param array $args
     * @param bool $isStatic
     * @param string|null $factoryMethodName
     *
     * @throws DependencyInjectionException
     * @throws Exceptions\ContainerTypeNotSupportingException
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public function __construct(
        string $interfaceName,
        array $args = [],
        bool $isStatic = false,
        string $factoryMethodName = null
    )
    {
        $this->interfaceName = $interfaceName;
        $this->args = $args;
        $this->isStatic = $isStatic;
        $this->factoryMethodName = $factoryMethodName;

        $this->checkInterface();
        $this->container = static::$containers[$interfaceName] ?? null;
    }

    /**
     * @throws DependencyInjectionException
     * @throws Exceptions\ContainerTypeNotSupportingException
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    protected function checkInterface() : void
    {
        if (!interface_exists($this->interfaceName) && !class_exists($this->interfaceName)) {
            throw new ParameterMustBeInterfaceNameOrClassNameException(
                translate('di.interface-parameter-error', ['interface-name' => $this->interfaceName,])
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
        if (!is_callable($this->container)) {
            return null;
        }

        $refCallable = new \ReflectionFunction($this->container);
        $returnType = $refCallable->getReturnType();
        if ($returnType && ($returnType->allowsNull() || $returnType->isBuiltin())) {
            throw new ReturnTypeMustImplementsInterfaceException();
        }

        $object = $refCallable->invokeArgs($this->args);
        if (!\is_subclass_of($object, $this->interfaceName) && !\is_a($object, $this->interfaceName)) {
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
        $containerData = explode('::', $this->container);
        $containerClassName = $containerData[0] ?? null;
        $containerFactoryMethodName = $containerData[1] ?? null;
        if (!class_exists($containerClassName)
            || (!is_subclass_of($containerClassName, $this->interfaceName)
                && !\is_a($containerClassName, $this->interfaceName))) {
            throw new ContainerNotImplementsException($containerClassName, $this->interfaceName);
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
     * @return callable|mixed|string|null
     *
     * @throws ContainerNotImplementsException
     * @throws DependencyInjectionException
     * @throws Exceptions\ContainerTypeNotSupportingException
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    protected function getContainerFromObject()
    {
        if (!is_object($this->container) || is_callable($this->container)) {
            return null;
        }

        if (!is_subclass_of($this->container, $this->interfaceName) && !\is_a($this->container, $this->interfaceName)) {
            throw new ContainerNotImplementsException($this->container, $this->interfaceName);
        }

        return $this->container;
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

        return $this->getContainerFromObject() ?? $this->getContainerFromCallable() ?? $this->getContainerByClassName();
    }

    /**
     * @param \ReflectionClass $refClass
     * @param string $factoryMethodName
     *
     * @return mixed
     *
     * @throws DependencyInjectionException
     * @throws Exceptions\ContainerTypeNotSupportingException
     * @throws FactoryMethodInvalidException
     * @throws FactoryMethodNotAllowedException
     * @throws FactoryMethodNotFoundException
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    protected function getContainerByFactory(
        \ReflectionClass $refClass,
        string $factoryMethodName
    )
    {
        if (!$refClass->hasMethod($factoryMethodName)) {
            throw new FactoryMethodNotFoundException(
                translate('di.fabric-method-must-be', ['factory-method-name' => static::FACTORY_METHOD_NAME,])
            );
        }

        $method = $refClass->getMethod($factoryMethodName);
        if (!$method->isStatic() || !$method->isPublic()) {
            throw new FactoryMethodNotAllowedException();
        }

        if (!$method->getReturnType()
            || !(($object = $method->invokeArgs(null, $this->args)) instanceof $this->interfaceName)) {
            throw new FactoryMethodInvalidException(
                translate('di.fabric-result-not-implements', ['interface-name' => $this->interfaceName,])
            );
        }

        return $object;
    }
}
