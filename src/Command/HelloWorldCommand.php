<?php

namespace Devops\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command
{
    protected static string $defaultName = 'test';

    protected function configure()
    {
        $this
            ->setDescription('Hello world sample to ensure configuration is setup correctly.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello, world.');

        return 0;
    }
}
