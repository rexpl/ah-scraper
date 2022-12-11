<?php

declare(strict_types=1);

namespace Rexpl\Scraper\Contracts;

use Rexpl\Scraper\Product;

interface DataCollector
{
    /**
     * New product is scraped.
     * 
     * @param Product $product
     * 
     * @return void
     */
    public function newRow(Product $product): void;
}