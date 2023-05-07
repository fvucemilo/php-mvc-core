<?php

namespace fvucemilo\phpmvc\Http\Sessions;

/**
 * Class Session provides a wrapper around the PHP session functions to facilitate working with session data.
 */
class Session
{

    /**
     * @var Session The Session instance.
     */
    private static Session $session;

    /**
     * @var FlashSession The FlashSession instance associated with this session.
     */
    public FlashSession $flash;

    /**
     * Constructs a new Session instance.
     */
    public function __construct()
    {
        session_start();
        self::$session = $this;
        $this->flash = new FlashSession(Session::$session);
    }

    /**
     * Sets a value in the session data.
     *
     * @param string $key The key to set.
     * @param mixed $value The value to set.
     *
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a value from the session data.
     *
     * @param string $key The key to retrieve.
     *
     * @return mixed The value associated with the key, or false if it doesn't exist.
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    /**
     * Removes a value from the session data.
     *
     * @param string $key The key to remove.
     *
     * @return void
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
}