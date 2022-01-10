<?php

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
 * @UniqueEntity("strProductCode", groups={"Default","import"})
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
    private int $intProductDataId;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * 
     * @Assert\NotBlank(groups={"Default","import"})
     */
    private string $strProductName;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $strProductDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     * 
     * @Assert\NotBlank(groups={"Default","import"})
     */
    private string $strProductCode;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private ?\DateTime $dtmAdded;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private ?\DateTime $dtmDiscontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, columnDefinition="timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")
     */
    private \DateTime $stmTimestamp;

    /**
     * @var int|null
     * 
     * @ORM\Column(name="intStock", type="smallint", options={"unsigned"=true})
     * 
     * @Assert\NotBlank(groups={"import"})
     */
    private ?int $intStock;

    /**
     * @var float|null
     * 
     * @ORM\Column(name="numCost", type="decimal", precision=10, scale=2)
     * 
     * @Assert\NotBlank(groups={"import"})
     * @Assert\LessThanOrEqual(value="1000", groups={"import"})
     */
    private ?float $numCost;

    /**
     * @return int
     */
    public function getIntProductDataId(): int
    {
        return $this->intProductDataId;
    }

    /**
     * @param int $intProductDataId
     */
    public function setIntProductDataId(int $intProductDataId): void
    {
        $this->intProductDataId = $intProductDataId;
    }

    /**
     * @return string
     */
    public function getStrProductName(): string
    {
        return $this->strProductName;
    }

    /**
     * @param string $strProductName
     */
    public function setStrProductName(string $strProductName): void
    {
        $this->strProductName = $strProductName;
    }

    /**
     * @return string
     */
    public function getStrProductDesc(): string
    {
        return $this->strProductDesc;
    }

    /**
     * @param string $strProductDesc
     */
    public function setStrProductDesc(string $strProductDesc): void
    {
        $this->strProductDesc = $strProductDesc;
    }

    /**
     * @return string
     */
    public function getStrProductCode(): string
    {
        return $this->strProductCode;
    }

    /**
     * @param string $strProductCode
     */
    public function setStrProductCode(string $strProductCode): void
    {
        $this->strProductCode = $strProductCode;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtmAdded(): ?\DateTime
    {
        return $this->dtmAdded;
    }

    /**
     * @param \DateTime|null $dtmAdded
     */
    public function setDtmAdded(?\DateTime $dtmAdded): void
    {
        $this->dtmAdded = $dtmAdded;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtmDiscontinued(): ?\DateTime
    {
        return $this->dtmDiscontinued;
    }

    /**
     * @param \DateTime|null $dtmDiscontinued
     */
    public function setDtmDiscontinued(?\DateTime $dtmDiscontinued): void
    {
        $this->dtmDiscontinued = $dtmDiscontinued;
    }

    /**
     * @return \DateTime
     */
    public function getStmTimestamp(): \DateTime
    {
        return $this->stmTimestamp;
    }

    /**
     * @param \DateTime $stmTimestamp
     */
    public function setStmTimestamp(\DateTime $stmTimestamp): void
    {
        $this->stmTimestamp = $stmTimestamp;
    }

    /**
     * @return int|null
     */
    public function getIntStock(): ?int
    {
        return $this->intStock;
    }

    /**
     * @param int|null $intStock
     */
    public function setIntStock(?int $intStock): void
    {
        $this->intStock = $intStock;
    }

    /**
     * @return float|null
     */
    public function getNumCost(): ?float
    {
        return $this->numCost;
    }

    /**
     * @param float|null $numCost
     */
    public function setNumCost(?float $numCost): void
    {
        $this->numCost = $numCost;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() 
    {
        $currentDate = new \DateTime();
        $this->dtmAdded = $currentDate;
        $this->stmTimestamp = $currentDate;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->stmTimestamp = new \DateTime();
    }

    /**
     * @Assert\IsFalse(message="Stock less than 10 and cost less than 5", groups={"import"})
     * 
     * @return bool
     */
    public function isCostAndStockNotValid()
    {
        return $this->getNumCost() < 5 && $this->getIntStock() < 10;
    }
}
