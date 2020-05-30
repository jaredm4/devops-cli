<?php

declare(strict_types=1);

namespace Devops\Command;

use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class HelloWorldCommandTest extends TestCase
{
    private AbstractLogger $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(NullLogger::class);
    }

    public function testOutputsHelloWorld(): void
    {
        $application = new Application();
        $application->add(new HelloWorldCommand($this->logger));
        $command = $application->find('test');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Hello, world.', $output);
    }

    public function testOutputsHelloName(): void
    {
        $application = new Application();
        $application->add(new HelloWorldCommand($this->logger));
        $command = $application->find('test');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--name' => 'John',
        ]);
        $output = $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Hello, John.', $output);
    }

    public function testLogging(): void
    {
        $this->logger->expects($this->once())->method('debug');
        $this->logger->expects($this->once())->method('info');
        $this->logger->expects($this->once())->method('notice');
        $this->logger->expects($this->once())->method('warning');
        $this->logger->expects($this->once())->method('error');
        $this->logger->expects($this->once())->method('critical');
        $this->logger->expects($this->once())->method('alert');
        $this->logger->expects($this->once())->method('emergency');

        $application = new Application();
        $application->add(new HelloWorldCommand($this->logger));
        $command = $application->find('test');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
