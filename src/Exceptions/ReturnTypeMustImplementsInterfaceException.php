<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ReturnTypeMustImplementsInterfaceException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ReturnTypeMustImplementsInterfaceException extends DependencyInjectionException
{
    public const MESSAGE = 'Return type of callable must implements requested interface.';
    public const CODE = 406;
}
