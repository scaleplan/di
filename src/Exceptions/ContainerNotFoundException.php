<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerNotFoundException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerNotFoundException extends DependencyInjectionException
{
    public const MESSAGE = 'di.container-not-found';
    public const CODE = 404;
}
