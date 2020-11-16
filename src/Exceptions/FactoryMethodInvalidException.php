<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodInvalidException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodInvalidException extends DependencyInjectionException
{
    public const MESSAGE = 'di.wrong-fabric-method';
    public const CODE = 406;
}
