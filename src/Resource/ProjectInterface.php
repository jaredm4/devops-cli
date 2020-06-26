<?php

declare(strict_types=1);

namespace Devops\Resource;

interface ProjectInterface
{
    public function getLatestCommitSha($branch): string;
}
