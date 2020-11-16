<?php

namespace Scaleplan\DependencyInjection\Exceptions;

use function Scaleplan\Translator\translate;

/**
 * Class DependencyInjectionException
 *
 * @package Scaleplan\DependencyInjection\Exceptions
 */
class DependencyInjectionException extends \Exception
{
    public const MESSAGE = 'di.container-error';
    public const CODE = 400;

    /**
     * DependencyInjectionException constructor.
     *
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
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            $message ?: translate(static::MESSAGE) ?: static::MESSAGE,
            $code ?: static::CODE,
            $previous
        );
    }
}
