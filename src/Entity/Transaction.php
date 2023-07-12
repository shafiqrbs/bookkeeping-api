<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AccountHead::class, inversedBy="transactions")
     */
    private $head;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $headName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subHeadName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $narration;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHead(): ?AccountHead
    {
        return $this->head;
    }

    public function setHead(?AccountHead $head): self
    {
        $this->head = $head;

        return $this;
    }

    public function getHeadName(): ?string
    {
        return $this->headName;
    }

    public function setHeadName(?string $headName): self
    {
        $this->headName = $headName;

        return $this;
    }

    public function getSubHeadName(): ?string
    {
        return $this->subHeadName;
    }

    public function setSubHeadName(?string $subHeadName): self
    {
        $this->subHeadName = $subHeadName;

        return $this;
    }

    public function getNarration(): ?string
    {
        return $this->narration;
    }

    public function setNarration(?string $narration): self
    {
        $this->narration = $narration;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
