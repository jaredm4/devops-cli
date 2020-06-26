<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Resource\Github as GithubResource;
use Devops\Resource\Release as ReleaseResource;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseCreateCommand extends Command implements DateHelperInterface
{
    use DateHelperTrait;
    use ReleaseViewHelperTrait;
    use ValidatorTrait;

    /** @var string|null The command name */
    protected static $defaultName = 'release:create';

    private LoggerInterface $logger;
    private ReleaseResource $releaseResource;
    private GithubResource $githubResource;
    private EntityManager $entityManager;

    private string $descriptionText = 'Create a Release of your application.';
    private string $helpText = <<<'HELP'
        A <fg=green>Release</> is compromised of Git commits of all your packages in the application for a given deployment. 
        An example is if you have a PHP and a JavaScript repository, a Release will contain the Git SHA-1 for each at time of creation.
        Releases are what get deployed in release:deploy.
        
        The <fg=yellow>--branch</> argument is used to verify if a <fg=green>Release</> can get deployed to Production. Only master branch <fg=green>Releases</> can go to Production.
        Otherwise, it is mostly informational.
        
        The <fg=yellow>--format</> changes how the results are returned from this command. Normally, a human readable representation of the Release is returned, however json can be specified if used in an automated fashion.
        HELP;
    private array $outputFormats = ['table', 'list', 'json'];

    public function __construct(LoggerInterface $logger, EntityManager $entityManager, ReleaseResource $releaseResource, GithubResource $githubResource)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->releaseResource = $releaseResource;
        $this->githubResource = $githubResource;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription($this->descriptionText)
            ->setHelp($this->helpText)
            ->addArgument('branch', InputArgument::OPTIONAL, 'Branch this Release relates to.', 'master')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'How to return Release information after creation. Available options are: '.json_encode($this->outputFormats), 'list')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'If specified, will not make any persistent changes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $branch = $input->getArgument('branch');
        $dry = $input->getOption('dry-run');
        $format = $this->validateOptionSet($input->getOption('format'), $this->outputFormats, 'The "--format" option should be one of '.json_encode($this->outputFormats).'.');

        if ($dry) {
            $this->logger->notice('DRY RUN ONLY. Will not save to database or execute any persisting API requests.');
        }

        // Get SHA1s for each project
        // todo project name should be services.yaml or similar?
        $app1_sha = $this->githubResource->getLatestCommitShaOrFail('devops-cli-dummy-app-1', 'master');

        // Verify Release doesn't already exist with SHA1s
        if ($this->releaseResource->releaseExists($app1_sha)) {
            if ($dry) {
                $this->logger->warning('Release already exists for current versions of Git repositories. --dry-run specified, continuing.');
            } else {
                /* @see \Devops\EventListener\ExitCodesListener::defineUserExitCodes() */
                throw new \RuntimeException('Release already exists for current versions of Git repositories.', 64);
            }
        }

        // todo Verify artifacts exist (docker hub, ECR, s3, etc). Tests may have failed if they don't.

        // todo Load previous release to find JIRA tickets between releases

        // Create Release
        $release = $this->releaseResource->createRelease($branch, $app1_sha);

        if ($dry) {
            $this->logger->notice('DRY RUN specified, Release not created.');
        } else {
            // todo Create tags on Github.

            // todo Optionally update JIRA issues with Release name

            // Flush ORM
            $this->entityManager->flush();

            $this->logger->notice('Release created.', [$release->getId(), $release->getCreated()]);
        }

        switch ($format) {
            case 'table':
                $this->renderReleaseTable($output, [$release]);
                break;
            case 'json':
                $output->write(json_encode([$release]));
                break;
            case 'list':
            default:
                $this->renderReleaseList($output, [$release]);
        }

        return 0;
    }
}
