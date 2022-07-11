<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use PHPUnit\Framework\TestCase;

require './src/fluent_helpers.php';

class FluentNumberTest extends TestCase
{
    public function testToRoman()
    {
        $cases = [
            1 => "I",
            2 => "II",
            3 => "III",
            4 => "IV",
            5 => "V",
            6.02 => "VI",
            7 => "VII",
            8 => "VIII",
            9 => "IX",
            10 => "X",
            30 => "XXX",
            55.55 => "LV",
            99 => "XCIX",
            133 => "CXXXIII",
            199 => "CXCIX",
            587 => "DLXXXVII",
            1001 => "MI",
            3333 => "MMMCCCXXXIII",
        ];

        foreach ($cases as $case => $expected) {
            $this->assertEquals($expected, num($case)->toRoman());
        }
    }
}