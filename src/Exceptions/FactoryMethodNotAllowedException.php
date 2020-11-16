<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodNotAllowedException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodNotAllowedException extends DependencyInjectionException
{
    public const MESSAGE = 'di.unexpected-fabric-method';
    public const CODE = 403;
}
