<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerTypeNotSupportingException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerTypeNotSupportingException extends DependencyInjectionException
{
    public const MESSAGE = 'di.container-type-not-supported';
    public const CODE = 406;
}
