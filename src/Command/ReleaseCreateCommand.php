<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Resource\Release as ReleaseResource;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseCreateCommand extends Command
{
    /** @var string|null The command name */
    protected static $defaultName = 'release:create';

    private LoggerInterface $logger;
    private EntityManager $entityManager;
    private ReleaseResource $releaseResource;

    private string $descriptionText = 'Create a Release of your application.';
    private string $helpText = <<<'HELP'
        A Release is compromised of Git commits of all your packages in the application for a given deployment.
        An example is if you have a PHP and a JavaScript repository, a Release will contain the Git SHA-1 for each at time of creation.
        Releases are what get deployed in deploy:deploy.
        HELP;

    public function __construct(LoggerInterface $logger, ReleaseResource $releaseResource, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->releaseResource = $releaseResource;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription($this->descriptionText)
            ->setHelp($this->helpText);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $release = $this->releaseResource->createRelease();

        $this->entityManager->flush();

        $this->logger->notice('Release created.', [$release->getId(), $release->getCreated()]);

        return 0;
    }
}
