<?php declare(strict_types=1);

use Devops\Command\HelloWorldCommand;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class HelloWorldCommandTest extends TestCase
{
    private Logger $logger;

    protected function setUp(): void
    {
        $this->logger = new Logger('test', [new PsrHandler(new NullLogger())], [new UidProcessor()]);
    }

    public function testOutputsHelloWorld(): void
    {
        $application = new Application();
        $application->add(new HelloWorldCommand($this->logger));
        $command = $application->find('test');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
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

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Hello, John.', $output);
    }
}
