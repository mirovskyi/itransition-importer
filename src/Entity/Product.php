<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
    private ?int $id;

    /**
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
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
     */
    private ?int $stock;

    /**
     * @ORM\Column(name="numCost", type="decimal", precision=10, scale=2)
     */
    private ?float $cost;

    public function __construct(
        string $code,
        string $name,
        string $description,
        ?\DateTime $discontinued = null,
        ?int $stock = null,
        ?float $cost = null
    ) {
        $this->id = null;
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->discontinued = $discontinued;
        $this->stock = $stock;
        $this->cost = $cost;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAdded(): ?\DateTime
    {
        return $this->added;
    }

    public function getDiscontinued(): ?\DateTime
    {
        return $this->discontinued;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function getCost(): ?float
    {
        return $this->cost;
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
}
