<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerTypeNotSupportingException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerTypeNotSupportingException extends DependencyInjectionException
{
    public const MESSAGE = 'Container type not supporting.';
    public const CODE = 406;
}
