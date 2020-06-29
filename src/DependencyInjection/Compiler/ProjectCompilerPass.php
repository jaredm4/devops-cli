<?php

declare(strict_types=1);

namespace Devops\DependencyInjection\Compiler;

use Devops\Resource\Release as ReleaseResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ProjectCompilerPass
 * Ties custom Projects and Release to Devops Commands that need them. Your project resources should be tagged with 'devops.project'.
 */
class ProjectCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->processProjects($container);
        $this->processRelease($container);
    }

    /**
     * Find the first project Resource tagged with 'devops.project' and set that as our official project resource.
     * In the event more than 1 exists, only the first will be used.
     */
    private function processProjects(ContainerBuilder $container): void
    {
        $commands = [];
        foreach ($container->findTaggedServiceIds('devops.project.command') as $id => $tags) {
            $commands[] = $container->getDefinition($id);
        }
        $resource = $container->findTaggedServiceIds('devops.project');
        if (count($resource) > 0) {
            foreach ($commands as $command) {
                $command->addMethodCall('setProjectResource', [new Reference(array_keys($resource)[0])]);
            }
        }
    }

    /**
     * Find the first Release entity tagged with 'devops.release' and use that as our official Release entity.
     * In the event more than 1 exists, only the first will be used.
     */
    private function processRelease(ContainerBuilder $container): void
    {
        $entity = $container->findTaggedServiceIds('devops.release');
        if (count($entity) > 0) {
            $resource = $container->getDefinition(ReleaseResource::class);
            $resource->addMethodCall('setReleaseEntityClass', [array_keys($entity)[0]]);
        }
    }
}
