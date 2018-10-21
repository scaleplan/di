<?php

namespace Scaleplan\DependencyInjection;

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
     */
    public static function getContainer(string $interfaceName, array $args = []) : ?object
    {
        if (empty($containerClassName = static::$containers[$interfaceName])) {
            return null;
        }

        try {
            return (new \ReflectionClass($containerClassName))->newInstanceArgs($args);
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
 */
function get_container(string $interfaceName, array $args = []) : ?object
{
    return DependencyInjection::getContainer($interfaceName, $args);
}