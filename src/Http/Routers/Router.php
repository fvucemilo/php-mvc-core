<?php

namespace fvucemilo\phpmvc\Http\Routers;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\Exceptions\ForbiddenException;
use fvucemilo\phpmvc\Exceptions\NotFoundException;
use fvucemilo\phpmvc\Http\Requests\Request;
use fvucemilo\phpmvc\Http\Responses\Response;
use fvucemilo\phpmvc\MVC\Controllers\Controller;

/**
 * The Router class handles HTTP request routing and mapping to their corresponding callbacks.
 */
class Router
{
    /**
     * @var Request The current HTTP request object.
     */
    public Request $request;

    /**
     * @var Response The current HTTP response object.
     */
    public Response $response;

    /**
     * @var array The mapping of HTTP methods to URLs and their corresponding callbacks.
     */
    private array $routeMap = [];

    /**
     * Router constructor.
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Registers a callback function for handling HTTP GET requests to a specific URL.
     *
     * @param string $url The URL to register the callback function to.
     * @param callable $callback The callback function to handle the HTTP GET request.
     *
     * @return void
     */
    public function get(string $url, $callback): void
    {
        $this->routeMap['get'][$url] = $callback;
    }

    /**
     * Registers a callback function for handling HTTP POST requests to a specific URL.
     *
     * @param string $url The URL to register the callback function to.
     * @param callable $callback The callback function to handle the HTTP POST request.
     *
     * @return void
     */
    public function post(string $url, $callback): void
    {
        $this->routeMap['post'][$url] = $callback;
    }

    /**
     * Resolves the current HTTP request and maps it to its corresponding callback function.
     *
     * @return mixed The result of the matched callback function.
     *
     * @throws NotFoundException If no matching callback function is found.
     * @throws ForbiddenException If the user is not authenticated but the current action requires authentication.
     */
    public function resolve(): mixed
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        $callback = $this->routeMap[$method][$url] ?? false;
        switch (true) {
            case !$callback:
                $callback = $this->getCallback();
                if ($callback === false) throw new NotFoundException();
                break;
            case is_string($callback):
                return $this->renderView($callback);
            case is_array($callback):
                /**
                 * @var $controller Controller
                 */
                $controller = new $callback[0];
                $controller->action = $callback[1];
                Application::$app->controller = $controller;
                $middlewares = $controller->getMiddlewares();
                foreach ($middlewares as $middleware) $middleware->execute();
                $callback[0] = $controller;
                break;
        }
        return call_user_func($callback, $this->request, $this->response);
    }

    /**
     * Attempts to find a callback function to handle the current HTTP request when the URL and HTTP method do not match.
     *
     * @return mixed The result of the matched callback function, or false if no matching callback function is found.
     */
    public function getCallback(): mixed
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        // Trim slashes
        $url = trim($url, '/');
        // Get all routes for current request method
        $routes = $this->getRouteMap($method);
        // Start iterating registered routes
        foreach ($routes as $route => $callback) {
            // Trim slashes
            $route = trim($route, '/');
            $routeNames = [];
            if (!$route) continue;
            // Find all route names from route and save in $routeNames
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) $routeNames = $matches[1];
            // Convert route name into regex pattern
            $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "($m[2])" : '(\w+)', $route) . "$@";
            // Test and match current route against $routeRegex
            if (preg_match_all($routeRegex, $url, $valueMatches)) {
                $values = [];
                for ($i = 1; $i < count($valueMatches); $i++) $values[] = $valueMatches[$i][0];
                $routeParams = array_combine($routeNames, $values);
                $this->request->setRouteParams($routeParams);
                return $callback;
            }
        }
        return false;
    }

    /**
     * Retrieves the registered routes for a specific HTTP method.
     *
     * @param string $method The HTTP method to retrieve the registered routes for.
     *
     * @return array The mapping of URLs and their corresponding callback functions for the specified HTTP method.
     */
    public function getRouteMap(string $method): array
    {
        return $this->routeMap[$method] ?? [];
    }

    /**
     * Renders a view using the configured view renderer.
     *
     * @param string $view The name of the view to render.
     * @param array $params An optional array of parameters to pass to the view.
     *
     * @return false|array|string The rendered view, or false if rendering the view fails.
     *
     * @throws NotFoundException If the view file doesn't exist.
     */
    public function renderView(string $view, array $params = []): false|array|string
    {
        return Application::$app->view->renderView($view, $params);
    }

    /**
     * Renders only the contents of a view using the configured view renderer.
     *
     * @param string $view The name of the view to render.
     * @param array $params An optional array of parameters to pass to the view.
     *
     * @return false|string The rendered view contents, or false if rendering the view fails.
     *
     * @throws NotFoundException If the view file doesn't exist.
     */
    public function renderViewOnly(string $view, array $params = []): false|string
    {
        return Application::$app->view->renderViewOnly($view, $params);
    }
}