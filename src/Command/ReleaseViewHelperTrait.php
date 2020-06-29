<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Entity\ApplicationReleaseInterface;
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
    private array $standardHeaders = ['ID', 'Branch', 'Created'];

    /**
     * Renders a table of Releases in a list format, headers on left. This can be useful when there are many columns and
     * horizontal space is limited.
     *
     * @param Release[]|ApplicationReleaseInterface[] $releases
     */
    protected function renderReleaseList(OutputInterface $output, array $releases): void
    {
        $table = new Table($output);
        $count = 0;
        $app_shas = [];
        $headers = implode("\n", array_map(function ($header) {
            return "<fg=green>${header}</>";
        }, $this->assembleHeaders($releases)));

        foreach ($releases as $release) {
            if (is_a($release, ApplicationReleaseInterface::class)) {
                $app_shas = $release->getApplicationShas();
                ksort($app_shas);
            }
            // manually add a separator between rows
            if ($count++) {
                $table->addRow(new TableSeparator());
            }
            $table->addRow([
                $headers,
                implode("\n", array_merge([
                    $release->getId(),
                    $release->getBranch(),
                    $release->getCreated()->setTimezone($this->getDateTimeZone())->format($this->getDateTimeFormat()),
                ], $app_shas)),
            ]);
        }
        $table->render();
    }

    /**
     * Renders a standard table layout of Releases with headers on top.
     *
     * @param Release[]|ApplicationReleaseInterface[] $releases
     */
    protected function renderReleaseTable(OutputInterface $output, array $releases): void
    {
        $table = new Table($output);
        $table->setHeaderTitle('Releases');
        $table->setHeaders($this->assembleHeaders($releases));
        $app_shas = [];
        foreach ($releases as $release) {
            if (is_a($release, ApplicationReleaseInterface::class)) {
                $app_shas = $release->getApplicationShas();
                ksort($app_shas);
            }
            $table->addRow(array_merge([
                $release->getId(),
                $release->getBranch(),
                $release->getCreated()->setTimezone($this->getDateTimeZone())->format($this->getDateTimeFormat()),
            ], $app_shas));
        }
        $table->render();
    }

    private function assembleHeaders(array $releases)
    {
        $headers = $this->standardHeaders;

        if (count($releases) > 0 && is_a($releases[0], ApplicationReleaseInterface::class)) {
            $app_headers = $releases[0]->getApplicationNames();
            ksort($app_headers);
            $headers = array_merge($headers, $app_headers);
        }

        return $headers;
    }
}
