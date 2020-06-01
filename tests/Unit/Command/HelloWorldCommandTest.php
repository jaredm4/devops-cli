<?php

use Devops\Command\HelloWorldCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->logger = mockLogger();

    $this->application = new Application();
    $this->application->add(new HelloWorldCommand($this->logger));
});

afterEach(function () {
    Mockery::close();
});

it('outputs hello world', function () {
    $command = $this->application->find('test');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);
    $output = $commandTester->getDisplay();

    assertEquals(0, $commandTester->getStatusCode());
    assertStringContainsString('Hello, world.', $output);
});

it('outputs hello john', function () {
    $command = $this->application->find('test');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--name' => 'John',
    ]);
    $output = $commandTester->getDisplay();

    assertEquals(0, $commandTester->getStatusCode());
    assertStringContainsString('Hello, John.', $output);
});

it('logs each type of severity', function () {
    $this->logger->shouldReceive('debug')->once();
    $this->logger->shouldReceive('info')->once();
    $this->logger->shouldReceive('notice')->once();
    $this->logger->shouldReceive('warning')->once();
    $this->logger->shouldReceive('error')->once();
    $this->logger->shouldReceive('critical')->once();
    $this->logger->shouldReceive('alert')->once();
    $this->logger->shouldReceive('emergency')->once();

    $command = $this->application->find('test');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);
});
