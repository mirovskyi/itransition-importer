<?php

declare(strict_types=1);

namespace App\Entity;

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * Check validation rules when cost < 5 and stock < 10.
     */
    public function testIsCostAndStockNotValidWhenLess(): void
    {
        $product = new Product();
        $product->setCost(3);
        $product->setStock(5);
        $this->assertTrue($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost = 5 and stock = 10.
     */
    public function testIsCostAndStockNotValidWhenEqual(): void
    {
        $product = new Product();
        $product->setCost(5);
        $product->setStock(10);
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost > 5 and stock > 10.
     */
    public function testIsCostAndStockNotValidWhenGreater(): void
    {
        $product = new Product();
        $product->setCost(123);
        $product->setStock(21);
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost < 5 and stock > 10.
     */
    public function testIsCostAndStockNotValidWhenCostLessAndStockGreater(): void
    {
        $product = new Product();
        $product->setCost(3);
        $product->setStock(12);
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost > 5 and stock < 10.
     */
    public function testIsCostAndStockNotValidWhenCostGreaterAndStockLess(): void
    {
        $product = new Product();
        $product->setCost(23);
        $product->setStock(7);
        $this->assertFalse($product->isCostAndStockNotValid());
    }
}
