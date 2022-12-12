<?php

declare(strict_types=1);

namespace Rexpl\Scraper;

class HttpClients
{
    /**
     * Random user agent.
     * 
     * @var array<array<string,string>>
     */
    public const USER_AGENTS = [
        0 => [
            'Accept-Language' => 'en,nl',
            'Cache-Control' => 'max-age=0',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'cross-site',
            'Sec-Fetch-User' => '?1',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Google Chrome";v="107", "Chromium";v="107", "Not=A?Brand";v="24"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"windows"',
        ],
        1 => [
            'Accept-Language' => 'en-US,en;q=0.9',
            'Cache-Control' => 'max-age=0',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-User' => '?1',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46',
            'sec-ch-ua' => '"Not?A_Brand";v="8", "Chromium";v="108", "Microsoft Edge";v="108"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"windows"',
        ],
        /**
         * Remove firefox user agent, causing problems
         * Possibly because of content-encoding
         */
        /*2 => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0',
            'Accept-Language' => 'en-US,en;q=0.5',
            *///'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            /*'Accept-Encoding' => 'gzip, deflate, br',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-User' => '?1',
        ],*/
    ];
}