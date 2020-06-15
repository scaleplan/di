<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class FactoryMethodNotAllowedException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class FactoryMethodNotAllowedException extends DependencyInjectionException
{
    public const MESSAGE = 'Фабричный метод не ожидается здесь.';
    public const CODE = 403;
}
