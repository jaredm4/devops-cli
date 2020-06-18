<?php

declare(strict_types=1);

namespace Devops\Resource;

use Devops\Entity\Release as ReleaseEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

class Release
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
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
}
