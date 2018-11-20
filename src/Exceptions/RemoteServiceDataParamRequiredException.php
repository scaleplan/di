<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class RemoteServiceDataParamRequiredException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class RemoteServiceDataParamRequiredException extends DependencyInjectionException
{
    public const MESSAGE = 'Remote service data parameter required.';
}