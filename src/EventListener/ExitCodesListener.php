<?php

declare(strict_types=1);

namespace Devops\EventListener;

use Symfony\Component\Process\Process;

class ExitCodesListener
{
    /**
     * Adds user-defined exit codes to Symfony's default list.
     *
     * @see Process::$exitCodes
     */
    public function defineUserExitCodes()
    {
        Process::$exitCodes += [
            64 => 'Build already exists.',
            65 => 'Missing build artifact.',
            66 => 'Project artifact already exists.',
        ];
    }
}
