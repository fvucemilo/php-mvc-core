<?php

namespace fvucemilo\phpmvc\MVC\Views;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\Exceptions\NotFoundException;

/**
 * Class represents a view in an MVC architecture.
 */
class View
{
    /**
     * @var string The title of the view.
     */
    public string $title = '';

    /**
     * Renders a view and its layout.
     *
     * @param string $view The name of the view file.
     * @param array $params An associative array of data to be passed to the view.
     *
     * @return string|false|array The rendered view and layout content, or false on error.
     * @throws NotFoundException If the view file doesn't exist.
     *
     */
    public function renderView(string $view, array $params): array|false|string
    {
        $layout = Application::$app->config['default_layout'];
        if (Application::$app->controller) $layout = Application::$app->controller->layout;
        $viewContent = $this->renderViewOnly($view, $params);
        $layoutFile = Application::$ROOT_DIR . Application::$app->config['layouts_path'] . "/$layout.php";
        if (!file_exists($layoutFile)) throw new NotFoundException("Layout file '$layoutFile' not found!", 404);
        ob_start();
        include_once $layoutFile;
        $layoutContent = ob_get_clean();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Renders a view without its layout.
     *
     * @param string $view The name of the view file.
     * @param array $params An associative array of data to be passed to the view.
     *
     * @return string|false The rendered view content, or false on error.
     * @throws NotFoundException If the view file doesn't exist.
     *
     */
    public function renderViewOnly(string $view, array $params): false|string
    {
        foreach ($params as $key => $value) $$key = $value;
        $viewFile = Application::$ROOT_DIR . Application::$app->config['views_path'] . "/$view.php";
        if (!file_exists($viewFile)) throw new NotFoundException("View file '$viewFile' not found!", 404);
        ob_start();
        include_once $viewFile;
        return ob_get_clean();
    }
}