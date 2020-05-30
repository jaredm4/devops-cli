<?php

declare(strict_types=1);

// helper file to return a usable container (used by devops and doctrine clis)

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// Create the container
$containerBuilder = new ContainerBuilder();
$containerBuilder->setParameter('root_dir', realpath(__DIR__.'/..'));
// make container aware of console commands for autoconfigure
$containerBuilder->registerForAutoconfiguration(Command::class)->addTag('console.command');
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load('services.yaml');
$containerBuilder->compile();

return $containerBuilder;
