<?php

declare(strict_types=1);

namespace Rexpl\Scraper;

use Rexpl\Scraper\Contracts\DataCollector;

class CsvCollector implements DataCollector
{
    /**
     * Csv file.
     * 
     * @var resource
     */
    protected $file;


    /**
     * @param string $path
     * 
     * @return void
     */
    public function __construct(string $path)
    {
        $this->file = fopen($path, 'w+');

        fwrite(
            $this->file,
            '"Url","Name","Product Available Online","New","Brand","Size","Price","In promotion","Promotion","Image url"' . "\n"
        );
    }


    /**
     * Add a new row.
     * 
     * @param Product $product
     * 
     * @return void
     */
    public function newRow(Product $product): void
    {
        fwrite(
            $this->file, 
            sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"',
                'https://www.ah.nl' . $product->url,
                $product->name,
                $product->available ? 'Yes' : 'No',
                $product->isNew ? 'Yes' : 'No',                
                $product->brand,
                $product->size,
                $product->price,
                $product->isPromotion ? 'Yes' : 'No',
                $product->promotion,
                $product->imgUrl
            ) . "\n"
        );
    }
}