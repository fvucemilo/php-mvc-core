<?php

namespace fvucemilo\phpmvc\Middlewares;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\Exceptions\ForbiddenException;
use fvucemilo\phpmvc\Security\Authentications\BaseAuthentication;

/**
 * The AuthMiddleware class checks whether a user is authenticated before allowing access to certain routes/actions.
 */
class AuthMiddleware extends BaseMiddleware
{
    /**
     * @var array An array of actions that require authentication.
     */
    protected array $actions = [];

    /**
     * AuthMiddleware constructor.
     *
     * @param array $actions An optional array of actions that require authentication.
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }


    /**
     * Executes the middleware.
     *
     * @throws ForbiddenException If the user is not authenticated but the current action requires authentication.
     */
    public function execute(): void
    {
        if (BaseAuthentication::isAuthenticated()
            && (empty($this->actions)
                || in_array(Application::$app->controller->action, $this->actions))) throw new ForbiddenException();
    }
}