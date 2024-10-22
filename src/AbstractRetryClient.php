<?php

declare(strict_types=1);

namespace Pp;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Event\Runtime\PHP;

/**
 * HTTPレスポンスステータスが429のとき任意の回数リトライできるAPIクライアント
 * 継承先でHTTPメソッド、URL、クエリストリングスなどのオプションを設定して使う
 */
abstract class AbstractRetryClient
{
    protected string $baseUrl;
    private Client $client;

    public function __construct(string $baseUrl, ?int $maxRetries = 5, ?Client $client = null)
    {
        $this->baseUrl = $baseUrl;
        $this->client = $client ?? RetryClientFactory::create($maxRetries);
    }

    public function request(array $condition, ?HandlerStack $handler = null): string
    {
        $options = array_merge($this->prepareOptions($condition), ['handler' => $handler ?? $this->getRetryHandler()]);
        $response = $this->client->request($this->prepareMethod(), $this->prepareUrl(), $options);

        return $response->getBody()->getContents();
    }

    abstract protected function prepareMethod(): string;
    abstract protected function prepareUrl(): string;
    abstract protected function prepareOptions(array $condition): array;

    private function getRetryHandler(): HandlerStack
    {
        $decider = function (int $retries, Request $request, Response $response) {
            echo (($retries === 0) ? '初回実行' : "リトライ{$retries}回目") . PHP_EOL;
            if ($retries >= $this->maxRetries) {
                return false;
            }
            $statusCode = $response->getStatusCode();
            if ($statusCode === 429) {
                return true;
            }
            return false;
        };
        $delay = function (int $retries, Response $response) {
            $retryAfterSec = isset($response->getHeader('Retry-After')[0]) ? (int) $response->getHeader('Retry-After')[0] : 0;

            return $retryAfterSec * 1000;
        };
        $retry = Middleware::retry($decider, $delay);
        $handler = HandlerStack::create(new CurlHandler());
        $handler->push($retry);

        return $handler;
    }
}
