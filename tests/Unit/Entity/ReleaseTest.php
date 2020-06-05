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
