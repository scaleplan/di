<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerTypeNotSupportingException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerTypeNotSupportingException extends DependencyInjectionException
{
    public const MESSAGE = 'Такой тип контейнера не поддерживается.';
    public const CODE = 406;
}
