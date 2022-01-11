<?php

namespace App\Tests\Model;

use App\Model\ProductDTO;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    /**
     * Check validation rules when cost < 5 and stock < 10.
     */
    public function testIsCostAndStockNotValidWhenLess(): void
    {
        $product = new ProductDTO();
        $product->cost = 3;
        $product->stock = 5;
        $this->assertTrue($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost = 5 and stock = 10.
     */
    public function testIsCostAndStockNotValidWhenEqual(): void
    {
        $product = new ProductDTO();
        $product->cost = 5;
        $product->stock = 10;
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost > 5 and stock > 10.
     */
    public function testIsCostAndStockNotValidWhenGreater(): void
    {
        $product = new ProductDTO();
        $product->cost = 123;
        $product->stock = 21;
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost < 5 and stock > 10.
     */
    public function testIsCostAndStockNotValidWhenCostLessAndStockGreater(): void
    {
        $product = new ProductDTO();
        $product->cost = 3;
        $product->stock = 12;
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check validation rules when cost > 5 and stock < 10.
     */
    public function testIsCostAndStockNotValidWhenCostGreaterAndStockLess(): void
    {
        $product = new ProductDTO();
        $product->cost = 23;
        $product->stock = 7;
        $this->assertFalse($product->isCostAndStockNotValid());
    }

    /**
     * Check implementation of EntityDTOInterface.
     * Should create Product entity.
     */
    public function testCreatingEntity(): void
    {
        $product = new ProductDTO();
        $product->code = $product->name = $product->description = 'Test';
        $this->assertInstanceOf(\App\Entity\Product::class, $product->createEntity());
    }
}
