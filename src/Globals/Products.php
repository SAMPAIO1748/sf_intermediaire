<?php

namespace App\Globals;

use App\Repository\ProductRepository;

class Products
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        $gproducts = $this->productRepository->findAll();

        return $gproducts;
    }
}
