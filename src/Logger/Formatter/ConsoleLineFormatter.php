<?php

declare(strict_types=1);

namespace Devops\Logger\Formatter;

use Monolog\Formatter\LineFormatter;

class ConsoleLineFormatter extends LineFormatter
{
    public const SIMPLE_FORMAT = '[%level_name%] %message% %context%';

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        // lower case the level_name
        $record['level_name'] = strtolower($record['level_name']);

        // trim trailing white-space, as error logs get a red background and makes the spaces noticeable
        return rtrim(parent::format($record));
    }
}
