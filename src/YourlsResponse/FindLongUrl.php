<?php

declare(strict_types=1);

namespace Mehrkanal\YourlsPhpSdk\YourlsResponse;

class FindLongUrl
{
    private array $shortUrls;

    public function __construct(General $response, string $domain)
    {
        foreach ($response->getBody()['keywords'] as $shortUrl) {
            $this->shortUrls[] = $domain . '/' . $shortUrl;
        }
    }

    public function findShortUrls(): array
    {
        return $this->shortUrls;
    }
}
