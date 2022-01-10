<?php

namespace App\Entity;

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * Check validation rules when cost < 5 and stock < 10
     */
    public function testIsCostAndStockNotValidWhenLess(): void
    {
        $product = new Product();
        $product->setNumCost(3);
        $product->setIntStock(5);
        $this->assertTrue($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost = 5 and stock = 10
     */
    public function testIsCostAndStockNotValidWhenEqual(): void
    {
        $product = new Product();
        $product->setNumCost(5);
        $product->setIntStock(10);
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost > 5 and stock > 10
     */
    public function testIsCostAndStockNotValidWhenGreater(): void
    {
        $product = new Product();
        $product->setNumCost(123);
        $product->setIntStock(21);
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost < 5 and stock > 10
     */
    public function testIsCostAndStockNotValidWhenCostLessAndStockGreater(): void
    {
        $product = new Product();
        $product->setNumCost(3);
        $product->setIntStock(12);
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost > 5 and stock < 10
     */
    public function testIsCostAndStockNotValidWhenCostGreaterAndStockLess(): void
    {
        $product = new Product();
        $product->setNumCost(23);
        $product->setIntStock(7);
        $this->assertFalse($product->isCostAndStockNotValid());
    }
}
