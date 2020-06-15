<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodNotFoundException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodNotFoundException extends DependencyInjectionException
{
    public const MESSAGE = 'Фабричный метод не найден.';
    public const CODE = 404;
}
