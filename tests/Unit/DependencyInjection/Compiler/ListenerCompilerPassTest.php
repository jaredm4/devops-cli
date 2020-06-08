<?php

declare(strict_types=1);

use Devops\DependencyInjection\Compiler\ListenerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

beforeEach(function () {
    $this->eventDispatcher = mock(EventDispatcher::class);
    $this->container = mock(ContainerBuilder::class);
});

it('adds listener from tagged service', function () {
    $this->container->expects('findTaggedServiceIds')->with('console.event_listener')->andReturn([
        'TestListener' => [['event' => 'console.command', 'method' => 'myMethod']],
    ]);
    $listener = new stdClass();
    $this->container->expects('get')->once()->with('TestListener')->andReturn($listener);
    $this->eventDispatcher->expects('addListener')->once()->with('console.command', [$listener, 'myMethod'], 0);

    $compiler = new ListenerCompilerPass($this->eventDispatcher);
    $compiler->process($this->container);
});

it('adds listener with priority from tagged service', function () {
    $this->container->expects('findTaggedServiceIds')->with('console.event_listener')->andReturn([
        'TestListener' => [['event' => 'console.command', 'method' => 'myMethod', 'priority' => 1]],
    ]);
    $listener = new stdClass();
    $this->container->expects('get')->once()->with('TestListener')->andReturn($listener);
    $this->eventDispatcher->expects('addListener')->once()->with('console.command', [$listener, 'myMethod'], 1);

    $compiler = new ListenerCompilerPass($this->eventDispatcher);
    $compiler->process($this->container);
});
