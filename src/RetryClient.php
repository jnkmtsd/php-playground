<?php

declare(strict_types=1);

namespace Pp;

class RetryClient extends AbstractRetryClient
{
    protected function prepareMethod(): string
    {
        return 'get';
    }

    protected function prepareUrl(): string
    {
//        return "{$this->baseUrl}/get";
        return "{$this->baseUrl}/status/429";
    }

    protected function prepareOptions(array $condition): array
    {
        return [
            'query' => [
                'id' => implode(',', $condition),
            ],
        ];
    }
}
