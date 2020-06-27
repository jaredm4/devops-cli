<?php

declare(strict_types=1);

namespace Devops\Entity;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InheritanceType;
use JsonSerializable;

/**
 * @ORM\Entity
 * @InheritanceType("SINGLE_TABLE")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="releases",options={"collate"="utf8_general_ci"})
 */
class Release implements JsonSerializable
{
    /**
     * Allowing the ID to be NULL helps allow dry-run views to work.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected ?int $id = null;
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $branch;
    /**
     * @ORM\Column(type="datetime")
     */
    protected DateTime $created;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->created = new DateTime('now', new DateTimeZone('UTC'));
    }

    /**
     * @return int|null NULL if the entity has not yet been created
     */
    public function getId()
    {
        return $this->id;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function setBranch($branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'branch' => $this->branch,
            'created' => $this->created->format(DateTime::RFC3339_EXTENDED),
        ];
    }
}
