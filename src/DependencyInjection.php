<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;

/**
 * Class DependencyInjection
 *
 * @package Scaleplan\DependencyInjection
 */
class DependencyInjection
{
    /**
     * @var array
     */
    protected static $containers = [];

    /**
     * @var array
     */
    protected static $cache = [];

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
     * @param string $interfaceName
     * @param array $args
     * @param string $type
     * @param bool $allowCached
     * @param string|null $factoryMethodName
     *
     * @return object|string
     *
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    protected static function getContainer(
        string $interfaceName,
        array $args = [],
        \string $type = 'local',
        \bool $allowCached = true,
        string $factoryMethodName = null
    )
    {
        $cacheKey = "$interfaceName::$factoryMethodName:" . serialize($args);
        if ($allowCached && empty(static::$cache[$cacheKey])) {
            return static::$cache[$cacheKey];
        }

        switch ($type) {
            case 'local':
                $di = new LocalDI($interfaceName, $args, false, $factoryMethodName);
                $container = $di->getContainer();
                break;

            case 'static':
                $di = new LocalDI($interfaceName, $args, true, $factoryMethodName);
                $container = $di->getContainer();
                break;

            default:
                throw new ContainerTypeNotSupportingException();
        }

        if ($container && empty(static::$cache[$cacheKey])) {
            static::$cache[$cacheKey] = $container;
        }

        return $container;
    }

    /**
     * @param string $interfaceName
     * @param array $args
     * @param bool $allowCached
     * @param string|null $factoryMethodName
     *
     * @return object
     *
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public static function getLocalContainer(
        string $interfaceName,
        array $args = [],
        \bool $allowCached = true,
        string $factoryMethodName = null
    )
    {
        return static::getContainer($interfaceName, $args, 'local', $allowCached, $factoryMethodName);
    }

    /**
     * @param string $interfaceName
     *
     * @return string
     *
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public static function getStaticContainer(string $interfaceName) : ?string
    {
        return static::getContainer($interfaceName, [], 'static', false);
    }
}

/**
 * @param string $interfaceName
 * @param array $args
 * @param bool $allowCached
 * @param string|null $factoryMethodName
 *
 * @return object
 *
 * @throws ContainerTypeNotSupportingException
 * @throws DependencyInjectionException
 * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
 * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
 * @throws \ReflectionException
 */
function get_container(
    string $interfaceName,
    array $args = [],
    \bool $allowCached = true,
    string $factoryMethodName = null
)
{
    return DependencyInjection::getLocalContainer($interfaceName, $args, $allowCached, $factoryMethodName);

}

/**
 * @param string $interfaceName
 *
 * @return string
 *
 * @throws ContainerTypeNotSupportingException
 * @throws DependencyInjectionException
 * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
 * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
 * @throws \ReflectionException
 */
function get_static_container(string $interfaceName) : ?string
{
    return DependencyInjection::getStaticContainer($interfaceName);
}
