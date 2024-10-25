<?php

declare(strict_types=1);

namespace Pp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Event\Runtime\PHP;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RetryableClient implements ClientInterface
{
    private ClientInterface $client;
    private const MAX_RETRY_COUNT = 3;

    public function __construct(ClientInterface $client)
    {
        $client->getConfig('handler')->push(Middleware::retry($this->decider(), $this->delay()));
        $this->client = $client;
    }

    private function decider(): \Closure
    {
        return function (int $retries, Request $request, Response $response) {
//            echo ($retries === 0 ? '初回実行' : "リトライ{$retries}回目") . PHP_EOL;
            if ($retries >= self::MAX_RETRY_COUNT) {
                return false;
            }
            $statusCode = $response->getStatusCode();
            if ($statusCode === 429) {
                return true;
            }
            return false;
        };
    }

    private function delay(): \Closure
    {
        return function (int $retries, Response $response): float {
            return 0;
        };
    }

    /**
     * @inheritDoc
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->client->send($request, $options);
    }

    /**
     * @inheritDoc
     */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        return $this->client->sendAsync($request, $options);
    }

    /**
     * @inheritDoc
     */
    public function request(string $method, $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function requestAsync(string $method, $uri, array $options = []): PromiseInterface
    {
        return $this->client->requestAsync($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(?string $option = null)
    {
        return $this->getConfig($option);
    }
}
