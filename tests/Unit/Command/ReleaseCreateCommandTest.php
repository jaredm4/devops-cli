<?php

declare(strict_types=1);

use Devops\Command\ReleaseCreateCommand;
use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Github;
use Devops\Resource\Release as ReleaseResource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->logger = mockLogger();
    $this->em = mock(EntityManager::class);
    $this->rr = mock(ReleaseResource::class);
    $this->gr = mock(Github::class);

    $this->application = new Application();

    $this->command = new ReleaseCreateCommand($this->logger, $this->em, $this->rr, $this->gr);
    $this->command->setDateTimeZone(new DateTimeZone('America/Los_Angeles'));
    $this->command->setDateTimeFormat(DateTime::ATOM);
    $this->application->add($this->command);
});

afterEach(function () {
    Mockery::close();
});

it('creates release with resource and returns it formatted', function ($format) {
    $date = new DateTime('2020-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $re->allows([
        'getId' => 1,
        'getBranch' => 'foo-branch',
        'getApp1Sha' => 'abcde12345abcde12345abcde12345abcde12345',
        'getCreated' => $date,
        'jsonSerialize' => [
            'id' => 1,
            'branch' => 'foo-branch',
            'app1_sha' => 'abcde12345abcde12345abcde12345abcde12345',
            'created' => $date->format(DateTime::RFC3339_EXTENDED),
        ],
    ]);
    $this->gr->expects('getLatestCommitShaOrFail')->atleast(1)->andReturn('abcde12345abcde12345abcde12345abcde12345');
    $this->gr->expects('createLightweightTag')->atleast(1)->with('devops-cli-dummy-app-1', 'abcde12345abcde12345abcde12345abcde12345', 1);
    $this->rr->expects('releaseExists')->once()->with('abcde12345abcde12345abcde12345abcde12345')->andReturns(false);
    $this->rr->expects('createRelease')->once()->andReturn($re);
    $this->em->expects('flush')->once();
    $this->logger->expects('notice')->with('Release created.', [1, $date]);

    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--format' => $format,
    ]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile("tests/fixtures/Unit/Command/release_create_command_${format}.txt", $output);
    assertEquals(0, $commandTester->getStatusCode());
})->with(['list', 'table', 'json']);

it('throws an exception if a release already exists', function () {
    $this->gr->expects('getLatestCommitShaOrFail')->atleast(1)->andReturn('abcde12345abcde12345abcde12345abcde12345');
    $this->rr->expects('releaseExists')->once()->with('abcde12345abcde12345abcde12345abcde12345')->andReturns(true);
    $this->rr->expects('createRelease')->never();
    $this->em->expects('flush')->never();

    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);
})->throws(\RuntimeException::class);

it('should not persist during dry-run', function () {
    $now = new DateTime('2020-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $re->allows([
        'getId' => 1,
        'getBranch' => 'foo-branch',
        'getApp1Sha' => 'abcde12345abcde12345abcde12345abcde12345',
        'getCreated' => $now,
    ]);
    $this->gr->allows(['getLatestCommitShaOrFail' => 'abcde12345abcde12345abcde12345abcde12345']);
    $this->rr->expects('releaseExists')->once()->with('abcde12345abcde12345abcde12345abcde12345')->andReturns(false);
    $this->rr->allows(['createRelease' => $re]);
    $this->em->expects('flush')->never();
    $this->logger->expects('notice')->with('Release created.', [1, $now])->never();

    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--dry-run' => null,
    ]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile('tests/fixtures/Unit/Command/release_create_command_list.txt', $output);
    assertEquals(0, $commandTester->getStatusCode());
});

it('should allow releases that already exist during dry-run', function () {
    $now = new DateTime('2020-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $re->allows([
        'getId' => 1,
        'getBranch' => 'foo-branch',
        'getApp1Sha' => 'abcde12345abcde12345abcde12345abcde12345',
        'getCreated' => $now,
    ]);
    $this->gr->allows(['getLatestCommitShaOrFail' => 'abcde12345abcde12345abcde12345abcde12345']);
    $this->rr->expects('releaseExists')->once()->with('abcde12345abcde12345abcde12345abcde12345')->andReturns(true);
    $this->rr->allows(['createRelease' => $re]);
    $this->em->expects('flush')->never();
    $this->logger->expects('notice')->with('Release created.', [1, $now])->never();

    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--dry-run' => null,
    ]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile('tests/fixtures/Unit/Command/release_create_command_list.txt', $output);
    assertEquals(0, $commandTester->getStatusCode());
});

it('throws exception when --limit fails validation', function ($limit) {
    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--limit' => $limit,
    ]);

    assertNotEquals(0, $commandTester->getStatusCode());
})->with([
    '-1', 'a', '1.1', 1.2, 0,
])->throws(InvalidOptionException::class);

it('throws exception when --format fails validation', function ($format) {
    $command = $this->application->find('release:create');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--format' => $format,
    ]);

    assertNotEquals(0, $commandTester->getStatusCode());
})->with([
    'foobar', 'true', 1,
])->throws(InvalidOptionException::class);
