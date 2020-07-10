<?php

declare(strict_types=1);

use Devops\Command\ReleaseListCommand;
use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release as ReleaseResource;
use Symfony\Component\Console\Application;
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

it('displays list of releases formatted', function ($format) {
    $date1 = new DateTime('2020-04-20 04:20:00', new DateTimeZone('UTC'));
    $date2 = new DateTime('2021-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $re->allows('getId')->andReturnValues([1, 2]);
    $re->allows('getBranch')->andReturnValues(['foo-branch', 'master']);
    $re->allows('getApp1Sha')->andReturnValues(['abcde12345abcde12345abcde12345abcde12345', 'bcde12345abcde12345abcde12345abcde12345a']);
    $re->allows('getCreated')->andReturnValues([$date1, $date2]);
    $re->allows('jsonSerialize')->andReturnValues([
        [
            'id' => 1,
            'branch' => 'foo-branch',
            'app1_sha' => 'abcde12345abcde12345abcde12345abcde12345',
            'created' => $date1->format(DateTime::RFC3339_EXTENDED),
        ],
        [
            'id' => 2,
            'branch' => 'master',
            'app1_sha' => 'bcde12345abcde12345abcde12345abcde12345a',
            'created' => $date2->format(DateTime::RFC3339_EXTENDED),
        ],
    ]);
    $this->rr->expects('getReleases')->once()->with(10)->andReturn([$re, $re]);

    $command = $this->application->find('release:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--format' => $format,
    ]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile("tests/fixtures/Unit/Command/release_list_command_${format}.txt", $output);
    assertEquals(0, $commandTester->getStatusCode());
})->with(['list', 'table', 'json']);

it('displays list of releases limited by option', function () {
    $date2 = new DateTime('2021-04-20 04:20:00', new DateTimeZone('UTC'));
    $re = mock(ReleaseEntity::class);

    $re->allows('getId')->andReturns(2);
    $re->allows('getBranch')->andReturns('master');
    $re->allows('getApp1Sha')->andReturns('bcde12345abcde12345abcde12345abcde12345a');
    $re->allows('getCreated')->andReturns($date2);
    $re->allows('jsonSerialize')->andReturns([
        'id' => 2,
        'branch' => 'master',
        'app1_sha' => 'bcde12345abcde12345abcde12345abcde12345a',
        'created' => $date2->format(DateTime::RFC3339_EXTENDED),
    ]);
    $this->rr->expects('getReleases')->once()->with(1)->andReturn([$re]);

    $command = $this->application->find('release:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--limit' => '1',
    ]);
    $output = $commandTester->getDisplay();

    assertStringEqualsFile('tests/fixtures/Unit/Command/release_list_command_list_limited.txt', $output);
    assertEquals(0, $commandTester->getStatusCode());
});

it('logs notice if there are no releases to show', function () {
    $this->rr->expects('getReleases')->once()->with(10)->andReturn([]);

    $command = $this->application->find('release:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute([]);
    $output = $commandTester->getDisplay();

    assertEquals('', $output);
    assertEquals(0, $commandTester->getStatusCode());
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
])->throws(UnexpectedValueException::class);

it('throws exception when --format fails validation', function ($format) {
    $command = $this->application->find('release:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        '--format' => $format,
    ]);

    assertNotEquals(0, $commandTester->getStatusCode());
})->with([
    'foobar', 'true', 1,
])->throws(UnexpectedValueException::class);
