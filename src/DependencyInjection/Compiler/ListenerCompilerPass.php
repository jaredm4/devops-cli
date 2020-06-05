<?php

declare(strict_types=1);

namespace Devops\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class ListenerCompilerPass
 * Ties listeners to events in the dispatcher based on tags on Listener services.
 */
class ListenerCompilerPass implements CompilerPassInterface
{
    private EventDispatcher $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('console.event_listener') as $id => $tags) {
            foreach ($tags as $tag) {
                $this->dispatcher->addListener($tag['event'],
                    [$container->get($id), $tag['method']],
                    isset($tag['priority']) ? $tag['priority'] : 0
                );
            }
        }
    }
}
