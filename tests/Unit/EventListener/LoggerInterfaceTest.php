<?php

declare(strict_types=1);

use Devops\EventListener\LoggingListener;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

beforeEach(function () {
    $this->logger = mockLogger();
    $this->command = mock(\Symfony\Component\Console\Command\Command::class);
    $this->input = mock(\Symfony\Component\Console\Input\InputInterface::class);
    $this->output = mock(\Symfony\Component\Console\Output\OutputInterface::class);
});

it('logs command name', function () {
    // Kludge, but since class is final it can't be mocked
    $event = new ConsoleCommandEvent($this->command, $this->input, $this->output);
    $this->command->expects('getName')->atLeast(1)->andReturn('foo');
    $this->logger->expects('notice')->once()->with('Beginning command.', ['foo']);

    $listener = new LoggingListener($this->logger);
    $listener->logCommandName($event);
});

it('does not log command name for help or list operations', function ($command) {
    // Kludge, but since class is final it can't be mocked
    $event = new ConsoleCommandEvent($this->command, $this->input, $this->output);
    $this->command->expects('getName')->atLeast(1)->andReturn($command);
    $this->logger->expects('notice')->never();

    $listener = new LoggingListener($this->logger);
    $listener->logCommandName($event);
})->with(['help', 'list']);

it('logs command status on success', function () {
    // Kludge, but since class is final it can't be mocked
    $event = new ConsoleTerminateEvent($this->command, $this->input, $this->output, 0);
    $this->command->expects('getName')->atLeast(1)->andReturn('foo');
    $this->logger->expects('notice')->once()->with('Finished command.', ['foo']);
    $this->logger->expects('warning')->never();

    $listener = new LoggingListener($this->logger);
    $listener->logCommandStatus($event);
});

it('does not log command status for help or list operations ', function ($command) {
    // Kludge, but since class is final it can't be mocked
    $event = new ConsoleTerminateEvent($this->command, $this->input, $this->output, 0);
    $this->command->expects('getName')->atLeast(1)->andReturn($command);
    $this->logger->expects('notice')->never();
    $this->logger->expects('warning')->never();

    $listener = new LoggingListener($this->logger);
    $listener->logCommandStatus($event);
})->with(['help', 'list']);

it('logs command status on error', function () {
    // Kludge, but since class is final it can't be mocked
    $event = new ConsoleTerminateEvent($this->command, $this->input, $this->output, 1);
    $this->command->expects('getName')->atLeast(1)->andReturn('foo');
    $this->logger->expects('notice')->never();
    $this->logger->expects('warning')->once()->with('Command exited with a non-zero status code.', ['foo', 1]);

    $listener = new LoggingListener($this->logger);
    $listener->logCommandStatus($event);
});

it('sets exit code to 255 on excessive status code result', function () {
    // Kludge, but since class is final it can't be mocked
    $event = new ConsoleTerminateEvent($this->command, $this->input, $this->output, 9001);
    $this->command->expects('getName')->atLeast(1)->andReturn('foo');
    $this->logger->expects('notice')->never();
    $this->logger->expects('warning')->once()->with('Command exited with a non-zero status code.', ['foo', 255]);

    $listener = new LoggingListener($this->logger);
    $listener->logCommandStatus($event);
});
