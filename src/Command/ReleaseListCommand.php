<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Entity\Release as ReleaseEntity;
use Devops\Resource\Release as ReleaseResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseListCommand extends Command
{
    use DateHelperTrait;
    use ReleaseViewHelperTrait;
    use TransformerTrait;

    /** @var string|null The command name */
    protected static $defaultName = 'release:list';

    private LoggerInterface $logger;
    private ReleaseResource $releaseResource;

    private string $descriptionText = 'List Releases created by this tool.';
    private string $helpText = <<<'HELP'
        Lists all created Releases and their statuses, most recent first.
        HELP;

    public function __construct(LoggerInterface $logger, ReleaseResource $releaseResource)
    {
        $this->logger = $logger;
        $this->releaseResource = $releaseResource;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription($this->descriptionText)
            ->setHelp($this->helpText)
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Restrict number of results to this amount.', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = $this->validateAndTransformInt($input->getOption('limit'));

        /** @var ReleaseEntity[] $builds */
        $builds = $this->releaseResource->getReleases($limit);

        $this->renderReleaseTable($output, $builds);

        return 0;
    }
}
