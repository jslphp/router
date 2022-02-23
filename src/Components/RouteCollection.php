<?php

namespace Jsl\Router\Components;

use Jsl\Router\Contracts\PlaceholdersInterface;
use Jsl\Router\Contracts\RouteCollectionInterface;
use Jsl\Router\Contracts\RouteInterface;
use Jsl\Router\Exceptions\MethodNotAllowedException;
use Jsl\Router\Exceptions\RouteNotFoundException;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var array
     */
    protected array $routes = [];

    /**
     * @var PlaceholdersInterface
     */
    protected PlaceholdersInterface $placeholders;

    /**
     * @var array
     */
    protected array $fixedArguments = [];


    /**
     * @param PlaceholdersInterface $placeholders
     */
    public function __construct(PlaceholdersInterface $placeholders)
    {
        $this->placeholders = $placeholders;
    }


    /**
     * @inheritDoc
     */
    public function add(RouteInterface $route): RouteInterface
    {
        $this->routes[$route->getPattern()][$route->getMethod()] = $route;

        return $route;
    }


    /**
     * @inheritDoc
     */
    public function addFixedArguments(array $arguments = []): self
    {
        $this->fixedArguments = $arguments;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function find(string $method, string $path): RouteInterface
    {
        if (isset($this->routes[$path][$method])) {
            return $this->routes[$path][$method];
        }

        $wrongMethod = false;

        foreach ($this->routes as $pattern => $methods) {
            $pattern = $this->placeholders->regexify($pattern, false);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $path, $args) === 1) {
                $arguments = array_merge($this->fixedArguments, array_slice($args, 1));

                if (key_exists($method, $methods)) {
                    return $methods[$method]->addArguments($arguments);
                }

                if (key_exists('ANY', $methods)) {
                    return $methods['ANY']->addArguments($arguments);
                }

                $wrongMethod = true;
            }
        }

        if ($wrongMethod) {
            throw new MethodNotAllowedException("Method not allowed");
        }

        throw new RouteNotFoundException("Route not found");
    }
}
