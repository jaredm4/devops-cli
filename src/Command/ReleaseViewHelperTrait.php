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
 */
trait ReleaseViewHelperTrait
{
    private array $headers = ['ID', 'Branch', 'App1 SHA', 'Created'];

    /**
     * Renders a table of Releases in a list format, headers on left. This can be useful when there are many columns and
     * horizontal space is limited.
     *
     * @param Release[] $releases
     */
    protected function renderReleaseList(OutputInterface $output, array $releases): void
    {
        $table = new Table($output);
        $count = 0;
        foreach ($releases as $release) {
            // manually add a separator between rows
            if ($count++) {
                $table->addRow(new TableSeparator());
            }
            $table->addRow([
                implode("\n", array_map(function ($header) {
                    return "<fg=green>${header}</>";
                }, $this->headers)),
                implode("\n", [
                    $release->getId(),
                    $release->getBranch(),
                    $release->getApp1Sha(),
                    $release->getCreated()->setTimezone($this->getDateTimeZone())->format($this->getDateTimeFormat()),
                ]),
            ]);
        }
        $table->render();
    }

    /**
     * Renders a standard table layout of Releases with headers on top.
     *
     * @param Release[] $releases
     */
    protected function renderReleaseTable(OutputInterface $output, array $releases): void
    {
        $table = new Table($output);
        $table->setHeaderTitle('Releases');
        $table->setHeaders($this->headers);
        foreach ($releases as $release) {
            $table->addRow([
                $release->getId(),
                $release->getBranch(),
                $release->getApp1Sha(),
                $release->getCreated()->setTimezone($this->getDateTimeZone())->format($this->getDateTimeFormat()),
            ]);
        }
        $table->render();
    }
}
