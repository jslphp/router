<?php

namespace Jsl\Router\Contracts;

interface GroupsInterface
{
    /**
     * Push new group data from the stack
     *
     * @param string|null $prefix
     * @param array $middlewares
     *
     * @return self
     */
    public function push(?string $prefix = null, array $middlewares = []): self;


    /**
     * Remove the last group fromt he stack and return it
     *
     * @return array
     */
    public function pop(): array;


    /**
     * Decorate middlewares
     *
     * @return array
     */
    public function getMiddlewares(): array;


    /**
     * Decorate prefix
     *
     * @param string $prefix
     *
     * @return string
     */
    public function decoratePrefix(string $prefix): string;
}
