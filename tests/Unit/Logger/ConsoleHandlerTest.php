<?php

declare(strict_types=1);

use Devops\Logger\ConsoleHandler;
use Monolog\Logger;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

it('writes warning log as green to console output', function () {
    $output = mock(ConsoleOutputInterface::class);
    $handler = new ConsoleHandler($output);

    $input = [
        'level' => Logger::WARNING,
        'formatted' => 'formatted faux log message',
    ];
    $output->allows(['getVerbosity' => OutputInterface::VERBOSITY_NORMAL]);
    $output->expects('writeln')->with("<info>${input['formatted']}</info>");

    $handler->write($input);
});

it('does not write debug log if verbosity is not high enough', function () {
    $output = mock(ConsoleOutputInterface::class);
    $handler = new ConsoleHandler($output);

    $input = [
        'level' => Logger::DEBUG,
        'formatted' => 'formatted faux log message',
    ];
    $output->expects('getVerbosity')->andReturn(OutputInterface::VERBOSITY_NORMAL);
    $output->expects('writeln')->never();

    $handler->write($input);
});

it('throws exception when provided level is not acceptable', function () {
    $output = mock(ConsoleOutputInterface::class);
    $handler = new ConsoleHandler($output);

    $input = [
        'level' => 9001,
    ];
    $output->expects('writeln')->never();

    $handler->write($input);
})->throws(InvalidArgumentException::class);

it('should switch to ErrorOutput for applicable levels', function () {
    $output = mock(ConsoleOutputInterface::class);
    $handler = new ConsoleHandler($output);

    $input = [
        'level' => Logger::ERROR,
        'formatted' => 'formatted faux log message',
    ];
    $output->allows(['getVerbosity' => OutputInterface::VERBOSITY_NORMAL]);
    $output->expects('writeln')->once()->with("<error>${input['formatted']}</error>");
    $output->expects('getErrorOutput')->once()->andReturn($output);

    $handler->write($input);
});
