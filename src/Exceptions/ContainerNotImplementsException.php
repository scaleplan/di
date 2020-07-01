<?php

namespace Scaleplan\DependencyInjection\Exceptions;

use function Scaleplan\Translator\translate;

/**
 * Class ContainerNotImplementsException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class ContainerNotImplementsException extends DependencyInjectionException
{
    public const MESSAGE = 'di.container-not-implements';
    public const CODE    = 406;

    /**
     * ContainerNotImplementsException constructor.
     *
     * @param $container
     * @param $interface
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     *
     * @throws ContainerTypeNotSupportingException
     * @throws DependencyInjectionException
     * @throws ParameterMustBeInterfaceNameOrClassNameException
     * @throws ReturnTypeMustImplementsInterfaceException
     * @throws \ReflectionException
     */
    public function __construct($container, $interface, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            translate(static::MESSAGE, ['container' => $container, 'interface' => $interface]) ?:
                strtr($message ?: static::MESSAGE, ['container' => $container, 'interface' => $interface]),
            $code,
            $previous
        );
    }
}
