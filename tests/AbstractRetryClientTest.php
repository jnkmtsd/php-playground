<?php

declare(strict_types=1);

namespace Pp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Pp\AbstractRetryClient;
use Pp\Sample;

class AbstractRetryClientTest extends TestCase
{
    public function test_request()
    {
        $client = $this->createConcreteClient();
        $response = $client->request([]);
        $this->assertSame('OK', $response);

        $arr = array_merge(['handler' => 'aaa'], ['handler' => 'bbb']);
        $this->assertSame(['handler' => 'bbb'], $arr);
    }

    private function createConcreteClient(): AbstractRetryClient
    {
        return new class extends AbstractRetryClient {
            public function __construct()
            {
                $mock = new MockHandler([
                    new Response(200, [], 'OK'),
                ]);
                $handlerStack = HandlerStack::create($mock);
                $client = new Client(['handler' => $handlerStack]);

                parent::__construct($client, 'https://httpbin.org', 3);
            }
            protected function prepareMethod(): string
            {
                return 'get';
            }

            protected function prepareUrl(): string
            {
                return "{$this->baseUrl}/get";
            }

            protected function prepareOptions(array $condition): array
            {
                return [];
            }
        };
    }
}
