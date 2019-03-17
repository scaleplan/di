<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerNotImplementsException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerNotImplementsException extends DependencyInjectionException
{
    public const MESSAGE = 'Container not implements passed interface.';
}
