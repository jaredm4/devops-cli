<?php

declare(strict_types=1);

use Devops\Command\ReleaseListCommand;
use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release as ReleaseResource;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->logger = mockLogger();
    $this->rr = mock(ReleaseResource::class);

    $this->application = new Application();
    $this->command = new ReleaseListCommand($this->logger, $this->rr);
    $this->command->setDateTimeZone(new DateTimeZone('America/Los_Angeles'));
    $this->command->setDateTimeFormat(DateTime::ATOM);
    $this->application->add($this->command);
});

afterEach(function () {
    Mockery::close();
});

it('throws exception when --limit fails validation', function ($limit) {
    $command = $this->application->find('release:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--limit' => $limit,
    ]);

    assertNotEquals(0, $commandTester->getStatusCode());
})->with([
    '-1', 'a', '1.1', 1.2, 0,
])->throws(InvalidOptionException::class);

it('displays table of Releases', function () {
    $now = new DateTime('2020-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $this->rr->expects('getReleases')->once()->with(10)->andReturn([$re, $re]);
    $re->expects('getId')->times(2)->andReturnValues([1, 2]);
    $re->expects('getCreated')->times(2)->andReturnValues([$now, $now]);

    $command = $this->application->find('release:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile('tests/fixtures/Command/release_list_command_0.txt', $output);
    assertEquals(0, $commandTester->getStatusCode());
});
