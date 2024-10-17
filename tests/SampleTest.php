<?php

declare(strict_types=1);

namespace Pp\Tests;

use PHPUnit\Framework\TestCase;
use Pp\Sample;

class SampleTest extends TestCase
{
    public function test_execute()
    {
        $sample = new Sample();
        $this->assertEquals('Hello World', $sample->execute());
    }
}
