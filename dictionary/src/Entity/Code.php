<?php

namespace App\Entity;

use App\Repository\CodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CodeRepository::class)
 */
class Code
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $global_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $razdel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $kod;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomdescr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idx;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deleted_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGlobalId(): ?int
    {
        return $this->global_id;
    }

    public function setGlobalId(int $global_id): self
    {
        $this->global_id = $global_id;

        return $this;
    }

    public function getRazdel(): ?string
    {
        return $this->razdel;
    }

    public function setRazdel(string $razdel): self
    {
        $this->razdel = $razdel;

        return $this;
    }

    public function getKod(): ?string
    {
        return $this->kod;
    }

    public function setKod(?string $kod): self
    {
        $this->kod = $kod;

        return $this;
    }

    public function getNomdescr(): ?string
    {
        return $this->nomdescr;
    }

    public function setNomdescr(?string $nomdescr): self
    {
        $this->nomdescr = $nomdescr;

        return $this;
    }

    public function getIdx(): ?string
    {
        return $this->idx;
    }

    public function setIdx(string $idx): self
    {
        $this->idx = $idx;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }
}
