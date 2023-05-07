<?php

namespace fvucemilo\phpmvc\Exceptions;

use Exception;

/**
 * The NotFoundException class represents an exception that is thrown when a requested resource or page cannot be found.
 */
class NotFoundException extends Exception
{
    /**
     * @var string The error message for this exception.
     */
    protected $message = 'Page not found';

    /**
     * @var int The HTTP status code to be returned with this exception.
     */
    protected $code = 404;
}