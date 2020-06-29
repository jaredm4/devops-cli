<?php

declare(strict_types=1);

namespace Devops\Resource;

interface ProjectInterface
{
    /**
     * Return an array of repo names and commit shas based on provided branch.
     * Ensure a commit is returned even if that branch does not exist (fallback to master recommended).
     *
     * @param $branch
     */
    public function getLatestApplicationShas($branch): array;
}
