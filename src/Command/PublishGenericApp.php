<?php

declare(strict_types=1);

namespace Devops\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PublishGenericApp extends Command
{
    use ProjectContextTrait;

    private LoggerInterface $logger;

    private string $helpText = <<<HELP
        This is a skeleton publish command that is used by example to how a single project can be published for use in Releases.
        HELP;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->setProjectName('Generic App');

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName("publish:{$this->projectNameToSpinalCase()}")
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not upload any created objects to external resources (S3, Docker, etc).')
            ->setDescription("Publish a version of {$this->getProjectName()}")
            ->setHelp($this->helpText);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // From here, you can create new Devops\Resources\* to help handle the various commands needed to publish a
        // version of this project.
        $githubRepository = 'Acme\\'.$this->projectNameToSnakeCase();
        // Todo add example Devops\Resource and process handlers.
        return 0;
    }
}
