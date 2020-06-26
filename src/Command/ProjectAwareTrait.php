<?php

namespace Devops\Command;

use Devops\Resource\ProjectInterface;

trait ProjectAwareTrait
{
    /** @var ProjectInterface[] */
    private array $projectResources = [];

    /**
     * Adds a dynamic Project to the scope of the Release.
     * @see \Devops\DependencyInjection\Compiler\ProjectCompilerPass
     * @param ProjectInterface $projectResource
     */
    public function addProjectResource(ProjectInterface $projectResource): void
    {
        $this->projectResources[] = $projectResource;
    }
}
