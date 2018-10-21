<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
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
     * @param string $interfaceName
     * @param array $args
     *
     * @return null|object
     * @throws DependencyInjectionException
     */
    public static function getContainer(string $interfaceName, array $args = []) : ?object
    {
        if (!interface_exists($interfaceName) || !class_exists($interfaceName)) {
            throw new DependencyInjectionException("Parameter $interfaceName must be interface name or class name");
        }

        if (empty($containerClassName = static::$containers[$interfaceName])) {
            return null;
        }

        try {
            $refClass = new \ReflectionClass($containerClassName);
            if (!$refClass->implementsInterface($interfaceName)) {
                return null;
            }

            return $refClass->newInstanceArgs($args);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}

/**
 * @param string $interfaceName
 * @param array $args
 *
 * @return null|object
 * @throws DependencyInjectionException
 */
function get_container(string $interfaceName, array $args = []) : ?object
{
    return DependencyInjection::getContainer($interfaceName, $args);
}