<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO implements EntityDTOInterface
{
    /**
     * @Assert\NotBlank(groups={"Default","import"})
     * @var string
     */
    public $code;

    /**
     * @Assert\NotBlank(groups={"Default","import"})
     * @var string
     */
    public $name;

    /**
     * @Assert\NotBlank(groups={"Default","import"})
     * @var string
     */
    public $description;

    /**
     * @Assert\Type(type="DateTime", groups={"Default","import"})
     * @var \DateTime|null
     */
    public $discontinued = null;

    /**
     * @Assert\NotBlank(groups={"import"})
     * @Assert\Type(type="int", groups={"Default","import"})
     * @var int|null
     */
    public $stock = null;

    /**
     * @Assert\NotBlank(groups={"import"})
     * @Assert\Type(type="float", groups={"Default","import"})
     * @Assert\LessThanOrEqual(value="1000", groups={"import"})
     * @var float|null
     */
    public $cost = null;

    /**
     * @Assert\IsFalse(message="Stock less than 10 and cost less than 5", groups={"import"})
     */
    public function isCostAndStockNotValid(): bool
    {
        return $this->cost < 5 && $this->stock < 10;
    }

    /**
     * {@inheritdoc}
     */
    public function createEntity(): object
    {
        return new Product(
            $this->code,
            $this->name,
            $this->description,
            $this->discontinued,
            $this->stock,
            $this->cost
        );
    }
}
