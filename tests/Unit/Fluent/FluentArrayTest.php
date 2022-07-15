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
    public function testSetAndGet()
    {
        $numbers = arr([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $numbers->getArray());

        $numbers->setArray([4, 5, 6]);

        $this->assertEquals([4, 5, 6], $numbers->getArray());
    }

    /**
     * @return void
     */
    public function testFlatten()
    {
        // tests without target array
        $cases = [
            ["input" => [1, [2, 3]], "expected" => [1, 2, 3]],
            ["input" => [[1, 55, [12, 3]], [15, [10]]], "expected" => [1, 55, 12, 3, 15, 10]],
            ["input" => [[1, 55, []], [15, [10]]], "expected" => [1, 55, 15, 10]],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], arr($case["input"])->flatten()->getArray());
        }

        // tests with target array
        $cases = [
            ["input" => [1, 2, 3], "targetArray" => [1], "expected" => [1, 1, 2, 3]],
            ["input" => [1, [2, 3]], "targetArray" => [10, 5], "expected" => [10, 5, 1, 2, 3]],
            ["input" => [1, [2, 3], [6]], "targetArray" => [10, 5], "expected" => [10, 5, 1, 2, 3, 6]],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], arr($case["input"])->flatten($case["targetArray"])->getArray());
        }
    }
}
