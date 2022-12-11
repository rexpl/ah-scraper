<?php

declare(strict_types=1);

namespace Rexpl\Scraper;

class Product
{
    /**
     * @param string $url
     * @param string $name
     * @param bool|null $available
     * @param bool $isNew
     * @param string $price
     * @param bool $isPromotion
     * @param string $promotion
     * @param string $brand
     * @param string $imgUrl
     * @param string $imgSize
     * 
     * @return void
     */
    public function __construct(
        public string $url,
        public string $name,
        public ?bool $available = null,
        public bool $isNew = false,
        public string $price = '0',
        public bool $isPromotion = false,
        public string $promotion = '',
        public string $brand = '',
        public string $imgUrl = '',
        public string $imgSize = ''
    ) {}
}