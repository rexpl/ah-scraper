<?php

declare(strict_types=1);

namespace Rexpl\Scraper;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class Application extends SingleCommandApplication
{  
    /**
     * Root path of the application.
     * 
     * @var string
     */
    public static string $path;


    /**
     * Instance to interact with the terminal.
     * 
     * @var SymfonyStyle
     */
    protected SymfonyStyle $terminal;


    /**
     * The albert heijn scraper instance.
     * 
     * @var AhScraper
     */
    protected AhScraper $scraper;


    /**
     * Configure the command.
     * 
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('scraper')
            ->setDescription('Start scraping https://www.ah.nl')
            ->addOption('slow', 's', InputOption::VALUE_NONE, 'Scrape slowly (random interval between requests 3-30 sec)')
            ->addOption('max-wait', null, InputOption::VALUE_OPTIONAL, 'Maximum time to wait between requests (sec)', '30')
            ->addOption('min-wait', null, InputOption::VALUE_OPTIONAL, 'Minimum time to wait between requests (sec)', '3')
            ->addArgument('file', InputArgument::OPTIONAL, 'Path to csv file (default: result.csv)', 'result.csv');
    }


    /**
     * Start the scraper
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->terminal = new SymfonyStyle($input, $output);

        $csv = $this->gotCsvFile($input);
        if (null === $csv) return static::SUCCESS;

        $this->scraper = new AhScraper(
            static::$path.'/xpath_config.php', [$csv]
        );

        Browser::$coolDown = $input->getOption('slow');
        Browser::$minCoolDown = (int) trim($input->getOption('min-wait'));
        Browser::$maxCoolDown = (int) trim($input->getOption('max-wait'));

        return $this->scrapeCategory(
            $this->setCategoryOption()
        );
    }


    /**
     * Make sure we have a csv file to write to.
     * 
     * @param InputInterface $input
     * 
     * @return CsvCollector|null
     */
    protected function gotCsvFile(InputInterface $input): ?CsvCollector
    {
        if ('result.csv' === $input->getArgument('file')) {

            $this->terminal->warning('No output file specified, results will be saved in "result.csv".');
        }

        if (file_exists($input->getArgument('file'))) {

            $this->terminal->warning(sprintf(
                'File %s already exists, content will be overwritten.', $input->getArgument('file')
            ));

            if (!$this->terminal->confirm('Do you wish to continue ?')) return null;
        }

        return new CsvCollector($input->getArgument('file'));
    }


    /**
     * Set the category option if it not set.
     * 
     * @return string
     */
    protected function setCategoryOption(): string
    {
        $allCategories = $this->getAllPossibleCategories();

        $default = 0;

        /**
         * Solely for the purpose of this exercise we try to find the key of the request category.
         */
        foreach ($allCategories as $key => $value) {

            if ($value['url'] === 'https://www.ah.nl/producten/bier-en-aperitieven') {

                $default = $key;
                break;
            }
        }

        $category = $this->terminal->choice(
            'Select category (default: Bier en aperitieven)',
            array_map(
                fn (array $ctgry) => $ctgry['name'],
                $allCategories
            ),
            $default
        );

        foreach ($allCategories as $value) {
            
            if ($value['name'] === $category) return $value['url'];
        }
    }


    /**
     * Collect all categories on the albert heijn home page.
     * 
     * @return array
     */
    protected function getAllPossibleCategories(): array
    {
        $crawler = $this->scraper->request('https://www.ah.nl/producten');

        return $crawler->evaluate($this->scraper->xPaths['all_categories'])
            ->each(function (Crawler $node) {

                /**
                 * The exercise is to scrape "/producten/bier-en-aperitieven", so we don't add untested label :)
                 */
                if ($node->attr('href') !== '/producten/bier-en-aperitieven') {
                    
                    $text = $node->innerText() . ' (not really tested)';
                }

                return [
                    'name' => $text ?? $node->innerText(),
                    'url' => 'https://www.ah.nl' . $node->attr('href'),
                ];
            });
    }


    /**
     * Srape the given category.
     * 
     * @param string $categoryUrl
     * 
     * @return int
     */
    protected function scrapeCategory(string $categoryUrl): int
    {
        $pageNeeded = $this->calculateNeededPages($categoryUrl);
        $crawler = $this->scraper->request($categoryUrl . '?page=' . $pageNeeded);

        $products = $crawler->evaluate($this->scraper->xPaths['category_product_list'])
            ->each(fn (Crawler $node) => new Product($node->attr('href'), $node->innerText()));

        $count = count($products);

        $this->terminal->info(sprintf(
            'Found %d products in the specified category.', $count
        ));
        $this->terminal->progressStart($count);

        foreach ($products as $product) $this->scrapeProduct($product);

        $this->terminal->progressFinish();

        // We can guess that all went well because the program should generate exception
        $this->terminal->success(sprintf(
            'Successfully scraped %d products from category.', $count
        ));

        return static::SUCCESS;
    }


    /**
     * Scrape the needed amount of pages to get all products.
     * 
     * @param string $categoryUrl
     * 
     * @return int
     */
    protected function calculateNeededPages(string $categoryUrl): int
    {
        $crawler = $this->scraper->request($categoryUrl);

        $text = $crawler->evaluate($this->scraper->xPaths['category_result_count'])->innerText();

        $resultsOnThisPage = '';
        $totalPossibleResults = '';
        $gotPageResults = false;
        $lastWasNumeric = false;

        /**
         * Hacky way to extract both numbers at the bottom of the page
         */
        foreach (str_split($text) as $value) {

            if (!is_numeric($value) && $lastWasNumeric) $gotPageResults = true;
            
            if (!is_numeric($value)) continue;

            if (!$gotPageResults) {

                $resultsOnThisPage .= $value;
                $lastWasNumeric = true;
                continue;
            }

            $totalPossibleResults .= $value;
        }

        return (int) ceil(
            (int) $totalPossibleResults / (int) $resultsOnThisPage
        );
    }


    /**
     * Scrape the given product.
     * 
     * @param Product $product
     * 
     * @return void
     */
    protected function scrapeProduct(Product $product): void
    {
        $crawler = $this->scraper->request('https://www.ah.nl' . $product->url);

        $product->available = $this->scraper->isProductAvailableOnline($crawler);
        $product->isNew = $this->scraper->isProductNew($crawler);

        [
            'price' => $price,
            'after' => $promo,
        ] = $this->scraper->productPrice($crawler, $product->isNew);

        $product->price = $price;

        if ($price !== $promo) {

            $product->isPromotion = true;
            $product->promotion = 'Price drop: ' . $promo;
        }

        if ($potentialPromotion = $this->scraper->productHasTextualPromotion($crawler)) {
            
            $product->isPromotion = true;
            $product->promotion = $potentialPromotion === 1 ? '2nd is free' : '2nd is discounted 50%';
        }

        $product->brand = $this->scraper->productBrand($crawler);
        $product->imgUrl = $this->scraper->productImage($crawler);

        $this->scraper->save($product);
        $this->terminal->progressAdvance();
    }
}