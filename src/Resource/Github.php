<?php

declare(strict_types=1);

namespace Devops\Resource;

use Devops\Exception\GithubCommitNotFoundException;
use Github\Client;
use Psr\Log\LoggerInterface;

class Github
{
    protected LoggerInterface $logger;
    protected Client $github;
    protected string $organization;

    public function __construct(LoggerInterface $logger, Client $github, $github_organization)
    {
        $this->logger = $logger;
        $this->github = $github;
        $this->organization = $github_organization;
    }

    /**
     * @param string $repository Git repo name
     * @param string $sha        Git ref (sha, tag, or branch) to find latest commit of
     *
     * @return mixed
     */
    public function getLatestCommitShaOrFail(string $repository, string $sha): string
    {
        $this->logger->info("Retrieving commit sha from GitHub for {$repository}.");
        $this->logger->debug('Github details follows. {org} {repo} {branch}', [
            'org' => $this->organization,
            'repo' => $repository,
            'branch' => $sha,
        ]);

        $commits = $this->github->api('repo')->commits()->all($this->organization, $repository, [
            'sha' => $sha,
            'per_page' => 1,
        ]);

        if (!$commits) {
            throw new GithubCommitNotFoundException("Could not find last commit for {$this->organization}/{$repository}.");
        }

        return $commits[0]['sha'];
    }
}
