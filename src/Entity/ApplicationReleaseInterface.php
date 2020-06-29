<?php

declare(strict_types=1);

namespace Devops\Entity;

/**
 * Adds application (git repo, etc) logic to a Release entity.
 * Interface ApplicationReleaseInterface.
 */
interface ApplicationReleaseInterface
{
    /**
     * @return array An associative array of property_name => sha value
     */
    public function getApplicationShas(): array;

    /**
     * @param array $application_shas An associative array of property_name => sha value
     */
    public function setApplicationShas(array $application_shas): void;

    /**
     * @return array human readable names of applications, keyed by their entity names
     */
    public function getApplicationNames(): array;
}
