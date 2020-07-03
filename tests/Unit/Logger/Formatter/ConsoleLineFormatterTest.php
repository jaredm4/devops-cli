<?php

declare(strict_types=1);

use Devops\Logger\Formatter\ConsoleLineFormatter;

it('converts log level name to lower case', function () {
    $formatter = new ConsoleLineFormatter();

    $input = [
        'level' => 300,
        'level_name' => 'WARNING',
        'message' => 'formatted faux log message',
        'context' => [],
        'extra' => [],
    ];

    $output = $formatter->format($input);
    assertStringContainsString('warning', $output);
});

it('trims log message of trailing white-space', function () {
    $formatter = new ConsoleLineFormatter(null, null, false, true);

    $input = [
        'level' => 300,
        'level_name' => 'WARNING',
        'message' => 'formatted faux log message',
        'context' => [],
        'extra' => [],
    ];

    $output = $formatter->format($input);
    assertStringEndsNotWith(' ', $output);
});
