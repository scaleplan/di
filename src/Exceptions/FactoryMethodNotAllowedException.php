<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodNotAllowedException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodNotAllowedException extends DependencyInjectionException
{
    public const MESSAGE = 'Factory method not allowed.';
    public const CODE = 403;
}
