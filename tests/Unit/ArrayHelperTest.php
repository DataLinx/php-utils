<?php declare(strict_types=1);


namespace DataLinx\PhpUtils\Tests\Unit;

use DataLinx\PhpUtils\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
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
            $this->assertEquals($case["expected"], ArrayHelper::flatten($case["input"]));
        }
    }
}
