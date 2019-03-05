<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\DTO\DTO;

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
    public static function init(array $containers)
    {
        static::$containers = $containers;
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
     * @return \object|null|DTO
     *
     * @throws DependencyInjectionException
     * @throws Exceptions\RemoteUrlInvalidException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     */
    protected static function getContainer(
        string $interfaceName,
        array $args = [],
        \string $type = 'local',
        \bool $allowCached = true,
        string $factoryMethodName = null
    ) : ?\object {
        $cacheKey = "$interfaceName::$factoryMethodName:" . serialize($args);
        if (empty(static::$cache[$cacheKey]) && $allowCached) {
            return static::$cache[$cacheKey];
        }

        switch ($type) {
            case 'local':
                $container = LocalDI::getLocalContainer($interfaceName, $args, false, $factoryMethodName);
                break;

            case 'remote':
                $container = RemoteDI::getRemoteContainer($interfaceName, $args);
                break;

            case 'static':
                $container = LocalDI::getLocalContainer($interfaceName, $args, true, $factoryMethodName);
                break;

            default:
                throw new ContainerTypeNotSupportingException();
        }

        if (empty(static::$cache[$cacheKey]) && $container) {
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
     * @return \object|null
     *
     * @throws DependencyInjectionException
     * @throws Exceptions\RemoteUrlInvalidException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     */
    public static function getLocalContainer(
        string $interfaceName,
        array $args = [],
        \bool $allowCached = true,
        string $factoryMethodName = null
    ) : ?\object {
        return static::getContainer($interfaceName, $args, 'local', $allowCached, $factoryMethodName);
    }

    /**
     * @param string $dtoName
     * @param array $args
     * @param bool $allowCached
     *
     * @return DTO|null
     *
     * @throws DependencyInjectionException
     * @throws Exceptions\RemoteUrlInvalidException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     */
    public static function getRemoteContainer(string $dtoName, array $args = [], $allowCached = true) : ?DTO
    {
        return static::getContainer($dtoName, $args, 'remote', $allowCached);
    }

    /**
     * @param string $interfaceName
     *
     * @return string|null
     *
     * @throws DependencyInjectionException
     * @throws Exceptions\RemoteUrlInvalidException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
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
 * @return \object|null
 *
 * @throws DependencyInjectionException
 * @throws Exceptions\RemoteUrlInvalidException
 * @throws \ReflectionException
 * @throws \Scaleplan\DTO\Exceptions\ValidationException
 * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
 */
function get_local_container(
    string $interfaceName,
    array $args = [],
    \bool $allowCached = true,
    string $factoryMethodName = null
) : ?\object
{
    return DependencyInjection::getLocalContainer($interfaceName, $args, $allowCached, $factoryMethodName);

}

/**
 * @param string $dtoName
 * @param array $args
 * @param bool $allowCached
 *
 * @return DTO
 *
 * @throws DependencyInjectionException
 * @throws Exceptions\RemoteUrlInvalidException
 * @throws \ReflectionException
 * @throws \Scaleplan\DTO\Exceptions\ValidationException
 * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
 */
function get_remote_container(string $dtoName, array $args = [], bool $allowCached = true) : ?DTO
{
    return DependencyInjection::getRemoteContainer($dtoName, $args, $allowCached);
}

/**
 * @param string $interfaceName
 *
 * @return string|null
 *
 * @throws DependencyInjectionException
 * @throws Exceptions\RemoteUrlInvalidException
 * @throws \ReflectionException
 * @throws \Scaleplan\DTO\Exceptions\ValidationException
 * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
 */
function get_static_container(string $interfaceName) : ?string
{
    return DependencyInjection::getStaticContainer($interfaceName);
}
