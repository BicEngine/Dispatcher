<?php

declare(strict_types=1);

namespace Bic\EventListener\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('bic-engine/dispatcher')]
abstract class TestCase extends BaseTestCase
{
}
