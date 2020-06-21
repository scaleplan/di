<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ReturnTypeMustImplementsInterfaceException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ReturnTypeMustImplementsInterfaceException extends DependencyInjectionException
{
    public const MESSAGE = 'di.return-not-implements';
    public const CODE = 406;
}
