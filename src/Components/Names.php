<?php

namespace Jsl\Router\Components;

use ArgumentCountError;
use InvalidArgumentException;
use Jsl\Router\Contracts\NamesInterface;
use Jsl\Router\Contracts\PlaceholdersInterface;
use Jsl\Router\Exceptions\UnknownNameException;

class Names implements NamesInterface
{
    /**
     * @var array
     */
    protected array $names = [];

    /**
     * @var PlaceholdersInterface
     */
    protected PlaceholdersInterface $placeholders;


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
    public function add(string $name, string $pattern): self
    {
        $this->names[$name] = $pattern;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return key_exists($name, $this->names);
    }


    /**
     * @inheritDoc
     */
    public function get(string $name, array $arguments = []): string
    {
        if ($this->has($name) === false) {
            throw new UnknownNameException("No route named {$name} found");
        }

        $slugs = explode('/', $this->names[$name]);
        $arg = 0;
        foreach ($slugs as $index => $slug) {
            if ($this->placeholders->has($slug)) {
                if (key_exists($arg, $arguments) === false) {
                    throw new ArgumentCountError("Missing argument for named route: {$name}");
                }

                if ($this->placeholders->compare($slug, $arguments[$arg]) === false) {
                    throw new InvalidArgumentException("Invalid argument type for named route: {$name}");
                }

                $slugs[$index] = $arguments[$arg++];
            }
        }

        return implode('/', $slugs);
    }


    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->names;
    }
}
