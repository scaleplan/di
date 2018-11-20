<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\DTO\DTO;
use Symfony\Component\Yaml\Yaml;

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
     * @param string $configPath
     */
    public static function init(string $configPath)
    {
        static::$containers = Yaml::parse(file_get_contents($configPath));
    }

    /**
     * @return array
     */
    public static function getContainers() : array
    {
        return static::$containers;
    }
}

/**
 * @param string $interfaceName
 * @param array $args
 * @param string|null $fabricMethodName
 *
 * @return null|object
 *
 * @throws DependencyInjectionException
 * @throws \ReflectionException
 */
function get_local_container(string $interfaceName, array $args = [], string $fabricMethodName = null) : ?object
{
    return LocalDI::getLocalContainer($interfaceName, $args, $fabricMethodName);
}

/**
 * @param string $dtoName
 * @param array $args
 *
 * @return DTO
 *
 * @throws DependencyInjectionException
 * @throws Exceptions\RemoteUrlInvalidException
 * @throws \Scaleplan\DTO\Exceptions\ValidationException
 * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
 */
function get_remote_container(string $dtoName, array $args = []) : DTO
{
    return RemoteDI::getRemoteContainer($dtoName, $args);
}