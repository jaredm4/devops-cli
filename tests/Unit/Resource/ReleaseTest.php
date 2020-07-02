<?php

declare(strict_types=1);

use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Hamcrest\Matchers;

beforeEach(function () {
    $this->logger = mockLogger();
    $this->em = mock(EntityManager::class);
});

it('creates and persists a release entity', function () {
    $this->em->expects('persist')->once()->with(Matchers::anInstanceOf(ReleaseEntity::class));
    $this->em->expects('flush')->never();

    $resource = new Release($this->logger, $this->em);

    $output = $resource->createRelease('foo-branch');
    assertInstanceOf(ReleaseEntity::class, $output);
    assertEquals('foo-branch', $output->getBranch());
});

it('finds and returns release entities', function () {
    $repo = mock(EntityRepository::class);
    $release = mock(ReleaseEntity::class);
    $this->em->expects('getRepository')->once()->with(ReleaseEntity::class)->andReturn($repo);
    $repo->expects('findBy')->once()->andReturn([$release]);

    $resource = new Release($this->logger, $this->em);
    $output = $resource->getReleases();

    assertCount(1, $output);
    assertEquals($release, $output[0]);
});

it('checks if a release exists with matching sha1s', function ($exists) {
    $repo = mock(EntityRepository::class);
    $release = mock(ReleaseEntity::class);
    $this->em->expects('getRepository')->once()->with(ReleaseEntity::class)->andReturn($repo);
    $repo->expects('findOneBy')->once()->with([
        'app1_sha' => 'abcde12345abcde12345abcde12345abcde12345',
    ])->andReturn($exists ? [$release] : null);

    $resource = new Release($this->logger, $this->em);
    assertEquals($exists, $resource->releaseExists('abcde12345abcde12345abcde12345abcde12345'));
})->with([true, false])->skip('Needs update to allow for dynamic projects');
