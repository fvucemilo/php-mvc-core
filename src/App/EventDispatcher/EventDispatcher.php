<?php

namespace fvucemilo\phpmvc\App\EventDispatcher;

/**
 * The EventDispatcher class extends the Event class to provide methods for dispatching events to their listeners.
 */
class EventDispatcher extends Event
{
    /**
     * Dispatches an event to its associated listeners.
     *
     * @param string $eventName The name of the event to trigger.
     * @param mixed $callback The callback function to be executed when the event is triggered.
     *
     * @return void
     */
    public function dispatch(string $eventName, mixed $callback): void
    {
        $this->eventListeners[$eventName][] = $callback;
    }
}