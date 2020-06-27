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

    private function processProjects(ContainerBuilder $container): void
    {
        $commands = [];
        foreach ($container->findTaggedServiceIds('devops.project.command') as $id => $tags) {
            $commands[] = $container->getDefinition($id);
        }
        foreach ($container->findTaggedServiceIds('devops.project') as $id => $tags) {
            foreach ($commands as $command) {
                $command->addMethodCall('addProjectResource', [new Reference($id)]);
            }
        }
    }

    /**
     * Find the first Release entity tagged with 'devops.release' and use that as our official Release entity.
     * In the event more than 1 exists, first will always be used.
     * @param ContainerBuilder $container
     */
    private function processRelease(ContainerBuilder $container): void
    {
        $release = $container->findTaggedServiceIds('devops.release');
        $resource = $container->getDefinition(ReleaseResource::class);
        $resource->addMethodCall('setReleaseEntityClass', [array_keys($release)[0]]);
    }
}
