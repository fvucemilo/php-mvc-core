<?php

namespace fvucemilo\phpmvc\Exceptions;

use Exception;

/**
 * The ForbiddenException class represents an exception that is thrown when a user attempts to access a resource or perform an action
 * that they do not have permission for.
 */
class ForbiddenException extends Exception
{
    /**
     * @var string The error message for this exception.
     */
    protected $message = 'You don\'t have permission to access this page';

    /**
     * @var int The HTTP status code to be returned with this exception.
     */
    protected $code = 403;
}