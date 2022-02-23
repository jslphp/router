<?php

namespace Jsl\Router\Components;

use Jsl\Router\Contracts\GroupsInterface;

class Groups implements GroupsInterface
{
    /**
     * @var array
     */
    protected array $groups = [];

    /**
     * Generated on push and pop
     * 
     * @var string
     */
    protected string $prefixes = '';

    /**
     * Generated on push and pop
     * 
     * @var array
     */
    protected array $middlewares = [];


    /**
     * @inheritDoc
     */
    public function push(?string $prefix = null, array $middlewares = []): self
    {
        $this->groups[] = [
            'prefix' => $prefix,
            'middlewares' => $middlewares,
        ];

        $this->generate();

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function pop(): array
    {
        $group = array_pop($this->groups);

        $this->generate();

        return $group;
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
    public function decoratePrefix(string $prefix): string
    {
        $prefix = trim($prefix, '/ ');
        return '/' . trim($this->prefixes, '/ ') . '/' . $prefix;
    }


    /**
     * Generate the group data for faster access
     *
     * @return void
     */
    protected function generate(): void
    {
        // Prefixex
        $prefixes = array_column($this->groups, 'prefix');
        $prefixes = array_filter($prefixes);

        $this->prefixes = $prefixes
            ? '/' . trim(implode('/', $prefixes), '/ ')
            : '';

        // Middlewares
        $this->middlewares = array_merge(...array_column($this->groups, 'middlewares'));
    }
}
