<?php

namespace fvucemilo\phpmvc\App\EventDispatcher;

/**
 * The Event class provides methods to handle events and trigger their associated listeners
 */
class Event
{
    /**
     * Constant representing the "beforeRequest" event.
     */
    public const EVENT_BEFORE_REQUEST = 'beforeRequest';

    /**
     * Constant representing the "afterRequest" event.
     */
    public const EVENT_AFTER_REQUEST = 'afterRequest';

    /**
     * @var array An array containing the registered event listeners.
     */
    public array $eventListeners = [];

    /**
     * Triggers an event and executes all associated event listeners.
     *
     * @param string $eventName The name of the event to trigger.
     *
     * @return void
     */
    public function triggerEvent(string $eventName): void
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            $event = new $callback[0];
            $event->action = $callback[1];
            $callback[0] = $event;
            call_user_func($callback);
        }
    }
}