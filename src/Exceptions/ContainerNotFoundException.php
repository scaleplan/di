<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerNotFoundException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerNotFoundException extends DependencyInjectionException
{
    public const MESSAGE = 'Контейнер не найден.';
    public const CODE = 404;
}
