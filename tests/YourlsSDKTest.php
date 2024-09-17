<?php

namespace Mehrkanal\YourlsPhpSdkTest;

use Codeception\Stub;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mehrkanal\YourlsPhpSdk\YourlsResponse\FindLongUrl;
use Mehrkanal\YourlsPhpSdk\YourlsResponse\GlobalStats;
use Mehrkanal\YourlsPhpSdk\YourlsResponse\UrlStats;
use Mehrkanal\YourlsPhpSdk\YourlsSDK;
use PHPUnit\Framework\TestCase;

class YourlsSDKTest extends TestCase
{
    public function testCreateShortUrl(): void
    {
        $mockClient = Stub::make(Client::class, [
            'post' => function () {
                return new Response(200, [], json_encode([
                    'status' => 'success',
                    'shorturl' => 'http://sho.rt/1f',
                ]));
            },
        ]);

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password', client: $mockClient);
        $shortUrl = $sdk->generateShortUrl('http://example.com');
        $this->assertSame('http://sho.rt/1f', $shortUrl);
    }

    public function testExpandShortUrl(): void
    {
        $mockClient = Stub::make(Client::class, [
            'post' => function () {
                return new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'longurl' => 'http://example.com',
                ]));
            },
        ]);

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password', client: $mockClient);
        $longUrl = $sdk->expandShortUrl('short-keyword');
        $this->assertSame('http://example.com', $longUrl);
    }

    public function testGetUrlStats(): void
    {
        $mockClient = Stub::make(Client::class, [
            'post' => function () {
                return new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'link' => [
                        'clicks' => 2,
                        'timestamp' => '1970-01-01 00:00:00',
                        'ip' => '1.1.1.1',
                        'url' => 'http://example.com',
                        'shorturl' => '1',
                    ],
                ], JSON_THROW_ON_ERROR));
            },
        ]);

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password', client: $mockClient);
        $stats = $sdk->getShortUrlStats('short-keyword');
        $this->assertInstanceOf(UrlStats::class, $stats);
        $this->assertSame(2, $stats->getClicks());
    }

    public function testGetGlobalStats(): void
    {
        $mockClient = Stub::make(Client::class, [
            'post' => function () {
                return new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'db-stats' => [
                        'total_links' => 1000,
                        'total_clicks' => 2000,
                    ],
                ], JSON_THROW_ON_ERROR));
            },
        ]);

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password', client: $mockClient);
        $stats = $sdk->getGlobalStats();
        $this->assertInstanceOf(GlobalStats::class, $stats);
        $this->assertSame(2000, $stats->getTotalClicks());
        $this->assertSame(1000, $stats->getTotalLinks());
    }

    public function testFindShortUrlsByLongUrl(): void
    {
        $mockClient = Stub::make(Client::class, [
            'post' => function () {
                return new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'keywords' => ['1x1', '2b2'],
                ], JSON_THROW_ON_ERROR));
            },
        ]);

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password', client: $mockClient);
        $shortUrlsByLongUrl = $sdk->findShortUrlsByLongUrl('https://example.com/directory/a');
        $this->assertInstanceOf(FindLongUrl::class, $shortUrlsByLongUrl);
        $this->assertSame([
            0 => 'http://sho.rt/1x1',
            1 => 'http://sho.rt/2b2',
        ], $shortUrlsByLongUrl->shortUrls());
    }
}
