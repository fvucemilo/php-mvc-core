<?php

namespace fvucemilo\phpmvc;

use Exception;
use fvucemilo\phpmvc\App\EventDispatcher\Event;
use fvucemilo\phpmvc\App\EventDispatcher\EventDispatcher;
use fvucemilo\phpmvc\DB\Database;
use fvucemilo\phpmvc\Exceptions\NotFoundException;
use fvucemilo\phpmvc\Http\Requests\Request;
use fvucemilo\phpmvc\Http\Responses\Response;
use fvucemilo\phpmvc\Http\Routers\Router;
use fvucemilo\phpmvc\Http\Sessions\Session;
use fvucemilo\phpmvc\MVC\Controllers\Controller;
use fvucemilo\phpmvc\MVC\Models\UserModel;
use fvucemilo\phpmvc\MVC\Views\View;
use fvucemilo\phpmvc\Security\Authentications\EmailAndPasswordAuthentication;

/**
 * Class Application represents the web application.
 */
class Application
{
    /**
     * @var Application The Application instance.
     */
    public static Application $app;

    /**
     * @var string The root directory of the application.
     */
    public static string $ROOT_DIR;

    /**
     * @var array The configuration array.
     */
    public array $config;

    /**
     * @var string|mixed The user class name.
     */
    public string $userClass;

    /**
     * @var string The layout name.
     */
    public string $layout = 'main';

    /**
     * @var Request The Request instance.
     */
    public Request $request;

    /**
     * @var Response The Response instance.
     */
    public Response $response;

    /**
     * @var Router The Router instance.
     */
    public Router $router;

    /**
     * @var Controller|null The Controller instance.
     */
    public ?Controller $controller = null;

    /**
     * @var Database The Database instance.
     */
    public Database $db;

    /**
     * @var Session The Session instance.
     */
    public Session $session;

    /**
     * @var View The View instance.
     */
    public View $view;

    /**
     * @var UserModel|null The UserModel instance.
     */
    public ?UserModel $user;

    /**
     * @var EventDispatcher The EventDispatcher instance.
     */
    public EventDispatcher $eventDispatcher;

    /**
     * @var EmailAndPasswordAuthentication The EmailAndPasswordAuthentication instance.
     */
    public EmailAndPasswordAuthentication $emailAndPasswordAuth;

    /**
     * Application constructor.
     *
     * @param string $rootDir The root directory of the application.
     * @param array $config The configuration array.
     */
    public function __construct(string $rootDir, array $config)
    {
        self::$app = $this;
        self::$ROOT_DIR = $rootDir;
        $this->config = $config;
        $this->user = null;
        $this->userClass = $config['userClass'];
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config);
        $this->session = new Session();
        $this->view = new View();
        $this->eventDispatcher = new EventDispatcher();
        $this->emailAndPasswordAuth = new EmailAndPasswordAuthentication();

        $userId = Application::$app->session->get('user');
        if ($userId)
            $this->user = $this->userClass::findOne([$this->userClass::getId() => $userId]);
    }

    /**
     * This method runs the application.
     *
     * @throws NotFoundException If the view file doesn't exist.
     *
     * @return void
     */
    public function run(): void
    {
        $this->executeHttpLifecycle();
    }

    /**
     * Executes the HTTP lifecycle.
     *
     * @throws NotFoundException If the view file doesn't exist.
     *
     * @return void
     */
    protected function executeHttpLifecycle(): void
    {
        try {
            $this->eventDispatcher->triggerEvent(Event::EVENT_BEFORE_REQUEST);
            echo $this->router->resolve();
            $this->eventDispatcher->triggerEvent(Event::EVENT_AFTER_REQUEST);
        } catch (Exception $e) {
            echo $this->router->renderViewOnly(Application::$app->config['default_error_view'], [
                'exception' => $e,
            ]);
        }
    }
}