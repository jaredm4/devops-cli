<?php

declare(strict_types=1);

namespace Acme\Entity;

use Devops\Entity\Release;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MyRelease extends Release
{
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    protected string $app1_sha;

    public function getApp1Sha(): string
    {
        return $this->app1_sha;
    }

    public function setApp1Sha($app1_sha): self
    {
        $this->app1_sha = $app1_sha;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
            'app1_sha' => $this->app1_sha,
        ];
    }
}
