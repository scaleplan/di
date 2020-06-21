<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ParameterMustBeDTOException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ParameterMustBeInterfaceNameOrClassNameException extends DependencyInjectionException
{
    public const MESSAGE = 'di.first-parameter-error';
    public const CODE = 406;
}
