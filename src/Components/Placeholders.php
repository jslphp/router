<?php

namespace Jsl\Router\Components;

use Jsl\Router\Contracts\PlaceholdersInterface;

class Placeholders implements PlaceholdersInterface
{
    /**
     * @var array
     */
    protected array $placeholders = [];


    public function __construct()
    {
        $this->add('?', '?');
        $this->add('(:num)', '([\d]+)');
        $this->add('(:hex)', '([a-fA-F0-9]+)');
        $this->add('(:any)', '([^\/]+)');
        $this->add('(:all)', '(.*)');
    }


    /**
     * @inheritDoc
     */
    public function add(string $placeholder, string $regex, bool $isQuoted = false): self
    {
        if ($isQuoted === false) {
            $placeholder = preg_quote($placeholder, '#');
        }

        $this->placeholders[$placeholder] = $regex;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function has(string $placeholder, bool $isQuoted = false): bool
    {
        if ($isQuoted === false) {
            $placeholder = preg_quote($placeholder, '#');
        }

        return key_exists($placeholder, $this->placeholders);
    }


    /**
     * @inheritDoc
     */
    public function get(string $placeholder, bool $isQuoted = false): string
    {
        if ($isQuoted === false) {
            $placeholder = preg_quote($placeholder, '#');
        }

        return $this->placeholders[$placeholder] ?? $placeholder;
    }


    /**
     * @inheritDoc
     */
    public function compare(string $placeholder, string $value, bool $isQuoted = false): bool
    {
        $pattern = $this->get($placeholder, $isQuoted);

        if ($pattern === $placeholder) {
            return true;
        }

        return preg_match("#^{$pattern}$#", $value) === 1;
    }


    /**
     * @inheritDoc
     */
    public function regexify(string $string, bool $isQuoted = false): string
    {
        if ($isQuoted === false) {
            $string = preg_quote($string, '#');
        }

        // Replace placeholders and ?
        $string = str_replace(
            array_keys($this->placeholders),
            array_values($this->placeholders),
            $string
        );

        // Set / before optional params as optional as well
        return preg_replace('#/\(([^/]+)\)\?#', '/?+($1)?', $string);
    }
}
