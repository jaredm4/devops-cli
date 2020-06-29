<?php

declare(strict_types=1);

namespace Acme\Resource;

use Devops\Resource\Github;
use Devops\Resource\ProjectInterface;
use Psr\Log\LoggerInterface;

class MyProject implements ProjectInterface
{
    private LoggerInterface $logger;
    private Github $githubResource;

    public function __construct(LoggerInterface $logger, Github $githubResource)
    {
        $this->logger = $logger;
        $this->githubResource = $githubResource;
    }

    public function getLatestApplicationShas($branch = 'master'): array
    {
        // the array's keys should match the property value on final release entity
        return [
            'dummy_app_sha' => $this->githubResource->getLatestCommitShaOrFail('devops-cli-dummy-app-1', $branch),
        ];
    }
}
