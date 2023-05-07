<?php

namespace fvucemilo\phpmvc\MVC\Controllers;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\Exceptions\NotFoundException;
use fvucemilo\phpmvc\Middlewares\BaseMiddleware;

/**
 * Class Controller is the base class for all controllers in the MVC pattern.
 */
class Controller
{
    /**
     * @var string The layout to be used for rendering the view.
     */
    public string $layout = '';

    /**
     * @var string The action to be performed by the controller.
     */
    public string $action = '';

    /**
     * @var BaseMiddleware[] An array of middleware objects for the controller.
     */
    protected array $middlewares = [];

    /**
     * Sets the layout to be used for rendering the view.
     *
     * @param string $layout The name of the layout file to be used. If an empty string is provided, the default layout file "main" will be used.
     *
     * @return void
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Renders the specified view with the given parameters.
     *
     * @param string $view The view file to be rendered.
     * @param array $params The parameters to be passed to the view.
     * @return false|array|string Returns the rendered view, or false if the view file was not found.
     *
     * @throws NotFoundException If the view file doesn't exist.
     */
    public function render(string $view, array $params = []): false|array|string
    {
        return Application::$app->router->renderView($view, $params);
    }

    /**
     * Registers a middleware to be used by the controller.
     *
     * @param BaseMiddleware $middleware The middleware to be registered.
     * @return void
     */
    public function registerMiddleware(BaseMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Returns an array of middleware objects registered for the controller.
     *
     * @return BaseMiddleware[] An array of middleware objects registered for the controller.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}