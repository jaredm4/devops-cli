<?php declare(strict_types=1);

use Devops\Command\HelloWorldCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class HelloWorldCommandTest extends TestCase
{
    public function testOutputsHelloWorld(): void
    {
        $application = new Application();
        $application->add(new HelloWorldCommand());
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
        $application->add(new HelloWorldCommand());
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
