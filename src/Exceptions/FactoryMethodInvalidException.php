<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodInvalidException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodInvalidException extends DependencyInjectionException
{
    public const MESSAGE = 'Неверный фабричный метод.';
    public const CODE = 406;
}
