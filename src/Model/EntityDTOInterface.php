<?php

declare(strict_types=1);

namespace App\Model;

interface EntityDTOInterface
{
    /**
     * Creates new instance of corresponding entity class.
     */
    public function createEntity(): object;
}
