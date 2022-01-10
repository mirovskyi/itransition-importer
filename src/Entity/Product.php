<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Product
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
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * 
     * @Assert\NotBlank(groups={"Default","import"})
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     * 
     * @Assert\NotBlank(groups={"Default","import"})
     */
    private string $code;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private ?\DateTime $added;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private ?\DateTime $discontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")
     */
    private \DateTime $timestamp;

    /**
     * @var int|null
     * 
     * @ORM\Column(name="intStock", type="smallint", options={"unsigned"=true})
     * 
     * @Assert\NotBlank(groups={"import"})
     */
    private ?int $stock;

    /**
     * @var float|null
     * 
     * @ORM\Column(name="numCost", type="decimal", precision=10, scale=2)
     * 
     * @Assert\NotBlank(groups={"import"})
     * @Assert\LessThanOrEqual(value="1000", groups={"import"})
     */
    private ?float $cost;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return \DateTime|null
     */
    public function getAdded(): ?\DateTime
    {
        return $this->added;
    }

    /**
     * @param \DateTime|null $added
     */
    public function setAdded(?\DateTime $added): void
    {
        $this->added = $added;
    }

    /**
     * @return \DateTime|null
     */
    public function getDiscontinued(): ?\DateTime
    {
        return $this->discontinued;
    }

    /**
     * @param \DateTime|null $discontinued
     */
    public function setDiscontinued(?\DateTime $discontinued): void
    {
        $this->discontinued = $discontinued;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $stock
     */
    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return float|null
     */
    public function getCost(): ?float
    {
        return $this->cost;
    }

    /**
     * @param float|null $cost
     */
    public function setCost(?float $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() 
    {
        $currentDate = new \DateTime();
        $this->added = $currentDate;
        $this->timestamp = $currentDate;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
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
