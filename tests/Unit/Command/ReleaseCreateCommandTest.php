<?php

use Devops\Command\ReleaseCreateCommand;
use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release as ReleaseResource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->logger = mockLogger();
    $this->rr = mock(ReleaseResource::class);
    $this->em = mock(EntityManager::class);

    $this->application = new Application();
    $this->application->add(new ReleaseCreateCommand($this->logger, $this->rr, $this->em));
});

afterEach(function () {
    Mockery::close();
});

it('creates release with resource', function () {
    $now = new DateTime();
    $re = mock(ReleaseEntity::class);

    $re->shouldReceive('getId')->once()->andReturn(1);
    $re->shouldReceive('getCreated')->once()->andReturn($now);
    $this->rr->shouldReceive('createRelease')->once()->andReturn($re);
    $this->em->shouldReceive('flush')->once();
    $this->logger->shouldReceive('notice')->with('Release created.', [1, $now]);

    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);

    assertEquals(0, $commandTester->getStatusCode());
});
