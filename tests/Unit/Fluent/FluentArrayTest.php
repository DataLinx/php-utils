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

    public function testInsertBefore()
    {
        // Test integers
        // -------------------------------------
        $source = arr([1, 3, 4]);
        $expected = [1, 2, 3, 4];

        $this->assertEquals($expected, $source->insertBefore(3, 2)->getArray());

        // Test strings
        // -------------------------------------
        $source = arr(["one", "three", "four"]);
        $expected = ["one", "two", "three", "four"];

        $this->assertEquals($expected, $source->insertBefore("three", "two")->getArray());

        // Test duplicate values
        // -------------------------------------
        $source = arr([1, 3, 4, 5, 3, 6]);
        $expected = [1, 2, 3, 4, 5, 3, 6];

        $this->assertEquals($expected, $source->insertBefore(3, 2)->getArray());

        // Test weak comparison
        // -------------------------------------
        $source = arr([1, 3, 4]);
        $expected = [1, 3, 4];

        $this->assertEquals($expected, $source->insertBefore('3', 2)->getArray());

        $source = arr([1, 3, 4]);
        $expected = [1, 2, 3, 4];

        $this->assertEquals($expected, $source->insertBefore('3', 2, null, false)->getArray());

        // Test associative array
        // -------------------------------------
        $source = arr([
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ]);
        $expected = [
            "one" => "apple",
            "avocado",
            "three" => "banana",
            "four" => "orange",
        ];

        $this->assertEquals($expected, $source->insertBefore("banana", "avocado")->getArray());

        // Test insertion with specific key
        // -------------------------------------
        $source = arr([
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ]);
        $expected = [
            "one" => "apple",
            "two" => "avocado",
            "three" => "banana",
            "four" => "orange",
        ];

        $this->assertEquals($expected, $source->insertBefore("banana", "avocado", "two")->getArray());

        // Test insertion with specific key that already exists
        // -------------------------------------
        $source = arr([
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ]);
        $expected = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];

        $this->assertEquals($expected, $source->insertBefore("banana", "avocado", "four")->getArray());
    }

    public function testPositionOf()
    {
        $arr = arr(["one", "two", "three", 4]);

        $this->assertEquals(2, $arr->positionOf("two"));
        $this->assertEquals(4, $arr->positionOf(4));
        $this->assertNull($arr->positionOf('4'));
        $this->assertEquals(4, $arr->positionOf('4', false));
        $this->assertNull($arr->positionOf("four"));
    }

    public function testPositionOfKey()
    {
        $arr = arr([
            "one" => "apple",
            "two" => "banana",
            "three" => "orange",
            4 => "avocado",
        ]);

        $this->assertEquals(2, $arr->positionOfKey("two"));
        $this->assertEquals(4, $arr->positionOfKey(4));
        $this->assertNull($arr->positionOfKey('4'));
        $this->assertEquals(4, $arr->positionOfKey('4', false));
        $this->assertNull($arr->positionOfKey("four"));
    }

    public function testRemove()
    {
        $source = arr([
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ]);
        $expected = [
            "one" => "apple",
            "four" => "orange",
        ];

        $this->assertEquals($expected, $source->remove("banana")->getArray());

        // Test multiple removals
        $source = arr([
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ]);
        $expected = [
            "one" => "apple",
        ];

        $this->assertEquals($expected, $source->remove(["banana", "orange"])->getArray());
    }
}
