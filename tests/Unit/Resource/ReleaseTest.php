<?php

declare(strict_types=1);

use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Hamcrest\Matchers;

beforeEach(function () {
    $this->em = mock(EntityManager::class);
});

it('creates and persists a Release entity', function () {
    $this->em->expects('persist')->once()->with(Matchers::anInstanceOf(ReleaseEntity::class));
    $this->em->expects('flush')->never();

    $resource = new Release($this->em);

    $output = $resource->createRelease('foo-branch', 'abcde12345abcde12345abcde12345abcde12345');
    assertInstanceOf(ReleaseEntity::class, $output);
    assertEquals('foo-branch', $output->getBranch());
    assertEquals('abcde12345abcde12345abcde12345abcde12345', $output->getApp1Sha());
});

it('finds and returns Release entities', function () {
    $repo = mock(EntityRepository::class);
    $release = mock(ReleaseEntity::class);
    $this->em->expects('getRepository')->once()->with(ReleaseEntity::class)->andReturn($repo);
    $repo->expects('findBy')->once()->andReturn([$release]);

    $resource = new Release($this->em);
    $output = $resource->getReleases();

    assertCount(1, $output);
    assertEquals($release, $output[0]);
});
