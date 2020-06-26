<?php

namespace Acme\Resource;

use Devops\Resource\Github;
use Devops\Resource\ProjectInterface;
use Psr\Log\LoggerInterface;

class Project implements ProjectInterface
{
    private LoggerInterface $logger;
    private Github $githubResource;

    public function __construct(LoggerInterface $logger, Github $githubResource)
    {
        $this->logger = $logger;
        $this->githubResource = $githubResource;
    }

    public function getLatestCommitSha($branch = 'master'): string
    {
        return $this->githubResource->getLatestCommitShaOrFail('devops-cli-dummy-app-1', $branch);
    }
}
