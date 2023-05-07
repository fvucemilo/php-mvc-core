<?php

namespace fvucemilo\phpmvc\Security\Authentications;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\MVC\Models\UserModel;

/**
 * The BaseAuthentication class provides a base implementation for the authentication process.
 */
abstract class BaseAuthentication
{
    /**
     * Determines if the current user is authenticated.
     *
     * @return bool true if the current user is authenticated, false otherwise.
     */
    public static function isAuthenticated(): bool
    {
        return !Application::$app->user;
    }

    /**
     * Logs a user in.
     *
     * @param UserModel $user The user to log in.
     *
     * @return bool
     */
    abstract public function login(UserModel $user): bool;

    /**
     * Logs the current user out.
     *
     * @return void
     */
    abstract public function logout(): void;
}