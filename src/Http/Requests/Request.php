<?php

namespace fvucemilo\phpmvc\Http\Requests;

/**
 * Class Request represents an HTTP request.
 */
class Request
{
    /**
     * @var array Route parameters extracted from the URL
     */
    private array $routeParams = [];

    /**
     * Gets the requested URL.
     *
     * @return string The requested URL
     */
    public function getUrl(): string
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) $path = substr($path, 0, $position);
        return $path;
    }

    /**
     * Gets the request body.
     *
     * @return array An associative array of request parameters
     */
    public function getBody(): array
    {
        $data = [];
        if ($this->isGet()) foreach ($_GET as $key => $value) $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        if ($this->isPost()) foreach ($_POST as $key => $value) $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        return $data;
    }

    /**
     * Determines if the request method is GET.
     *
     * @return bool true if the request method is GET, false otherwise
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    /**
     * Gets the route parameters.
     *
     * @return string An associative array of route parameters
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Determines if the request method is POST.
     *
     * @return bool true if the request method is POST, false otherwise
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    /**
     * Gets the route parameters.
     *
     * @return array An associative array of route parameters.
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Sets the route parameters.
     *
     * @param array $params An associative array of route parameters.
     *
     * @return self
     */
    public function setRouteParams(array $params): Request
    {
        $this->routeParams = $params;
        return $this;
    }

    /**
     * Gets a specific route parameter.
     *
     * @param string $param The name of the route parameter.
     * @param null $default The default value to return if the parameter is not found.
     *
     * @return mixed|null The value of the route parameter or null if it is not found
     */
    public function getRouteParam(string $param, $default = null): mixed
    {
        return $this->routeParams[$param] ?? $default;
    }
}