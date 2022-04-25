<?php

namespace Jsl\Router\Contracts;

interface RouteInterface
{
    /**
     * Get route method
     *
     * @return string
     */
    public function getMethod(): string;


    /**
     * Get route pattern
     *
     * @return string
     */
    public function getPattern(): string;


    /**
     * Get route controller
     *
     * @return array|callable
     */
    public function getController(): array|callable;


    /**
     * Add before middlewares
     *
     * @param array|callable ...$middlewares
     *
     * @return self
     */
    public function addMiddlewares(array|callable ...$middlewares): self;


    /**
     * Get route middlewares
     *
     * @return array
     */
    public function getMiddlewares(): array;


    /**
     * Add arguments
     *
     * @param array $arguments
     * 
     * @return self
     */
    public function addArguments(array $arguments): self;


    /**
     * Get arguments
     *
     * @return array
     */
    public function getArguments(): array;


    /**
     * Set route name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self;
}
