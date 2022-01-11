<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product.
 *
 * @ORM\Table(name="tblProductData", uniqueConstraints={@ORM\UniqueConstraint(name="strProductCode", columns={"strProductCode"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity("code", groups={"Default","import"})
 */
class Product
{
    /**
     * @ORM\Column(name="intProductDataId", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     *
     * @Assert\NotBlank(groups={"Default","import"})
     */
    private string $name;

    /**
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     *
     * @Assert\NotBlank(groups={"Default","import"})
     */
    private string $code;

    /**
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private ?\DateTime $added;

    /**
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private ?\DateTime $discontinued;

    /**
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")
     */
    private \DateTime $timestamp;

    /**
     * @ORM\Column(name="intStock", type="smallint", options={"unsigned"=true})
     *
     * @Assert\NotBlank(groups={"import"})
     */
    private ?int $stock;

    /**
     * @ORM\Column(name="numCost", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank(groups={"import"})
     * @Assert\LessThanOrEqual(value="1000", groups={"import"})
     */
    private ?float $cost;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getAdded(): ?\DateTime
    {
        return $this->added;
    }

    public function setAdded(?\DateTime $added): void
    {
        $this->added = $added;
    }

    public function getDiscontinued(): ?\DateTime
    {
        return $this->discontinued;
    }

    public function setDiscontinued(?\DateTime $discontinued): void
    {
        $this->discontinued = $discontinued;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist(): void
    {
        $currentDate = new \DateTime();
        $this->added = $currentDate;
        $this->timestamp = $currentDate;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(): void
    {
        $this->timestamp = new \DateTime();
    }

    /**
     * @Assert\IsFalse(message="Stock less than 10 and cost less than 5", groups={"import"})
     *
     * @return bool
     */
    public function isCostAndStockNotValid()
    {
        return $this->getCost() < 5 && $this->getStock() < 10;
    }
}
