<?php

namespace fvucemilo\phpmvc\Middlewares;

/**
 * BaseMiddleware is the abstract class that all middleware classes should extend.
 * The BaseMiddleware class provides a common interface for middleware classes to implement,
 * by defining an abstract execute method which must be implemented by its subclasses.
 */
abstract class BaseMiddleware
{
    /**
     * Executes the middleware logic.
     *
     * @return void
     */
    abstract public function execute(): void;
}