<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodNotFoundException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodNotFoundException extends DependencyInjectionException
{
    public const MESSAGE = 'Factory method not found.';
}