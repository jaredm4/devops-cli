<?php

declare(strict_types=1);

namespace Devops\Command;

use Devops\Resource\Release as ReleaseResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseListCommand extends Command implements DateHelperInterface
{
    use DateHelperTrait;
    use ReleaseViewHelperTrait;
    use ValidatorTrait;

    /** @var string|null The command name */
    protected static $defaultName = 'release:list';

    private LoggerInterface $logger;
    private ReleaseResource $releaseResource;

    private string $descriptionText = 'List Releases created by this tool.';
    private string $helpText = <<<'HELP'
        Lists all created Releases and their statuses, most recent first.

        The <fg=yellow>--limit</> controls how many results are returned by this command. Large lists can be slow.

        The <fg=yellow>--format</> changes how the results are returned from this command. Normally, a human readable representation of the Releases are returned, however json can be specified if used in an automated fashion.
        HELP;
    private array $outputFormats = ['table', 'list', 'json'];

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
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of results displayed to this amount', 10)
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'How to return or display the results. Available options are: '.json_encode($this->outputFormats), 'list');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = $this->validateAndTransformInt($input->getOption('limit'), 'The "--limit" option should be a non-zero positive integer.');
        $format = $this->validateOptionSet($input->getOption('format'), $this->outputFormats, 'The "--format" option should be one of '.json_encode($this->outputFormats).'.');

        $releases = $this->releaseResource->getReleases($limit);

        if (0 == count($releases)) {
            $this->logger->notice('No releases found.');
        }

        $this->render($output, $releases, $format);

        return 0;
    }
}
