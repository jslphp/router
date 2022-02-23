<?php

namespace Jsl\Router\Contracts;

use Jsl\Router\Exceptions\MethodNotAllowedException;
use Jsl\Router\Exceptions\RouteNotFoundException;

interface RouteCollectionInterface
{
    /**
     * Add a route to the collection
     *
     * @param RouteInterface $route
     *
     * @return self
     */
    public function add(RouteInterface $route): RouteInterface;


    /**
     * Add arguments that will always be the first passed to the controllers
     *
     * @param array $arguments
     *
     * @return self
     */
    public function addFixedArguments(array $arguments = []): self;


    /**
     * Find a matched route
     *
     * @param string $method
     * @param string $pattern
     *
     * @return RouteInterface
     * 
     * @throws RouteNotFoundException if no pattern match found
     * @throws MethodNotAllowedException if pattern found but with wrong method
     * 
     */
    public function find(string $method, string $pattern): RouteInterface;
}
