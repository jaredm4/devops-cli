<?php

declare(strict_types=1);

namespace Devops\Resource;

use Devops\Exception\GithubCommitNotFoundException;
use Github\Client;
use Github\Exception\ExceptionInterface;
use Github\Exception\RuntimeException;
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
        $this->logger->debug('Github details follows.', [
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

    /**
     * Create a lightweight Git tag in Github.
     * Should we also create annotated tags for releases? Could grab user (tagger) details from chatops user?
     *
     * @param $repository
     * @param $sha
     * @param $tag
     *
     * @return $this
     *
     * @throws ExceptionInterface
     */
    public function createLightweightTag(string $repository, string $sha, string $tag): self
    {
        $referenceData = [
            'ref' => "refs/tags/{$tag}",
            'sha' => $sha,
        ];

        $this->logger->info('Creating lightweight Git tag.', [
            'organization' => $this->organization,
            'repository' => $repository,
            'reference_data' => $referenceData,
        ]);

        try {
            $this->github->api('gitData')->references()->create($this->organization, $repository, $referenceData);
        } catch (RuntimeException $ex) {
            // ignore 'already exists' errors
            // handles use-case where a release deploy fails near the end and needs to be rerun.
            // todo this should be up to a team whether or not to ignore. make it a parameter?
            if (422 !== $ex->getCode()) {
                throw $ex;
            }
        }

        return $this;
    }
}
