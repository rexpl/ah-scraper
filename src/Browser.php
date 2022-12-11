<?php

declare(strict_types=1);

namespace Rexpl\Scraper;

use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class Browser
{
    /**
     * Has a cooldown time. (-s option)
     *
     * @var bool
     */
    public static bool $coolDown;


    /**
     * Maximum cool down time.
     *
     * @var int
     */
    public static int $maxCoolDown = 30;


    /**
     * Wait time till next request.
     * 
     * @param int
     */
    protected int $waitTime = 0;


    /**
     * The symfony browser instance.
     * 
     * @var HttpBrowser
     */
    public HttpBrowser $symfonyBrowser;


    /**
     * @param int $key
     * 
     * @return void
     */
    public function __construct(int $key)
    {
        $this->symfonyBrowser = new HttpBrowser(
            HttpClient::create([
                'headers' => HttpClients::USER_AGENTS[$key],
            ]),
            new History(),
            new CookieJar()
        );
    }


    /**
     * Is this browser available.
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        // If not set in slow mode
        if (!static::$coolDown) return true;

        return time() > $this->waitTime;
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
        if (static::$coolDown) {

            $this->waitTime = time() + rand(3, static::$maxCoolDown);
        }

        return $this->symfonyBrowser->request('GET', $path);
    }
}