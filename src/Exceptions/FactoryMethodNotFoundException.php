<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodNotFoundException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodNotFoundException extends DependencyInjectionException
{
    public const MESSAGE = 'di.fabric-method-not-found';
    public const CODE = 404;
}
