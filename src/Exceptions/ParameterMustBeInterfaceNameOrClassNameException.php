<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ParameterMustBeDTOException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ParameterMustBeInterfaceNameOrClassNameException extends DependencyInjectionException
{
    public const MESSAGE = 'Первый параметр должен быть именем интерфейса или именем класса.';
    public const CODE = 406;
}
