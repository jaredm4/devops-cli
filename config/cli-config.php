<?php

declare(strict_types=1);

// file used by doctrine cli tools to get the entity manager

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\DependencyInjection\ContainerBuilder;

require_once __DIR__.'/../vendor/autoload.php';

/** @var ContainerBuilder $containerBuilder */
$containerBuilder = require __DIR__.'/container.php';

/** @var EntityManager $entityManager */
$entityManager = $containerBuilder->get(EntityManager::class);

return ConsoleRunner::createHelperSet($entityManager);
