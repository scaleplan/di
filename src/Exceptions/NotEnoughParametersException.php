<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class NotEnoughParametersException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class NotEnoughParametersException extends DependencyInjectionException
{
    public const MESSAGE = 'di.not-enough-parameters';
    public const CODE = 406;
}
