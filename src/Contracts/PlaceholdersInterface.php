<?php

namespace Jsl\Router\Contracts;

interface PlaceholdersInterface
{
    /**
     * Add a placeholder
     *
     * @param string $placeholder
     * @param string $regex
     *
     * @return self
     */
    public function add(string $placeholder, string $regex): self;


    /**
     * Check if a placeholder exists
     *
     * @param string $placeholder
     *
     * @return bool
     */
    public function has(string $placeholder): bool;


    /**
     * Get regex for a placeholder
     *
     * @param string $placeholder
     *
     * @return string Returns the placeholder if it's not found in the stack
     */
    public function get(string $placeholder): string;


    /**
     * Compare a value against a placeholder
     *
     * @param string $placeholder
     * @param string $value
     * @param bool $isQuoted
     *
     * @return bool
     */
    public function compare(string $placeholder, string $value, bool $isQuoted = false): bool;


    /**
     * Replace placeholders with regex
     *
     * @param string $string
     * @param bool $isQuoted true if stirng is already preg_quote'd (defaults to false)
     *
     * @return string
     */
    public function regexify(string $string, bool $isQuoted = false): string;
}
