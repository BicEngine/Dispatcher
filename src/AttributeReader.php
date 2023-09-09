<?php

declare(strict_types=1);

namespace Bic\Dispatcher;

use Bic\Contracts\Event\ProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class AttributeReader implements ReaderInterface
{
    /**
     * @var \WeakMap<object, Dispatcher>
     */
    private readonly \WeakMap $contexts;

    public function __construct()
    {
        /** @var \WeakMap<object, Dispatcher> */
        $this->contexts = new \WeakMap();
    }

    /**
     * Skip methods:
     *  - __construct
     *  - __destruct
     *  - any *static*
     *  - any *abstract*
     */
    private function isNonCallable(\ReflectionMethod $method): bool
    {
        return $method->isConstructor()
            || $method->isDestructor()
            || $method->isStatic()
            || $method->isAbstract()
        ;
    }

    /**
     * @return iterable<array-key, Handler>
     */
    private function getHandlers(object $context): iterable
    {
        $reflection = new \ReflectionObject($context);

        foreach ($reflection->getMethods() as $method) {
            if ($this->isNonCallable($method)) {
                continue;
            }

            $attributes = $method->getAttributes(ProviderInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var ProviderInterface $instance */
                $instance = $attribute->newInstance();

                yield new Handler(
                    listener: $instance,
                    handler: $method->getClosure($context),
                );
            }
        }
    }

    public function read(object $context): EventDispatcherInterface
    {
        return $this->contexts[$context] ??= new Dispatcher($this->getHandlers($context));
    }
}
