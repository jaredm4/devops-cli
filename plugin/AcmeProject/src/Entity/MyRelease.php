<?php

declare(strict_types=1);

namespace Acme\Entity;

use Devops\Entity\ApplicationReleaseInterface;
use Devops\Entity\Release;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MyRelease extends Release implements ApplicationReleaseInterface
{
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected string $dummy_app_sha;

    /** {@inheritdoc} */
    public function getApplicationShas(): array
    {
        return [
            'dummy_app_sha' => $this->dummy_app_sha,
        ];
    }

    /** {@inheritdoc} */
    public function setApplicationShas(array $application_shas): void
    {
        $this->dummy_app_sha = $application_shas['dummy_app_sha'];
    }

    /** {@inheritdoc} */
    public function getApplicationNames(): array
    {
        return [
            'dummy_app_sha' => 'Dummy App Sha',
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + $this->getApplicationShas();
    }
}
