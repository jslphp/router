<?php

namespace Jsl\Router\Contracts;

use ArgumentCountError;
use Closure;
use Maer\Router\Exceptions\UnknownNameException;

interface RouterInterface
{
    /**
     * Add a GET route
     *
     * @param string $pattern
     * @param array|callable $controller
     * @param array $options
     *
     * @return RouteInterface
     */
    public function get(string $pattern, array|callable $controller): RouteInterface;


    /**
     * Add a POST route
     *
     * @param string $pattern
     * @param array|callable $controller
     * @param array $options
     *
     * @return RouteInterface
     */
    public function post(string $pattern, array|callable $controller): RouteInterface;


    /**
     * Add a PUT route
     *
     * @param string $pattern
     * @param array|callable $controller
     * @param array $options
     *
     * @return RouteInterface
     */
    public function put(string $pattern, array|callable $controller): RouteInterface;


    /**
     * Add a DELETE route
     *
     * @param string $pattern
     * @param array|callable $controller
     * @param array $options
     *
     * @return RouteInterface
     */
    public function delete(string $pattern, array|callable $controller): RouteInterface;


    /**
     * Add a ANY route
     *
     * @param string $pattern
     * @param array|callable $controller
     * @param array $options
     *
     * @return RouteInterface
     */
    public function any(string $pattern, array|callable $controller): RouteInterface;


    /**
     * Add a route
     *
     * @param string $method
     * @param string $pattern
     * @param array|callable $controller
     * @param array $options
     *
     * @return RouteInterface
     */
    public function addRoute(string $method, string $pattern, array|callable $controller): RouteInterface;


    /**
     * Add a route group
     *
     * @param array $options
     * @param Closure $callback
     *
     * @return self
     */
    public function group(array $options, Closure $callback): self;


    /**
     * Add arguments that will always be the first passed to the controllers
     *
     * @param array $arguments
     *
     * @return self
     */
    public function addFixedArguments(array $arguments = []): self;


    /**
     * Get a named route
     * 
     * @param string $name 
     * @param array $arguments 
     * 
     * @return string 
     * 
     * @throws UnknownNameException if the name doesn't exist
     * @throws ArgumentCountError if we got less arguments than needed
     */
    public function getNamedRoute(string $name, array $arguments = []): string;


    /**
     * Find matching route
     *
     * @param string|null $method
     * @param string|null $path
     *
     * @return RouteInterface
     * 
     * @throws RouteNotFoundException if no pattern match found
     * @throws MethodNotAllowedException if pattern found but with wrong method
     */
    public function find(string $method = null, string $path = null): RouteInterface;


    /**
     * Run the router and execute callback
     *
     * @param string|null $method
     * @param string|null $path
     *
     * @return mixed
     * 
     * @throws RouteNotFoundException if no pattern match found
     * @throws MethodNotAllowedException if pattern found but with wrong method
     */
    public function run(string $method = null, string $path = null): mixed;
}
