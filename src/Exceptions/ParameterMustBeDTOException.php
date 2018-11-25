<?php

namespace Scaleplan\DependencyInjection\Exceptions;

use Scaleplan\DTO\DTO;

/**
 * Class ParameterMustBeDTOException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ParameterMustBeDTOException extends DependencyInjectionException
{
    public const MESSAGE =
        "First parameter must be class name and this class must extends " . DTO::class . ' class.';
}