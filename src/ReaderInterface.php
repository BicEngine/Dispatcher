<?php

declare(strict_types=1);

namespace Bic\Dispatcher;

use Bic\Dispatcher\Exception\NonReadableException;
use Psr\EventDispatcher\EventDispatcherInterface;

interface ReaderInterface
{
    /**
     * @throws NonReadableException
     */
    public function read(object $context): EventDispatcherInterface;
}
