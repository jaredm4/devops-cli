<?php

declare(strict_types=1);

use Devops\Exception\GithubCommitNotFoundException;
use Devops\Resource\Github;
use Github\Client;

beforeEach(function () {
    $this->logger = mockLogger();
    $this->client = mock(Client::class);
});

it('gets the latest commit sha1 from github', function () {
    $this->client->expects('api')->with('repo')->andReturns($this->client);
    $this->client->expects('commits')->andReturns($this->client);
    $this->client->expects('all')->with('acme', 'foo', [
        'sha' => 'bar',
        'per_page' => 1,
    ])->andReturns([['sha' => 'abcde12345abcde12345abcde12345abcde12345']]);

    $resource = new Github($this->logger, $this->client, 'acme');

    $output = $resource->getLatestCommitShaOrFail('foo', 'bar');
    assertEquals('abcde12345abcde12345abcde12345abcde12345', $output);
});

it('throws an exception when it cannot find the latest commit sha1 from github', function () {
    $this->client->expects('api')->with('repo')->andReturns($this->client);
    $this->client->expects('commits')->andReturns($this->client);
    $this->client->expects('all')->with('acme', 'foo', [
        'sha' => 'bar',
        'per_page' => 1,
    ])->andReturns([]);

    $resource = new Github($this->logger, $this->client, 'acme');

    $output = $resource->getLatestCommitShaOrFail('foo', 'bar');
    assertEquals('abcde12345abcde12345abcde12345abcde12345', $output);
})->throws(GithubCommitNotFoundException::class);
