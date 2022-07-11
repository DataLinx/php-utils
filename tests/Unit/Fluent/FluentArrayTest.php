<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use PHPUnit\Framework\TestCase;

require_once './src/fluent_helpers.php';

class FluentArrayTest extends TestCase
{
    /**
     * @return void
     */
    public function testFlatten()
    {
        $cases = [
            ["input" => [1, [2, 3]], "expected" => [1, 2, 3]],
            ["input" => [[1, 55, [12, 3]], [15, [10]]], "expected" => [1, 55, 12, 3, 15, 10]],
            ["input" => [[1, 55, []], [15, [10]]], "expected" => [1, 55, 15, 10]],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], arr($case["input"])->flatten()->getArray());
        }

        // TODO Add test for the case when the target array is passed
    }
}
