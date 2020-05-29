<?php declare(strict_types=1);

namespace Devops\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command
{
    protected static string $defaultName = 'test';
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Hello world sample to ensure configuration is setup correctly.')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'What is your name?', 'world');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('name');
        # Using $output should be rare. Use logger for cli output so it can go to multiple destinations (log aggregator, slack, etc).
        $output->writeln("Hello, ${name}.");

        $this->logger->debug('Test debug log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->info('Test info log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->notice('Test notice log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->warning('Test warning log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->error('Test error log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->critical('Test critical log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->alert('Test alert log.', [new \RuntimeException('Faux runtime exception.')]);
        $this->logger->emergency('Test emergency log.', [new \RuntimeException('Faux runtime exception.')]);

        return 0;
    }
}
