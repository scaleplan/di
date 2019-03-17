<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class DependencyInjectionException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class DependencyInjectionException extends \Exception
{
    public const MESSAGE = 'Container error.';

    /**
     * DependencyInjectionException constructor.
     *
     * @param string|null $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = null, \int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?? static::MESSAGE, $code, $previous);
    }
}
