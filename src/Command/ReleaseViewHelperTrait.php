<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Entity\Release;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait ReleaseViewHelperTrait
 * Aids in rendering releases in tables.
 * @package Devops\Command
 */
trait ReleaseViewHelperTrait
{
    /**
     * Renders a list of releases in a vertical format. The standard format did not support HipChat well when there is
     * a lot of data to display.
     *
     * @param OutputInterface $output
     * @param Release[] $releases
     */
    protected function renderReleaseTable(OutputInterface $output, array $releases): void
    {
        $table = new Table($output);
        $count = 0;
        foreach ($releases as $release) {
            // manually add a separator between rows
            if ($count++) {
                $table->addRow(new TableSeparator());
            }
            $table->addRow([
                implode("\n", [
                    'Id',
                    //'Master',
                    'Created',
                ]),
                implode("\n", [
                    $release->getId(),
                    //$release->isMaster() ? 'X' : '', // TBD
                    $release->getCreated()->setTimezone($this->getDateTimeZone())->format($this->getDateTimeFormat()),
                ]),
            ]);
        }
        $table->render();
    }
}
