<?php

namespace Mehrkanal\YourlsPhpSdkTest;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mehrkanal\YourlsPhpSdk\FindLongUrlResponse;
use Mehrkanal\YourlsPhpSdk\YourlsGlobalStats;
use Mehrkanal\YourlsPhpSdk\YourlsSDK;
use Mehrkanal\YourlsPhpSdk\YourlsUrlStats;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class YourlsSDKTest extends TestCase
{
    public function testGenerateShortUrl(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'status' => 'success',
                    'shorturl' => 'http://sho.rt/1f',
                ], JSON_THROW_ON_ERROR)),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $shortUrl = $sdk->generateShortUrl('http://example.com');
        $this->assertSame('http://sho.rt/1f', $shortUrl);
    }

    public function testExpandShortUrl(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'longurl' => 'http://example.com',
                ], JSON_THROW_ON_ERROR)),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $longUrl = $sdk->expandShortUrl('short-keyword');
        $this->assertSame('http://example.com', $longUrl);
    }

    public function testGetShortUrlStats(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'link' => [
                        'clicks' => 2,
                        'timestamp' => '1970-01-01 00:00:00',
                        'ip' => '1.1.1.1',
                        'url' => 'http://example.com',
                        'shorturl' => '1',
                    ],
                ], JSON_THROW_ON_ERROR)),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $stats = $sdk->getShortUrlStats('short-keyword');
        $this->assertInstanceOf(YourlsUrlStats::class, $stats);
        $this->assertSame(2, $stats->getClicks());
    }

    public function testGetGlobalStats(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'db-stats' => [
                        'total_links' => 1000,
                        'total_clicks' => 2000,
                    ],
                ], JSON_THROW_ON_ERROR)),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $stats = $sdk->getGlobalStats();
        $this->assertInstanceOf(YourlsGlobalStats::class, $stats);
        $this->assertSame(2000, $stats->getTotalClicks());
        $this->assertSame(1000, $stats->getTotalLinks());
    }

    public function testFindShortUrlsByLongUrl(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'keywords' => [
                        '1x1',
                        '2b2',
                    ],
                ], JSON_THROW_ON_ERROR)),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $longUrlResponse = $sdk->findShortUrlsByLongUrl('https://example.com/directory/a');
        $this->assertInstanceOf(FindLongUrlResponse::class, $longUrlResponse);
        $this->assertSame(['http://sho.rt/1x1', 'http://sho.rt/2b2'], $longUrlResponse->shortUrls());
    }
}
