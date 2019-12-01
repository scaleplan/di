<?php

namespace Scaleplan\DependencyInjection\Exceptions;

/**
 * Class ContainerNotImplementsException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerNotImplementsException extends DependencyInjectionException
{
    public const MESSAGE = 'Container ":container" not implements ":interface".';
    public const CODE = 406;

    /**
     * ContainerNotImplementsException constructor.
     *
     * @param $container
     * @param $interface
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($container, $interface, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            strtr($message ?: static::MESSAGE, [':container' => $container, ':interface' => $interface]),
            $code,
            $previous
        );
    }
}
