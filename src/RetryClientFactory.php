<?php

declare(strict_types=1);

namespace Pp;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class RetryClientFactory
{
    public static function create(int $maxRetryCount): Client
    {
        $decider = function (int $retries, Request $request, Response $response) use ($maxRetryCount): bool {
            echo (($retries === 0) ? '初回実行' : "リトライ{$retries}回目") . PHP_EOL;
            if ($retries >= $maxRetryCount) {
                return false;
            }
            $statusCode = $response->getStatusCode();
            if ($statusCode === 429) {
                return true;
            }
            return false;
        };
        $delay = function (int $retries, Response $response): int {
            $retryAfterSec = isset($response->getHeader('Retry-After')[0]) ? (int) $response->getHeader('Retry-After')[0] : 0;

            return $retryAfterSec * 1000;
        };
        $retry = Middleware::retry($decider, $delay);
        $handler = HandlerStack::create(new CurlHandler());
        $handler->push($retry);

        return new Client(['handler' => $handler]);
    }
}
