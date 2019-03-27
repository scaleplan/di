<?php

namespace Scaleplan\DependencyInjection;

use Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;

/**
 * @param string $interfaceName
 * @param array $args
 * @param bool $allowCached
 * @param string|null $factoryMethodName
 *
 * @return mixed
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
    bool $allowCached = true,
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
