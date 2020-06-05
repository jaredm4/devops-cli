<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Psr\Log\NullLogger;

Mockery::globalHelpers();

/** @return MockInterface&NullLogger */
function mockLogger(): MockInterface
{
    return mock(NullLogger::class, [
        'debug' => null,
        'info' => null,
        'notice' => null,
        'warning' => null,
        'error' => null,
        'critical' => null,
        'alert' => null,
        'emergency' => null,
    ]);
}
