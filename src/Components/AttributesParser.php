<?php

namespace Jsl\Router\Components;

use Jsl\Router\Attributes\JslRoute;
use Jsl\Router\Attributes\JslRouteGroup;
use Jsl\Router\Contracts\AttributesParserInterface;
use ReflectionClass;
use ReflectionMethod;

class AttributesParser implements AttributesParserInterface
{
    /**
     * @var array
     */
    protected array $groups = [];


    /**
     * @param array $classes
     */
    public function __construct(array $classes)
    {
        $this->groups = $this->getClassAttributes((array)$classes);
    }


    /**
     * @inheritDoc
     */
    public function getResult(): array
    {
        return $this->groups;
    }


    /**
     * Get routes from class attributes
     *
     * @param string|array $class
     *
     * @return array
     */
    protected function getClassAttributes(string|array $class): array
    {
        $groups = [];

        foreach ((array)$class as $className) {
            $reflectClass = new ReflectionClass($className);
            $groups = array_merge($groups, $this->getGroup($reflectClass));
        }

        return $groups;
    }


    /**
     * Get group info from the class
     *
     * @param ReflectionClass $reflectClass
     *
     * @return array
     */
    protected function getGroup(ReflectionClass $reflectClass): array
    {
        $groups = [];

        foreach ($reflectClass->getAttributes(JslRouteGroup::class) as $attribute) {
            $group = [
                'prefix' => '',
                'middlewares' => null,
                'routes' => []
            ];

            $args = $attribute->getArguments();

            $group['prefix'] = $args['prefix'] ?? '';
            $group['middlewares'] = $args['middlewares'] ?? [];

            foreach ($reflectClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectMethod) {
                $group['routes'] = array_merge($group['routes'], $this->getRoutes($reflectMethod));
            }

            if ($group['routes']) {
                $groups[] = $group;
            }
        }

        return $groups;
    }


    /**
     * Get all routes for the method
     *
     * @param ReflectionMethod $reflectMethod
     *
     * @return array
     */
    protected function getRoutes(ReflectionMethod $reflectMethod): array
    {
        $routes = [];

        foreach ($reflectMethod->getAttributes(JslRoute::class) as $attribute) {
            $args = $attribute->getArguments();

            if (!key_exists('path', $args)) {
                // Empty path, skip
                continue;
            }

            $routes[] = [
                'method' => strtoupper($args['method'] ?? 'GET'),
                'path' => $args['path'],
                'controller' => [
                    $reflectMethod->getDeclaringClass()->name,
                    $reflectMethod->name
                ],
                'name' => $args['name'] ?? null,
                'middlewares' => $args['middlewares'] ?? [],
            ];
        }

        return $routes;
    }


    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->groups;
    }
}
