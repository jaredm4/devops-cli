<?php

use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release as ReleaseResource;
use Doctrine\ORM\EntityManager;
use Hamcrest\Matchers;

beforeEach(function () {
    $this->em = mock(EntityManager::class);
});

it('creates and persists a Release entity', function () {
    $this->em->expects('persist')->once()->with(Matchers::anInstanceOf(ReleaseEntity::class));
    $resource = new ReleaseResource($this->em);

    assertInstanceOf(ReleaseEntity::class, $resource->createRelease());
});
