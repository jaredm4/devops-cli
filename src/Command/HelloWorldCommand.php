<?php declare(strict_types=1);

namespace Devops\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command
{
    protected static string $defaultName = 'test';

    protected function configure()
    {
        $this
            ->setDescription('Hello world sample to ensure configuration is setup correctly.')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'What is your name?', 'world');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');
        $output->writeln("Hello, ${name}.");

        return 0;
    }
}
