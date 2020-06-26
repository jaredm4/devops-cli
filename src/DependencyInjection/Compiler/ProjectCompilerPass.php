<?php

declare(strict_types=1);

namespace Devops\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ProjectCompilerPass
 * Ties custom Projects to Devops Commands that need them. Your project resources should be tagged with 'devops.project'.
 */
class ProjectCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
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
}
