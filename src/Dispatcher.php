<?php

declare(strict_types=1);

namespace Bic\Dispatcher;

use Bic\Contracts\Event\MatcherInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Bic\Dispatcher
 */
final class Dispatcher implements EventDispatcherInterface
{
    /**
     * @var array<class-string, list<Handler>>
     */
    private array $listeners = [];

    /**
     * @param iterable<array-key, Handler> $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $this->listeners[$handler->listener->getEventClass()][] = $handler;
        }
    }

    /**
     * @template TArgEvent of object
     *
     * @param TArgEvent $event
     *
     * @return TArgEvent
     */
    public function dispatch(object $event): object
    {
        if (!isset($this->listeners[$event::class])
            || ($event instanceof StoppableEventInterface && $event->isPropagationStopped())
        ) {
            return $event;
        }

        foreach ($this->listeners[$event::class] as $handler) {
            $listener = $handler->listener;

            if ($listener instanceof MatcherInterface && !$listener->match($event)) {
                continue;
            }

            ($handler->handler)($event);

            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}
