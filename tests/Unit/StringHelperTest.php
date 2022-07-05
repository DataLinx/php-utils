<?php

namespace Datalinx\PhpUtils\Tests\Unit;

use Datalinx\PhpUtils\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function testHtml2Plain()
    {
        $sampleHtml = "<p>This is a test paragraph.</p>";
        $expectedText = "This is a test paragraph.";

        $this->assertEquals($expectedText, StringHelper::html2plain($sampleHtml));

        $sampleHtml = "<p>This is a test paragraph.</p><p>This is another paragraph,<br/>but it has a line break.</p>";
        $expectedText = "This is a test paragraph.\n\nThis is another paragraph,\nbut it has a line break.";

        $this->assertEquals($expectedText, StringHelper::html2plain($sampleHtml));

        // TODO Add more cases for other line break variants
    }

    public function testSplitAddress()
    {
        $cases = [
            'Pot v X 123b' => [
                'Pot v X',
                '123b',
            ],
            'Pot  v   X 123/b ' => [
                'Pot v X',
                '123/b',
            ],
            // TODO Add more test cases
        ];

        foreach ($cases as $input => $expected) {
            $this->assertEquals($expected, StringHelper::splitAddress($input));
        }
    }
}