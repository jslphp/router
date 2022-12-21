<?php

namespace Jsl\Router;

use Closure;
use InvalidArgumentException;
use Jsl\Router\Components\Attributes;
use Jsl\Router\Components\AttributesParser;
use Jsl\Router\Components\Groups;
use Jsl\Router\Components\Names;
use Jsl\Router\Components\Placeholders;
use Jsl\Router\Components\Route;
use Jsl\Router\Components\RouteCollection;
use Jsl\Router\Contracts\GroupsInterface;
use Jsl\Router\Contracts\NamesInterface;
use Jsl\Router\Contracts\PlaceholdersInterface;
use Jsl\Router\Contracts\RouteCollectionInterface;
use Jsl\Router\Contracts\RouteInterface;
use Jsl\Router\Contracts\RouterInterface;

class Router implements RouterInterface
{
    /**
     * @var RouteCollectionInterface
     */
    protected RouteCollectionInterface $collection;

    /**
     * @var NamesInterface
     */
    protected NamesInterface $names;

    /**
     * @var PlaceholdersInterface
     */
    protected PlaceholdersInterface $placeholders;

    /**
     * @var GroupsInterface
     */
    protected GroupsInterface $groups;

    /**
     * @var callable
     */
    protected mixed $classResolver = null;

    /**
     * @var string
     */
    protected string $routeClass;


    /**
     * @param RouteCollectionInterface|null $collection
     * @param NamesInterface|null $names
     * @param PlaceholdersInterface|null $placeholders
     * @param GroupsInterface|null $groups
     * @param string $routeClass
     */
    public function __construct(
        RouteCollectionInterface $collection = null,
        NamesInterface $names = null,
        PlaceholdersInterface $placeholders = null,
        GroupsInterface $groups = null,
        string $routeClass = Route::class
    ) {
        $this->placeholders = $placeholders ?? new Placeholders;
        $this->names = $names ?? new Names($this->placeholders);
        $this->collection = $collection ?? new RouteCollection($this->placeholders);
        $this->groups = $groups ?? new Groups;

        // Check if the route class implements the RouteInterface
        $interfaces = class_implements($routeClass);
        if (empty($interfaces) || in_array(RouteInterface::class, $interfaces) === false) {
            throw new InvalidArgumentException("The route class must implement " . RouteInterface::class);
        }

        $this->routeClass = $routeClass;

        // Set defaut class resolver
        $this->classResolver = fn ($class) => new $class;
    }


    /**
     * @inheritDoc
     */
    public function get(string $path, array|callable $controller): RouteInterface
    {
        return $this->addRoute('GET', $path, $controller);
    }


    /**
     * @inheritDoc
     */
    public function post(string $path, array|callable $controller): RouteInterface
    {
        return $this->addRoute('POST', $path, $controller);
    }


    /**
     * @inheritDoc
     */
    public function put(string $path, array|callable $controller): RouteInterface
    {
        return $this->addRoute('PUT', $path, $controller);
    }


    /**
     * @inheritDoc
     */
    public function delete(string $path, array|callable $controller): RouteInterface
    {
        return $this->addRoute('DELETE', $path, $controller);
    }


    /**
     * @inheritDoc
     */
    public function any(string $path, array|callable $controller): RouteInterface
    {
        return $this->addRoute('ANY', $path, $controller);
    }


    /**
     * @inheritDoc
     */
    public function addRoute(string $method, string $path, array|callable $controller): RouteInterface
    {
        // Add group prefixes
        $path = $this->groups->decoratePrefix($path);

        /**
         * @var RouteInterface
         */
        $route = new $this->routeClass($method, $path, $controller, $this->names);

        // Add group middlewares
        if ($middlewares = $this->groups->getMiddlewares()) {
            $route->addMiddlewares(...$middlewares);
        }

        $this->collection->add($route);

        return $route;
    }


    /**
     * @inheritDoc
     */
    public function addRoutesFromArray(array $groups): self
    {
        foreach ($groups as $groupInfo) {
            $group = [
                'prefix' => $groupInfo['prefix'] ?? '',
                'middlewares' => $groupInfo['middlewares'] ?? [],
            ];

            $this->group($group, function (Router $router) use ($groupInfo) {
                foreach ($groupInfo['routes'] ?? [] as $route) {
                    $item = $router->addRoute(
                        $route['method'] ?? 'GET',
                        $route['path'] ?? '',
                        $route['controller'] ?? null
                    );

                    if ($route['name'] ?? null) {
                        $item->setName($route['name']);
                    }

                    if ($route['middlewares'] ?? null) {
                        $item->addMiddlewares($route['middlewares']);
                    }
                }
            });
        }
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function addRoutesFromClassAttributes(string|array $class): self
    {
        $this->addRoutesFromArray(
            (new AttributesParser((array)$class))->getResult()
        );

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function group(array $options, Closure $callback): self
    {
        if (isset($options['middlewares']) && is_string($options['middlewares'])) {
            $options['middlewares'] = explode('|', $options['middlewares']);
        }

        $this->groups->push($options['prefix'] ?? null, $options['middlewares'] ?? []);

        call_user_func_array($callback, [$this]);

        $this->groups->pop();

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function addFixedArguments(array $arguments = []): self
    {
        $this->collection->addFixedArguments($arguments);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getNamedRoute(string $name, array $arguments = []): string
    {
        return $this->names->get($name, $arguments);
    }


    /**
     * Set function for instantiating callback classes
     *
     * @param callable $classResolver
     *
     * @return self
     */
    public function setClassResolver(callable $classResolver): self
    {
        $this->classResolver = $classResolver;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function find(string $method = null, string $path = null): RouteInterface
    {
        $method = strtoupper($method ?? $_SERVER['REQUEST_METHOD']);
        $path   = '/' . trim(strtok($path ?? $_SERVER['REQUEST_URI'], '?'), '/ ');

        return $this->collection->find($method, $path);
    }


    /**
     * @inheritDoc
     */
    public function run(?string $method = null, ?string $path = null): mixed
    {
        $route = $this->find($method, $path);
        $arguments = $route->getArguments();

        foreach ($route->getMiddlewares() as $mw) {
            $mw = $this->resolveCallback($mw);

            if (call_user_func_array($mw, $arguments) === false) {
                return null;
            }
        }

        $controller = $route->getController();
        $controller = $this->resolveCallback($controller);

        return call_user_func_array($controller, $arguments);
    }


    /**
     * Resolve callback
     *
     * @param array|callable $callback
     *
     * @return array|callable
     */
    protected function resolveCallback(array|callable $callback): array|callable
    {
        if (is_callable($callback) === false) {
            if (is_string($callback) && class_exists($callback)) {
                // Since it's a string with a valid class name, instantiate it
                $callback = call_user_func_array($this->classResolver, [$callback]);
            } else if (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && class_exists($callback[0])) {
                // Since it's an array containing two strings, instantiate the first element
                $callback[0] = call_user_func_array($this->classResolver, [$callback[0]]);
            }
        }

        return $callback;
    }
}
