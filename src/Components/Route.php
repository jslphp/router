<?php

namespace Jsl\Router\Components;

use Jsl\Router\Contracts\NamesInterface;
use Jsl\Router\Contracts\RouteInterface;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    protected string $method;

    /**
     * @var string
     */
    protected string $pattern;

    /**
     * @var array|callable
     */
    protected mixed $controller;

    /**
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * @var NamesInterface
     */
    protected ?NamesInterface $names = null;


    /**
     * @param string $method
     * @param string $pattern
     * @param array|callable $controller
     */
    public function __construct(string $method, string $pattern, array|callable $controller, NamesInterface $names = null)
    {
        $this->method = strtoupper($method);
        $this->pattern = '/' . trim($pattern, '/ ');
        $this->controller = $controller;
        $this->names = $names;
    }


    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }


    /**
     * @inheritDoc
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }


    /**
     * @inheritDoc
     */
    public function getController(): array|callable
    {
        return $this->controller;
    }


    /**
     * @inheritDoc
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }


    /**
     * @inheritDoc
     */
    public function addMiddlewares(array|callable ...$middlewares): self
    {
        if (empty($middlewares)) {
            return $this;
        }

        $this->middlewares = array_merge($this->middlewares, $middlewares);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function addArguments(array $arguments): self
    {
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }


    /**
     * @inheritDoc
     */
    public function setName(string $name): self
    {
        if ($this->names) {
            $this->names->add($name, $this->pattern);
        }

        return $this;
    }
}
