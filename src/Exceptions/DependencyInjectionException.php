<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class DependencyInjectionException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class DependencyInjectionException extends \Exception
{
    public const MESSAGE = 'Ошибка контейнера.';
    public const CODE = 400;

    /**
     * DependencyInjectionException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: static::MESSAGE, $code ?: static::CODE, $previous);
    }
}
