# Router

### Quick example

```php
// Load composers autoloader
require __DIR__ '/path/to/vendor/autoload.php';

// Import and instantiate the router
use Jsl\Router\Router;

$router = new Router;

$router->get('/some/path', function () {
    return 'Response for URL /some/path';
});

// Run the router and get the response from the callback
$response = $router->run();

echo $response; 
// If the URL is /some/path, then you'll see "Response for URL /some/path"

```

### Adding routes
Different ways of adding routes 

```php
// Add a GET route
$router->get('/foo', function () {
    return 'This is the callback for GET /foo';
});

// Add a POST route
$router->post('/foo', function () {
    return 'This is the callback for POST /foo';
});

//...same for put(), delete()

// Allow any verb
$router->any('/foo', function () {
    return 'This is the callback for any verb on /foo';
});

// Add a route for some other verb (PATCH is just an example)
$router->addRoute('PATCH', '/foo', function () {
    return 'This is the callback for PATCH /foo';
});
```

### Adding routes using attributes
```php
// Add attributes to the class

// Add optional group info, like a prefix for all routes in the class
#[JslRouteGroup(prefix: '/foo')]
class SomeClass
{
    // Add a route for this method
    #[JslRoute(
        method: 'GET',
        path: '/bar',
        name: 'someName'
    )]
    public function someMethod()
    {
        return 'Callback for route /foo/bar';
    }
}


// Pass the class name to the router
$router->addRoutesFromClassAttributes(SomeClass::class);

// Pass a list of class names
$router->addRoutesFromClassAttributes([
    SomeClass::class,
    SomeOtherClass::class,
]);

```

### Callbacks
Route callbacks comes in different shapes and sizes

```php
// Closure
$route->get('/foo', function () { ... });

// Passing class name and method name - The router will instatiate the class on run()
$router->get('/foo', ['Some\ClassName', 'someMethod']);

// Passing instance and method name
$router->get('/foo', [$somClass, 'someMethod']);

// ...you can use any callable as callback
```

### Route parameters
Add dynamic route parameters

```php
// (:num) - Matches only numbers (uses regex: [\d]+)
$router->get('/foo/(:num)', function ($param) {
    return "This is a route param: {$param}";
});

// (:hex) - Matches only hexadecimal characters (uses regex: [a-fA-F0-9]+)
$router->get('/foo/(:hex)', function ($param) {
    return "This is a route param: {$param}";
});

// (:any) - Matches any characters (up to the next /) (uses regex: [^\/]+)
$router->get('/foo/(:num)', function ($param) {
    return "This is a route param: {$param}";
});

// (:all) - Matches everything (including /) (uses regex: .*)
$router->get('/foo/(:num)', function ($param) {
    return "This is a route param: {$param}";
});

// Make a dynamic paramter optional by adding ?
$router->get('/foo/(:num)?', function ($param = 123) {
    return "This is a route param: {$param} or 123 if no param was passed";
});
```

### Group routes

```php
// Add a prefix to all routes in the group
$router->group(['prefix' => '/foo'], function (Router $router) {
    // This will add a route for /foo/bar
    $router->get('/bar', function () { ... });

    // This will add a route for /foo/example
    $router->get('/example', function () { ... });
});

// Use dynamic parameters in the prefix, just like in the routes
$router->group(['prefix' => '/foo/(:num)'], function (Router $router) {
    // This will add a route for /foo/(:num)/bar
    $router->get('/bar', function () { ... });
})
```

### Named routes
Instead of remembering all URLs and manually keep updating/syncing links, you can use named routes

```php
// Add a route and give it a name
$router->get('/foo', function () { ... })->setName('something-foo');

// Get the route for a specific name
$path = $router->getNamedRoute('something-foo'); 
// Returns: /foo

// Add a route with dynamic parameters and give it a name
$router->get('/foo/(:num)', function () { ... })->setName('something-foo');

// Get the route for a specific name with dynamic parameters
// Pass the values for each dynamic route parameter as an array
$path = $router->getNamedRoute('something-foo', [123]); 
// Returns: /foo/123
```
