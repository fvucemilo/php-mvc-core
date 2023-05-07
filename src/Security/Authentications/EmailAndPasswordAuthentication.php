<?php

namespace fvucemilo\phpmvc\Security\Authentications;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\MVC\Models\UserModel;

/**
 * The EmailAndPasswordAuthentication class is responsible for providing email and password authentication for users.
 */
class EmailAndPasswordAuthentication extends BaseAuthentication
{
    /**
     * Logs in a user by setting the user object and user id in the session.
     *
     * @param UserModel $user The user model to log in.
     *
     * @return bool Returns true if the user was successfully logged in, false otherwise.
     */
    public function login(UserModel $user): bool
    {
        Application::$app->user = $user;
        $className = get_class($user);
        $primaryKey = $className::getId();
        $value = $user->{$primaryKey};
        Application::$app->session->set('user', $value);
        return true;
    }

    /**
     * Logs out the current user by removing the user object and user id from the session.
     *
     * @return void
     */
    public function logout(): void
    {
        Application::$app->user = null;
        Application::$app->session->remove('user');
    }
}