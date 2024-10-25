<?php

declare(strict_types=1);

namespace Pp;

class Client
{
    private RetryableClient $client;

    public function __construct(RetryableClient $client)
    {
        $this->client = $client;
    }

    public function request(string $path): string
    {
        $url = "https://httpbin.org{$path}";
        $response = $this->client->request('get', $url);

        return $response->getBody()->getContents();
    }
}
