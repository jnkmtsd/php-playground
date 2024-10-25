<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$client = new \Pp\Client(new \Pp\RetryableClient(new \GuzzleHttp\Client()));

echo '/status/200 request' . PHP_EOL;
echo $client->request('/status/200') . PHP_EOL;

echo '/status/429 request' . PHP_EOL;

try {
    echo $client->request('/status/429') . PHP_EOL;
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
}
