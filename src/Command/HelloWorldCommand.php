<?php

declare(strict_types=1);

namespace Devops\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command
{
    /** @var string|null The command name */
    protected static $defaultName = 'test';

    private LoggerInterface $logger;

    private string $descriptionText = 'Hello world sample to ensure configuration is setup correctly.';
    private string $helpText = <<<HELP
        A command to help diagnose configuration issues.
        Useful to ensuring configuration, logging and chatops are operating as expected.
        Will not perform any writes, builds or deployments.
        HELP;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription($this->descriptionText)
            ->setHelp($this->helpText)
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'What is your name?', 'world');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('name');
        // Using $output should be rare. Use logger for cli output so it can go to multiple destinations (log aggregator, slack, etc).
        $output->writeln("Hello, ${name}.");

        $this->logger->debug('Test debug log.', ['foo' => 'bar']);
        $this->logger->info('Test info log.');
        $this->logger->notice('Test notice log.');
        $this->logger->warning('Test warning log.');
        $this->logger->error('Test error log.');
        $this->logger->critical('Test critical log.');
        $this->logger->alert('Test alert log.');
        $this->logger->emergency('Test emergency log.', [new \RuntimeException('Faux runtime exception.')]);

        return 0;
    }
}
