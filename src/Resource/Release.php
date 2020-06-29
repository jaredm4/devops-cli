<?php

declare(strict_types=1);

namespace Devops\Resource;

use Devops\Entity\ApplicationReleaseInterface;
use Devops\Entity\Release as ReleaseEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;

class Release
{
    private LoggerInterface $logger;
    private EntityManager $entityManager;
    private string $releaseEntityClass = ReleaseEntity::class;

    public function __construct(LoggerInterface $logger, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * Override the default Release entity.
     */
    public function setReleaseEntityClass(string $releaseEntityClass): void
    {
        $this->releaseEntityClass = $releaseEntityClass;
    }

    /**
     * @throws ORMException
     */
    public function createRelease(string $branch, array $application_shas): ReleaseEntity
    {
        /** @var ReleaseEntity|ApplicationReleaseInterface $release */
        $release = new $this->releaseEntityClass();
        $release->setBranch($branch);
        if (is_a($release, ApplicationReleaseInterface::class)) {
            $release->setApplicationShas($application_shas);
        }

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
            ->getRepository($this->releaseEntityClass)
            ->findBy([], ['created' => 'desc'], $limit);
    }

    public function releaseExists(array $application_shas): bool
    {
        $this->logger->info('Checking if Release already exists against SHAs.');
        /** @var ReleaseEntity|ApplicationReleaseInterface|null $build */
        $build = $this->entityManager->getRepository($this->releaseEntityClass)
            ->findOneBy($application_shas);
//            ->findOneBy([
//                'app1_sha' => $app1_sha,
//            ]);

        return !is_null($build);
    }
}
