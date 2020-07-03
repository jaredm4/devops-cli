<?php

declare(strict_types=1);

namespace Devops\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Updated ConsoleLogger for Monolog, heavily inspired by Symfony's ConsoleLogger.
 * The primary difference is how it handles $context variables on logging, now more in-line with how Monolog handles it.
 * Symfony's ConsoleLogger uses many private properties so not able to extend it to tweak it's output.
 *
 * @see \Symfony\Component\Console\Logger\ConsoleLogger
 */
class ConsoleHandler extends AbstractProcessingHandler
{
    const INFO = 'info';
    const ERROR = 'error';

    private OutputInterface $output;
    private array $verbosityLevelMap = [
        Logger::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
        Logger::ALERT => OutputInterface::VERBOSITY_NORMAL,
        Logger::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
        Logger::ERROR => OutputInterface::VERBOSITY_NORMAL,
        Logger::WARNING => OutputInterface::VERBOSITY_NORMAL,
        Logger::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
        Logger::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
        Logger::DEBUG => OutputInterface::VERBOSITY_DEBUG,
    ];
    private array $formatLevelMap = [
        Logger::EMERGENCY => self::ERROR,
        Logger::ALERT => self::ERROR,
        Logger::CRITICAL => self::ERROR,
        Logger::ERROR => self::ERROR,
        Logger::WARNING => self::INFO,
        Logger::NOTICE => self::INFO,
        Logger::INFO => self::INFO,
        Logger::DEBUG => self::INFO,
    ];

    public function __construct(OutputInterface $output, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->output = $output;
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $record): void
    {
        if (!isset($this->verbosityLevelMap[$record['level']])) {
            throw new InvalidArgumentException(sprintf('The log level "%s" does not exist.', $record['level']));
        }

        $output = $this->output;

        // Write to the error output if necessary and available
        if (self::ERROR === $this->formatLevelMap[$record['level']]) {
            if ($this->output instanceof ConsoleOutputInterface) {
                $output = $output->getErrorOutput();
            }
        }

        if ($output->getVerbosity() >= $this->verbosityLevelMap[$record['level']]) {
            $output->writeln(sprintf('<%1$s>%2$s</%1$s>', $this->formatLevelMap[$record['level']], $record['formatted']));
        }
    }
}
