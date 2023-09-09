<?php

declare(strict_types=1);

namespace Bic\Dispatcher;

use Bic\Contracts\Event\ProviderInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Bic\Dispatcher
 */
final class Handler
{
    public function __construct(
        public readonly ProviderInterface $listener,
        public readonly \Closure $handler,
    ) {
    }

    public function __invoke(mixed ...$args): mixed
    {
        return ($this->handler)(...$args);
    }
}
