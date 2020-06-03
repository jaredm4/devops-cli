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

    $this->command = new ReleaseCreateCommand($this->logger, $this->rr, $this->em);
    $this->command->setDateTimeZone(new DateTimeZone('America/Los_Angeles'));
    $this->command->setDateTimeFormat(DateTime::ATOM);
    $this->application->add($this->command);
});

afterEach(function () {
    Mockery::close();
});

it('creates release with resource', function () {
    $now = new DateTime('2020-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $re->shouldReceive('getId')->times(2)->andReturn(1);
    $re->shouldReceive('getCreated')->times(2)->andReturn($now);
    $this->rr->shouldReceive('createRelease')->once()->andReturn($re);
    $this->em->shouldReceive('flush')->once();
    $this->logger->shouldReceive('notice')->with('Release created.', [1, $now]);

    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile('tests/fixtures/Command/release_create_command_0.txt', $output);
    assertEquals(0, $commandTester->getStatusCode());
});
