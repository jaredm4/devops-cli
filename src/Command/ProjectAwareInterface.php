<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Resource\ProjectInterface;

interface ProjectAwareInterface
{
    public function addProjectResource(ProjectInterface $projectResource): void;
}
