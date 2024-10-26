<?php

declare(strict_types=1);

namespace Pp;

use GuzzleHttp\ClientInterface;

class Client
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
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
