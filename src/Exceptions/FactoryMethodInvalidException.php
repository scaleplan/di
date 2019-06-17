<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodInvalidException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodInvalidException extends DependencyInjectionException
{
    public const MESSAGE = 'Factory method is invalid.';
    public const CODE = 406;
}
