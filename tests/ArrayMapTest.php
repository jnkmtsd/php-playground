<?php

declare(strict_types=1);

namespace Pp\Tests;

use PHPUnit\Framework\TestCase;

class ArrayMapTest extends TestCase
{
    public function test_array_map(): void
    {
        $array = [
            'id1' => ['name' => 'John', 'age' => 25],
            'id2' => ['name' => 'Mike', 'age' => 30],
        ];
        $nameArray = array_map(function (array $item) {
            return $item['name'];
        }, $array);
        $this->assertSame(['id1' => 'John', 'id2' => 'Mike'], $nameArray);

        // ['John', 'Mike'] になってほしい場合
        $nameArray = array_map(function (string $key, array $item) {
            return $item['name'];
        }, array_keys($array), $array);
        $this->assertSame(['John', 'Mike'], $nameArray);
    }
}
