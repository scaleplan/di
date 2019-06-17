<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ParameterMustBeDTOException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ParameterMustBeInterfaceNameOrClassNameException extends DependencyInjectionException
{
    public const MESSAGE = 'First parameter must be interface name or class name.';
    public const CODE = 406;
}
