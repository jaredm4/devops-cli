<?php

declare(strict_types=1);

use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release as ReleaseResource;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Hamcrest\Matchers;

beforeEach(function () {
    $this->em = mock(EntityManager::class);
});

it('creates and persists a Release entity', function () {
    $this->em->expects('persist')->once()->with(Matchers::anInstanceOf(ReleaseEntity::class));

    $resource = new ReleaseResource($this->em);

    assertInstanceOf(ReleaseEntity::class, $resource->createRelease());
});

it('finds and returns Release entities', function () {
    $repo = mock(EntityRepository::class);
    $release = mock(ReleaseEntity::class);
    $this->em->expects('getRepository')->once()->with(ReleaseEntity::class)->andReturn($repo);
    $repo->expects('findBy')->once()->andReturn([$release]);

    $resource = new ReleaseResource($this->em);
    $output = $resource->getReleases();

    assertCount(1, $output);
    assertInstanceOf(ReleaseEntity::class, $output[0]);
});
