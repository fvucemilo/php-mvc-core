<?php

namespace fvucemilo\phpmvc\Http\Sessions;

/**
 * FlashSession class provides a way to store flash messages in a session.
 */
class FlashSession
{
    /**
     * @var string The key to use to store flash messages in the session.
     */
    protected const FLASH_KEY = 'flash_messages';

    /**
     * @var Session The session instance.
     */
    private Session $session;

    /**
     * FlashSession constructor.
     *
     * @param Session $session The session instance.
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        // Mark any existing flash messages as "to be removed"
        $flashMessages = $this->session->get(self::FLASH_KEY) ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $this->session->set(self::FLASH_KEY, $flashMessages);
    }

    /**
     * Retrieves a flash message from the session.
     *
     * @param string $key The key to retrieve the message for.
     *
     * @return mixed The message, or false if not found.
     */
    public function get(string $key): mixed
    {
        $flashMessages = $this->session->get(self::FLASH_KEY) ?? [];
        $message = $flashMessages[$key]['value'] ?? false;

        // If the message exists, mark it as "to be removed"
        if ($message) {
            $flashMessages[$key]['remove'] = true;
            $this->session->set(self::FLASH_KEY, $flashMessages);
        }

        return $message;
    }

    /**
     * Stores a flash message in the session.
     *
     * @param string $key The key to store the message under.
     * @param string $message The message to store.
     *
     * @return void
     */
    public function set(string $key, string $message): void
    {
        $flashMessages = $this->session->get(self::FLASH_KEY) ?? [];
        $flashMessages[$key] = [
            'remove' => false,
            'value' => $message
        ];
        $this->session->set(self::FLASH_KEY, $flashMessages);
    }

    /**
     * Destructor. Removes any flash messages that have been marked as "to be removed".
     */
    public function __destruct()
    {
        $this->clearRemovedFlashMessages();
    }

    /**
     * Removes any flash messages that have been marked as "to be removed".
     *
     * @return void
     */
    private function clearRemovedFlashMessages(): void
    {
        $flashMessages = $this->session->get(self::FLASH_KEY) ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $this->session->set(self::FLASH_KEY, $flashMessages);
    }

    /**
     * Clears all flash messages from the session.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->session->remove(self::FLASH_KEY);
    }
}