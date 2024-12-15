<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router {
    protected $routes = [];

    /**
     * Add a new route
     */
    public function registerRoute(string $method,string $uri,string $action, array $middleware = []):void
    {
        list($controller, $controllerMethod) = explode('@', $action);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware
        ];
    }

    /**
     * Add a GET route
     * 
     * @param string $uri
     * @param string $action
     * @return void
     */
    public function get(string $uri,string $action, array $middleware = []):void 
    {
        $this->registerRoute('GET', $uri, $action, $middleware);
    }

    /**
     * Add a POST route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post(string $uri,string $controller, array $middleware = []):void 
    {
        $this->registerRoute('POST', $uri, $controller, $middleware);
    }

    /**
     * Add a PUT route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put(string $uri,string $controller,array $middleware = []):void 
    {
        $this->registerRoute('PUT', $uri, $controller, $middleware);
    }

    /**
     * Add a DELETE route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete(string $uri,string $controller, array $middleware = []):void 
    {
        $this->registerRoute('DELETE', $uri, $controller, $middleware);
    }


    /**
     * Route the request
     * 
     * @param string $uri
     * @return void
     */
    public function route(string $uri):void 
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Check for _method input
        if('POST' === $requestMethod && isset($_POST['_method'])) {
            // Override the request method with the value of _method
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach($this->routes as $route) {

            // Split the current URI into segments
            $uriSegments = explode('/', trim($uri, '/'));

            // Split the route URI into segments
            $routeSegments = explode('/', trim($route['uri'], '/'));

            $match = true;

            // Check if the number of segments matches
            if(count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
                $params = [];

                $match = true;

                for($i = 0; $i < count($uriSegments); $i++) {
                    // If the URI do not match and there is no param
                    if(($routeSegments[$i] !== $uriSegments[$i]) && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }

                    // Check for the param and add to $params array
                    if(preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }

                if($match) {
                    foreach($route['middleware'] as $role) {
                        (new Authorize())->handle($role);
                    }

                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    //Instantiate the controller and call the method
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params);
                    return;
                }
            }

            // if($route['uri'] === $uri && $route['method'] === $method) {
            //     //Extract Controller and ControllerMethod
            //     $controller = 'App\\Controllers\\' . $route['controller'];
            //     $controllerMethod = $route['controllerMethod'];

            //     //Instantiate the controller and call the method
            //     $controllerInstance = new $controller();
            //     $controllerInstance->$controllerMethod();
            //     return;
            // };
        }
        ErrorController::notFound();
    }
}