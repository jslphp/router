<?php

namespace Jsl\Router\Contracts;

use ArgumentCountError;
use Jsl\Router\Exceptions\UnknownNameException;

interface NamesInterface
{
    /**
     * Add a named pattern
     *
     * @param string $name
     * @param string $pattern
     *
     * @return self
     */
    public function add(string $name, string $pattern): self;


    /**
     * Check if a named pattern exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;


    /**
     * Get a named pattern
     *
     * @param string $name
     * @param array $arguments
     * 
     * @return string
     * 
     * @throws UnknownNameException if the name doesn't exist
     * @throws ArgumentCountError if we got less arguments than needed
     */
    public function get(string $name, array $arguments = []): string;


    /**
     * Get list of all named patterns
     *
     * @return array
     */
    public function all(): array;
}
