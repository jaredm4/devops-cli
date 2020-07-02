<?php

declare(strict_types=1);

/**
 * Just a one-off tool to help me keep Github clean of tags/etc while I work on code.
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;

require __DIR__.'/vendor/autoload.php';

/** @var ContainerBuilder $container */
$container = require __DIR__.'/config/container.php';

/** @var \Github\Client $client */
$client = $container->get('Github\Client');

$org = 'jaredm4';
$repo = 'devops-cli-dummy-app-1';

try {
    $tags = $client->api('gitData')->references()->tags($org, $repo);
} catch (\Github\Exception\RuntimeException $ex) {
    if (404 !== $ex->getCode()) {
        throw $ex;
    }
}

if (empty($tags)) {
    echo 'No tags found.'.PHP_EOL;
    exit;
}

$count = 0;
foreach ($tags as $tag) {
    echo "Removing tag ${tag['ref']}.".PHP_EOL;
    $client->api('gitData')->references()->remove($org, $repo, substr($tag['ref'], 5));
}

echo 'Done.'.PHP_EOL;
