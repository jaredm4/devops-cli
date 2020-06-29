<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Resource\ProjectInterface;

trait ProjectAwareTrait
{
    private ?ProjectInterface $projectResource = null;

    /**
     * Adds a dynamic Project to the scope of the Release.
     *
     * @see \Devops\DependencyInjection\Compiler\ProjectCompilerPass
     *
     * @param ProjectInterface $projectResource Your custom project logic, implementing ProjectInterface interface
     */
    public function setProjectResource(ProjectInterface $projectResource): void
    {
        $this->projectResource = $projectResource;
    }

    public function getProjectResource(): ProjectInterface
    {
        return $this->projectResource;
    }
}
