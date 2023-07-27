<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    const TRANSACTION_TYPE_DEBIT = 'DEBIT';
    const TRANSACTION_TYPE_CREDIT = 'CREDIT';
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

    /**
     * @ORM\ManyToOne(targetEntity=TransactionBatch::class, inversedBy="transaction")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $transactionBatch;

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

    public function getTransactionBatch(): ?TransactionBatch
    {
        return $this->transactionBatch;
    }

    public function setTransactionBatch(?TransactionBatch $transactionBatch): self
    {
        $this->transactionBatch = $transactionBatch;

        return $this;
    }
}
