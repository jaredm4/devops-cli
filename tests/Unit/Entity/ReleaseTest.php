<?php

declare(strict_types=1);

use Devops\Entity\Release;

it('returns id', function () {
    $class = new \ReflectionClass(Release::class);
    $idProperty = $class->getProperty('id');
    $idProperty->setAccessible(true);

    $release = new Release();
    $idProperty->setValue($release, 1);

    assertEquals(1, $release->getId());
});

it('returns created date', function () {
    $release = new Release();
    $release->onPrePersist();

    assertInstanceOf(DateTime::class, $release->getCreated());
});

it('returns serialized json', function () {
    $class = new \ReflectionClass(Release::class);
    $idProperty = $class->getProperty('id');
    $idProperty->setAccessible(true);
    $createdProperty = $class->getProperty('created');
    $createdProperty->setAccessible(true);

    $date = new DateTime('now', new DateTimeZone('UTC'));

    $release = new Release();
    $idProperty->setValue($release, 1);
    $createdProperty->setValue($release, $date);

    assertEquals([
        'id' => 1,
        'created' => $date->format(DateTime::RFC3339_EXTENDED),
    ], $release->jsonSerialize());
});
