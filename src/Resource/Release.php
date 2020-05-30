<?php

declare(strict_types=1);

namespace Devops\Resource;

use Devops\Entity\Release as ReleaseEntity;
use Doctrine\ORM\EntityManager;

class Release
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createRelease(): ReleaseEntity
    {
        $release = new ReleaseEntity();
        $this->entityManager->persist($release);

        return $release;
    }
}
