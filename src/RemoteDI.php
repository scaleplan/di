<?php

namespace Scaleplan\DependencyInjection;

use Lmc\HttpConstants\Header;
use Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException;
use Scaleplan\DependencyInjection\Exceptions\RemoteUrlInvalidException;
use Scaleplan\DTO\DTO;
use Scaleplan\Http\Request;

/**
 * Class RemoteDI
 *
 * @package Scaleplan\DependencyInjection
 */
class RemoteDI
{
    public const REMOTE_CONTAINER_SIGNATURE = 'token@host:port/url';

    public const REMOTE_CONTAINER_TEMPLATE =
        '/^(?:(.+?)@)?(https?\:\/\/[\w\.-]+?\.[a-z]{2,6}(?:\:\d{2,5})?\/(?:[\w-\/]+))/i';

    /**
     * @var array
     */
    protected static $remoteTokens = [];

    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * @param string $dtoName
     *
     * @return RemoteDI
     *
     * @throws DependencyInjectionException
     * @throws RemoteUrlInvalidException
     */
    public static function getInstance(string $dtoName) : RemoteDI
    {
        if (!static::$instances[$dtoName]) {
            static::$instances[$dtoName] = new static($dtoName);
        }

        return static::$instances[$dtoName];
    }

    /**
     * @param $name
     * @param $value
     */
    public static function addRemoteToken($name, $value) : void
    {
        static::$remoteTokens[$name] = $value;
    }

    /**
     * @var string|null
     */
    protected $token;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $dtoName;

    /**
     * RemoteDI constructor.
     *
     * @param string $dtoName
     *
     * @throws DependencyInjectionException
     * @throws RemoteUrlInvalidException
     */
    protected function __construct(string $dtoName)
    {
        if (!class_exists($dtoName) || !is_subclass_of($dtoName, DTO::class)) {
            throw new DependencyInjectionException(
                "Parameter $dtoName must be class name and this class must extends " . DTO::class . ' class'
            );
        }

        if (empty($signature = DependencyInjection::getContainers()[$dtoName] ?? null)) {
            return null;
        }

        if (preg_match(static::REMOTE_CONTAINER_TEMPLATE, $signature, $matches) === false) {
            throw new RemoteUrlInvalidException();
        }

        if ($matches[2]) {
            $this->token = $matches[1];
            $this->url = $matches[2];
        }

        $this->token = null;
        $this->url = $matches[1];
        $this->dtoName = $dtoName;
    }

    /**
     * @param array $args
     *
     * @return string
     *
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     */
    protected function getContainerContent(array $args = []) : string
    {
        /** @var Request $request */
        static $request;
        if (!$request) {
            $request = new Request($this->url, $args);
            $request->addHeader(Header::AUTHORIZATION, $this->token);
        }

        return $request->send();
    }

    /**
     * @param array $args
     *
     * @return DTO
     *
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     */
    public function getContainer(array $args = []) : DTO
    {
        $response = json_decode($this->getContainerContent($args), true);
        /** @var DTO $dto */
        $dto = new $this->dtoName($response);
        $dto->validate();

        return $dto;
    }

    /**
     * @param string $dtoName
     * @param array $args
     *
     * @return DTO
     *
     * @throws DependencyInjectionException
     * @throws RemoteUrlInvalidException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     */
    public static function getRemoteContainer(string $dtoName, array $args = []) : DTO
    {
        $instance = static::getInstance($dtoName);
        return $instance->getContainer($args);
    }
}