<?php

declare(strict_types=1);

namespace Rexpl\Scraper;

use Exception;
use Rexpl\Scraper\Contracts\DataCollector;
use Symfony\Component\DomCrawler\Crawler;

class AhScraper
{
    /**
     * All browsers.
     * 
     * @var array<Browser>
     */
    protected array $browsers = [];


    /**
     * All neceassry xpath's.
     * 
     * @var array<string,string>
     */
    public readonly array $xPaths;


    /**
     * @param string $xPathsPath
     * 
     * @return void
     */
    public function __construct(
        string $xPathsPath,
        protected array $dataCollectors
    ) {
        $this->xPaths = require $xPathsPath;

        foreach ($dataCollectors as $value) $this->verifyIsDataCollector($value);
    }


    /**
     * Verifies that the specified object implements the data collector interface.
     * 
     * This methods does not do anything, we are in strict mode (php will throw typerror
     * if object not implementing required interafce)
     * 
     * @param DataCollector $object
     * 
     * @return void
     */
    protected function verifyIsDataCollector(DataCollector $object): void
    {
    }


    /**
     * Make a get request.
     * 
     * @param string $path
     * 
     * @return Crawler
     */
    public function request(string $path): Crawler
    {
        return $this->provideBrowser()->request($path);
    }


    /**
     * Provides an available browser. If no browser is available a new browser is created.
     * 
     * @return Browser
     */
    protected function provideBrowser(): Browser
    {
        $browser = $this->browsers[0] ?? $this->makeNewBrowser();

        while (true) {

            if (!$browser->isAvailable()) {

                sleep(Browser::$minCoolDown);
                continue;
            }
    
            return $browser;
        }

        /**
         * Browser rotation, not working
         * Leads to unexpected behaviour
         * 
         * Likely a cookie problem
         */
        /*foreach ($this->browsers as $browser) {
            
            if ($browser->isAvailable()) return $browser;
        }

        /**
         * We exhausted all our "user agents" we wait 5 seconds and see if another becomes available.
         */
        /*if (count($this->browsers) === 4) {

            sleep(5);
            return $this->provideBrowser();
        }

        return $this->makeNewBrowser();*/
    }


    /**
     * Make a new browser.
     * 
     * @return Browser
     */
    protected function makeNewBrowser(): Browser
    {
        $newBrowser = new Browser(count($this->browsers));

        // tmp key 0 to have only one browser.
        $this->browsers[0] = $newBrowser;

        return $newBrowser;
    }


    /**
     * Is the product available.
     * 
     * @param Crawler $crawler
     * 
     * @return bool
     */
    public function isProductAvailableOnline(Crawler $crawler): bool
    {
        $node = $crawler->evaluate($this->xPaths['product_available']);

        if ($node->count() === 0) return true;

        return $node->innerText() !== 'Alleen in de winkel';
    }


    /**
     * Is the product new.
     * 
     * @param Crawler $crawler
     * 
     * @return bool
     */
    public function isProductNew(Crawler $crawler): bool
    {
        $node = $crawler->evaluate($this->xPaths['product_new']);

        if ($node->count() === 0) return false;

        return $node->first()->innerText() === 'Nieuw';
    }


    /**
     * Return product price.
     * 
     * @param Crawler $crawler
     * @param bool $new
     * 
     * @return array
     */
    public function productPrice(Crawler $crawler, bool $new): array
    {
        $potentialPriceNode = $crawler->evaluate($this->xPaths[$new ? 'product_price_new' : 'product_price']);

        if ('price-amount' === $potentialPriceNode->attr('data-testhook')) {

            return [
                'price' => $potentialPriceNode->text(),
                'after' => $potentialPriceNode->text(),
            ];
        }

        $potentialPriceNode = $crawler->evaluate($this->xPaths['product_price_before']);

        /**
         * Product is in promotion.
         */
        if ('price-amount' === $potentialPriceNode->attr('data-testhook')) {

            $finalPrice = $crawler->evaluate($this->xPaths['product_price_promo']);

            return [
                'price' => $potentialPriceNode->text(),
                'after' => $finalPrice->text(),
            ];
        }

        throw new Exception('Failed parsing the price');
        
    }


    /**
     * Get the product brand.
     * 
     * @param Crawler $crawler
     * 
     * @return string
     */
    public function productBrand(Crawler $crawler): string
    {
        $brandNode = $crawler->evaluate($this->xPaths['product_brand'])->first();

        return substr($brandNode->innerText(), 10);
    }


    /**
     * Get the product image url.
     * 
     * @param Crawler $carwler
     * 
     * @return string
     */
    public function productImage(Crawler $crawler): string
    {
        return $crawler->evaluate($this->xPaths['product_image'])->attr('src');
    }


    /**
     * Is second product free.
     * 
     * This method returns false fr no promotion, 1 for "2e gratis" and 2 for "2e halve prijs".
     * 
     * @param Crawler $crawler
     * 
     * @return false|int
     */
    public function productHasTextualPromotion(Crawler $crawler): false|int
    {
        $node = $crawler->evaluate($this->xPaths['product_second_free']);

        if ($node->count() <= 0) return false;

        if ($node->text() === '2e gratis') return 1;
        if ($node->text() === '2e halve prijs') return 2;

        return false;
    }


    /**
     * Save the product.
     * 
     * @param Product $product
     * 
     * @return void
     */
    public function save(Product $product): void
    {
        foreach ($this->dataCollectors as $collector) $collector->newRow($product); 
    }
}