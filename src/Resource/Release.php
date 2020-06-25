<?php

declare(strict_types=1);

namespace Devops\Resource;

use Devops\Entity\Release as ReleaseEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;

class Release
{
    protected LoggerInterface $logger;
    protected EntityManager $entityManager;

    public function __construct(LoggerInterface $logger, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws ORMException
     */
    public function createRelease(string $branch, string $app1_sha): ReleaseEntity
    {
        $release = new ReleaseEntity();
        $release->setBranch($branch);
        $release->setApp1Sha($app1_sha);

        $this->entityManager->persist($release);

        return $release;
    }

    /**
     * @param int|null $limit @param int|null $limit Optionally limit the number of results to the last $limit rows
     *
     * @return ReleaseEntity[]
     */
    public function getReleases(int $limit = null): array
    {
        return $this->entityManager
            ->getRepository(ReleaseEntity::class)
            ->findBy([], ['created' => 'desc'], $limit);
    }

    public function releaseExists($app1_sha): bool
    {
        $this->logger->info('Checking if Release already exists against SHAs.');
        /** @var ReleaseEntity|null $build */
        $build = $this->entityManager->getRepository('Devops\Entity\Release')
            ->findOneBy([
                'app1_sha' => $app1_sha,
            ]);

        return !is_null($build);
    }
}
