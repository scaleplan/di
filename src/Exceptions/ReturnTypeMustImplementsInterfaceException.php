<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ReturnTypeMustImplementsInterfaceException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ReturnTypeMustImplementsInterfaceException extends DependencyInjectionException
{
    public const MESSAGE = 'Возвращаемое значение должно соответвовать запрашиваемому интерфейсу или классу.';
    public const CODE = 406;
}
