<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$retryClient = new \Pp\RetryClient($client, 'https://httpbin.org', 10);
var_dump($retryClient->request(['1', '2']));
