<?php

namespace Scaleplan\DependencyInjection\Exceptions;

use Scaleplan\DTO\DTO;

/**
 * Class ParameterMustBeDTOException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ParameterMustBeInterfaceNameOrClassNameException extends DependencyInjectionException
{
    public const MESSAGE = 'First parameter must be interface name or class name.';
}