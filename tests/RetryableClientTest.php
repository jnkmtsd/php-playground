<?php

declare(strict_types=1);

namespace Pp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Pp\RetryableClient;

class RetryableClientTest extends TestCase
{
    public function test_request_retry_success(): void
    {
        $client = (function () {
            $handlerStack = HandlerStack::create(new MockHandler(
                [
                    new Response(429, [], 'Too Many Requests'),
                    new Response(429, [], 'Too Many Requests'),
                    new Response(429, [], 'Too Many Requests'),
                    new Response(200, [], 'OK'), // RetryableClient::MAX_RETRY_COUNT
                ]
            ));
            return new Client(['handler' => $handlerStack]);
        })();
        $retryClient = new RetryableClient($client);
        $response = $retryClient->request('get', 'uri');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getBody()->getContents());
    }

    public function test_request_retry_failure(): void
    {
        $client = (function () {
            $handlerStack = HandlerStack::create(new MockHandler(
                [
                    new Response(429, [], 'Too Many Requests'),
                    new Response(429, [], 'Too Many Requests'),
                    new Response(429, [], 'Too Many Requests'),
                    new Response(429, [], 'Too Many Requests'), // RetryableClient::MAX_RETRY_COUNT
                    new Response(200, [], 'OK'),
                ]
            ));
            return new Client(['handler' => $handlerStack]);
        })();
        $retryClient = new RetryableClient($client);

        $this->expectException(ClientException::class);
        $retryClient->request('get', 'uri');
    }
}
