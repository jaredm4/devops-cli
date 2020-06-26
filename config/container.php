<?php

declare(strict_types=1);

// helper file to return a usable container (used by devops and doctrine clis)

use Devops\DependencyInjection\Compiler\ListenerCompilerPass;
use Devops\DependencyInjection\Compiler\ProjectCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// Create the container
$containerBuilder = new ContainerBuilder();
$containerBuilder->setParameter('root_dir', realpath(__DIR__.'/..'));
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load('services.yaml');

$containerBuilder->addCompilerPass(new ProjectCompilerPass());

// add our custom listener compiler pass if $dispatcher was defined before requiring this container file.
// (ex. doctrine cli doesn't need this)
if (isset($dispatcher)) {
    $containerBuilder->addCompilerPass(new ListenerCompilerPass($dispatcher), PassConfig::TYPE_BEFORE_REMOVING);
}

$containerBuilder->compile();

return $containerBuilder;
