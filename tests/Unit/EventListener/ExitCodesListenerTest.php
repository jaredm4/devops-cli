<?php

declare(strict_types=1);

use Devops\EventListener\ExitCodesListener;
use Symfony\Component\Process\Process;

beforeEach(function () {
    // backup base symfony exist codes
    $this->originalExitCodes = Process::$exitCodes;
});

afterEach(function () {
    // restore base symfony exit codes
    Process::$exitCodes = $this->originalExitCodes;
});

it('adds user-defined exit codes', function () {
    $listener = new ExitCodesListener();
    $listener->defineUserExitCodes();
    assertArrayHasKey(64, Process::$exitCodes);
    assertArrayHasKey(65, Process::$exitCodes);
    assertArrayHasKey(66, Process::$exitCodes);
});
