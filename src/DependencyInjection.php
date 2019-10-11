<?php

namespace Scaleplan\DependencyInjection;

use Psr\Container\ContainerInterface;
use Scaleplan\DependencyInjection\Exceptions\ContainerNotFoundException;
use Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\Helpers\FileHelper;

/**
 * Class DependencyInjection
 *
 * @package Scaleplan\DependencyInjection
 */
class DependencyInjection implements ContainerInterface
{
    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * @param array $containers
     */
    protected static function init(array $containers) : void
    {
        LocalDI::init($containers);
    }

    /**
     * @param array $containers
     */
    public static function addContainers(array $containers) : void
    {
        LocalDI::addContainers($containers);
    }

    /**
     * @param string $dirPath
     */
    public static function loadContainersFromDir(string $dirPath) : void
    {
        foreach (FileHelper::getRecursivePaths($dirPath) as $file) {
            LocalDI::addContainers(include $file);
        }
    }

    /**
     * @return array
     */
    public static function getContainers() : array
    {
        return LocalDI::getContainers();
    }

    /**
     * @param $interfaceName
     * @param array $args
     * @param null $factoryMethodName
     *
     * @return string
     */
    public static function getCacheKey($interfaceName, $args = [], $factoryMethodName = null) : string
    {
        return "$interfaceName::$factoryMethodName:" . serialize($args);
    }

    /**
     * @param $interfaceName
     * @param array $args
     * @param null $factoryMethodName
     */
    public static function removeFromCache($interfaceName, $args = [], $factoryMethodName = null) : void
    {
        unset(static::$cache[static::getCacheKey($interfaceName, $args, $factoryMethodName)]);
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
        string $type = 'local',
        bool $allowCached = true,
        string $factoryMethodName = null
    )
    {
        $cacheKey = null;
        try {
            $cacheKey = static::getCacheKey($interfaceName, $args, $factoryMethodName);
        } catch (\Throwable $e) {
            $allowCached = false;
        }

        if ($allowCached && !empty(static::$cache[$cacheKey])) {
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
     * @return object|null
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
        bool $allowCached = true,
        string $factoryMethodName = null
    ) : ?object
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

    /**
     * @param string $interfaceName
     *
     * @return string
     *
     * @throws ContainerNotFoundException
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public static function getRequiredStaticContainer(string $interfaceName) : string
    {
        if (null === ($container = static::getStaticContainer($interfaceName))) {
            throw new ContainerNotFoundException();
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
    public static function getRequiredLocalContainer(
        string $interfaceName,
        array $args = [],
        bool $allowCached = true,
        string $factoryMethodName = null
    ) : object
    {
        if (null ===($container = static::getLocalContainer($interfaceName, $args, $allowCached, $factoryMethodName))) {
            throw new ContainerNotFoundException();
        }

        return $container;
    }

    /**
     * @param string $interfaceName
     *
     * @return mixed|object
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public function get($interfaceName)
    {
        return static::getRequiredLocalContainer($interfaceName);
    }

    /**
     * @param string $interfaceName
     *
     * @return bool
     *
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public function has($interfaceName) : bool
    {
        return null !== static::getLocalContainer($interfaceName);
    }
}
