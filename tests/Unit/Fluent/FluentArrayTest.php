<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use PHPUnit\Framework\TestCase;

class FluentArrayTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetAndGet(): void
    {
        $numbers = arr([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $numbers->toArray());

        $numbers->setArray([4, 5, 6]);

        $this->assertEquals([4, 5, 6], $numbers->toArray());
    }

    /**
     * @return void
     */
    public function testToString(): void
    {
        $this->assertEquals(print_r([1,2,3], true), (string)arr([1,2,3]));
    }

    /**
     * @return void
     */
    public function testFlatten(): void
    {
        // tests without target array
        $cases = [
            ["input" => [1, [2, 3]], "expected" => [1, 2, 3]],
            ["input" => [[1, 55, [12, 3]], [15, [10]]], "expected" => [1, 55, 12, 3, 15, 10]],
            ["input" => [[1, 55, []], [15, [10]]], "expected" => [1, 55, 15, 10]],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], arr($case["input"])->flatten()->toArray());
        }

        // tests with target array
        $cases = [
            ["input" => [1, 2, 3], "targetArray" => [1], "expected" => [1, 1, 2, 3]],
            ["input" => [1, [2, 3]], "targetArray" => [10, 5], "expected" => [10, 5, 1, 2, 3]],
            ["input" => [1, [2, 3], [6]], "targetArray" => [10, 5], "expected" => [10, 5, 1, 2, 3, 6]],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], arr($case["input"])->flatten($case["targetArray"])->toArray());
        }
    }

    /**
     * @return void
     */
    public function testInsertBefore(): void
    {
        // Test integers
        // -------------------------------------
        $source = [1, 3, 4];
        $expected = [1, 2, 3, 4];
        $actual = arr($source)->before(3)->insert(2)->toArray();

        $this->assertEquals($expected, $actual);

        // Test strings
        // -------------------------------------
        $source = ["one", "three", "four"];
        $expected = ["one", "two", "three", "four"];
        $actual = arr($source)->before("three")->insert("two")->toArray();

        $this->assertEquals($expected, $actual);

        // Test duplicate values
        // -------------------------------------
        $source = [1, 3, 4, 5, 3, 6];
        $expected = [1, 2, 3, 4, 5, 3, 6];
        $actual = arr($source)->before(3)->insert(2)->toArray();

        $this->assertEquals($expected, $actual);

        // Test weak comparison
        // -------------------------------------
        $source = [1, 3, 4];
        $expected = [1, 3, 4];
        $actual = arr($source)->before("3")->insert(2)->toArray();

        $this->assertEquals($expected, $actual);

        $source = [1, 3, 4];
        $expected = [1, 2, 3, 4];
        $actual = arr($source)->before("3", false)->insert(2)->toArray();

        $this->assertEquals($expected, $actual);

        // Test associative array
        // -------------------------------------
        $source = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $expected = [
            "one" => "apple",
            "avocado",
            "three" => "banana",
            "four" => "orange",
        ];
        $actual = arr($source)->before("banana")->insert("avocado")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion with specific key
        // -------------------------------------
        $source = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $expected = [
            "one" => "apple",
            "two" => "avocado",
            "three" => "banana",
            "four" => "orange",
        ];
        $actual = arr($source)->before("banana")->insert("avocado", "two")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion with specific key that already exists
        // -------------------------------------
        $source = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $expected = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $actual = arr($source)->before("banana")->insert("avocado", "four")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the beginning
        // -------------------------------------
        $source = [3, 4, 5];
        $expected = [2, 3, 4, 5];
        $actual = arr($source)->before(3)->insert(2)->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the end
        // -------------------------------------
        $source = [3, 4, 5];
        $expected = [3, 4, 5, 2];
        $actual = arr($source)->insert(2)->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the end - with key
        // -------------------------------------
        $source = [3, 4, 5];
        $expected = [3, 4, 5, "two" => 2];
        $actual = arr($source)->insert(2, "two")->toArray();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testInsertBeforeKey(): void
    {
        // Test insertion at the beginning of the array with a specific key
        // -------------------------------------
        $source = [
            "one" => 1,
            "two" => 2,
            "four" => 4,
        ];
        $expected = [
            "zero" => 0,
            "one" => 1,
            "two" => 2,
            "four" => 4,
        ];
        $actual = arr($source)->beforeKey("one")->insert(0, "zero")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion in the middle of the array with a specific key
        // -------------------------------------
        $source = [
           "one" => 1,
           "two" => 2,
           "four" => 4,
        ];
        $expected = [
            "one" => 1,
            "two" => 2,
            "three" => 3,
            "four" => 4,
        ];
        $actual = arr($source)->beforeKey("four", false)->insert(3, "three")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion of the array with a specific key that does not exits
        // -------------------------------------
        $source = [
            "one" => 1,
            "two" => 2,
            "four" => 4,
        ];
        $expected = $source;
        $actual = arr($source)->beforeKey("five", false)->insert(3, "three")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the beginning of the array with a position index
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [0, 1, 2, 4];
        $actual = arr($source)->beforeKey(0)->insert(0)->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion in the middle of the array with a position index
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [1, 2, 3, 4];
        $actual = arr($source)->beforeKey(2)->insert(3)->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion of the array with a position index that is out of range
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [1, 2, 4];
        $actual = arr($source)->beforeKey(100)->insert(3)->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testInsertAfter(): void
    {
        // Test integers
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [1, 2, 3, 4];
        $actual = arr($source)->after(2)->insert(3)->toArray();

        $this->assertEquals($expected, $actual);

        // Test strings
        // -------------------------------------
        $source = ["one", "two", "four"];
        $expected = ["one", "two", "three", "four"];
        $actual = arr($source)->after("two")->insert("three")->toArray();

        $this->assertEquals($expected, $actual);

        // Test duplicate values
        // -------------------------------------
        $source = [1, 2, 4, 5, 2, 6];
        $expected = [1, 2, 3, 4, 5, 2, 6];
        $actual = arr($source)->after(2)->insert(3)->toArray();

        $this->assertEquals($expected, $actual);

        // Test weak comparison
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [1, 2, 4];
        $actual = arr($source)->after("2")->insert(3)->toArray();

        $this->assertEquals($expected, $actual);

        $source = [1, 2, 4];
        $expected = [1, 2, 3, 4];
        $actual = arr($source)->after("2", false)->insert(3)->toArray();

        $this->assertEquals($expected, $actual);

        // Test associative array
        // -------------------------------------
        $source = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $expected = [
            "one" => "apple",
            "avocado",
            "three" => "banana",
            "four" => "orange",
        ];
        $actual = arr($source)->after("apple")->insert("avocado")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion with specific key
        // -------------------------------------
        $source = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $expected = [
            "one" => "apple",
            "two" => "avocado",
            "three" => "banana",
            "four" => "orange",
        ];
        $actual = arr($source)->after("apple")->insert("avocado", "two")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion with specific key that already exists
        // -------------------------------------
        $source = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $expected = [
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ];
        $actual = arr($source)->after("apple")->insert("avocado", "four")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the end
        // -------------------------------------
        $source = [3, 4, 5];
        $expected = [3, 4, 5, 6];
        $actual = arr($source)->insert(6)->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the end - with key
        // -------------------------------------
        $source = [3, 4, 5];
        $expected = [3, 4, 5, "six" => 6];
        $actual = arr($source)->insert(6, "six")->toArray();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testInsertAfterKey(): void
    {
        // Test insertion at the end of the array with a specific key
        // -------------------------------------
        $source = [
            "one" => 1,
            "two" => 2,
            "four" => 4,
        ];
        $expected = [
            "one" => 1,
            "two" => 2,
            "four" => 4,
            "five" => 5,
        ];
        $actual = arr($source)->afterKey("four")->insert(5, "five")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion in the middle of the array with a specific key
        // -------------------------------------
        $source = [
            "one" => 1,
            "two" => 2,
            "four" => 4,
        ];
        $expected = [
            "one" => 1,
            "two" => 2,
            "three" => 3,
            "four" => 4,
        ];
        $actual = arr($source)->afterKey("two", false)->insert(3, "three")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion of the array with a specific key that does not exits
        // -------------------------------------
        $source = [
            "one" => 1,
            "two" => 2,
            "four" => 4,
        ];
        $expected = $source;
        $actual = arr($source)->afterKey("five", false)->insert(3, "three")->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion in the middle of the array with a position index
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [1, 2, 3, 4];
        $actual = arr($source)->afterKey(1)->insert(3)->toArray();

        $this->assertEquals($expected, $actual);

        // Test insertion at the end of the array with a position index
        // -------------------------------------
        $source = [1, 2, 4];
        $expected = [1, 2, 4, 5];
        $actual = arr($source)->afterKey(2)->insert(5)->toArray();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testPositionOf(): void
    {
        $arr = arr(["one", "two", "three", 4]);

        $this->assertEquals(2, $arr->positionOf("two"));
        $this->assertEquals(4, $arr->positionOf(4));
        $this->assertNull($arr->positionOf("4"));
        $this->assertEquals(4, $arr->positionOf("4", false));
        $this->assertNull($arr->positionOf("four"));
    }

    /**
     * @return void
     */
    public function testPositionOfKey(): void
    {
        $arr = arr([
            "one" => "apple",
            "two" => "banana",
            "three" => "orange",
            4 => "avocado",
        ]);

        $this->assertEquals(2, $arr->positionOfKey("two"));
        $this->assertEquals(4, $arr->positionOfKey(4));
        $this->assertNull($arr->positionOfKey("4"));
        $this->assertEquals(4, $arr->positionOfKey("4", false));
        $this->assertNull($arr->positionOfKey("four"));
    }

    /**
     * @return void
     */
    public function testRemove(): void
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

        $this->assertEquals($expected, $source->remove("banana")->toArray());

        // Test multiple removals
        $source = arr([
            "one" => "apple",
            "three" => "banana",
            "four" => "orange",
        ]);
        $expected = [
            "one" => "apple",
        ];

        $this->assertEquals($expected, $source->remove(["banana", "orange"])->toArray());
    }
}
