<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class RemoteUrlInvalidException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class RemoteUrlInvalidException extends DependencyInjectionException
{
    public const MESSAGE = 'Remote URL is invalid.';
}