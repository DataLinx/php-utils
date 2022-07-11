<?php

namespace DataLinx\PhpUtils\Tests\Unit;

use DataLinx\PhpUtils\StringHelper;
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

        $sampleHtml = "This is the first line break.<br>This is the second line break.<br/>And this is the third one.<br />";
        $expectedText = "This is the first line break.\nThis is the second line break.\nAnd this is the third one.";

        $this->assertEquals($expectedText, StringHelper::html2plain($sampleHtml));

        $sampleHtml = "  <p>This is a test paragraph.</p>No paragraph.  ";
        $expectedText = "This is a test paragraph.\n\nNo paragraph.";

        $this->assertEquals($expectedText, StringHelper::html2plain($sampleHtml));
    }

    public function testLinkHashtags()
    {
        $cases = [
            "#this is something" => "<a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"this\">#this</a> is something",
            "this #is something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a> something",
            "this is #something" => "this is <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"something\">#something</a>",
            "#this is #something" => "<a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"this\">#this</a> is <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"something\">#something</a>",
            "#something" => "<a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"something\">#something</a>",
            "this is something" => "this is something",
            "" => "",
            "this #is. something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>. something",
            "this #is, something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>, something",
            "this #is; something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>; something",
            "this #is? something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>? something",
            "this #is! something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>! something",
            "this #is: something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>: something",
            "this is #something!" => "this is <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"something\">#something</a>!",
            "#this? is #something," => "<a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"this\">#this</a>? is <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"something\">#something</a>,",
            "this #is... something" => "this <a class=\"hashtag\" href=\"https://www.example.com/\" data-tag=\"is\">#is</a>... something",
        ];

        foreach ($cases as $case => $expected) {
            $this->assertEquals($expected, StringHelper::linkHashtags($case, "https://www.example.com/"));
        }
    }

    public function testCamel2Snake()
    {
        $cases = [
            "camelCase" => "camel_case",
            "PascalCase" => "pascal_case",
        ];

        foreach ($cases as $case => $expected) {
            $this->assertEquals($expected, StringHelper::camel2snake($case));
        }
    }

    public function testSnake2Camel()
    {
        $this->assertEquals("PascalCase", StringHelper::snake2camel("pascal_case"));

        $this->assertEquals("camelCase", StringHelper::snake2camel("camel_case", false));
    }

    public function testCleanString()
    {
        $cases = [
            "mark  " => "mark",
            "Johnny Bravo" => "Johnny Bravo",
            "Johnny  Bravo" => "Johnny Bravo",
            "Johnny   Bravo" => "Johnny Bravo",
            "Johnny    Bravo" => "Johnny Bravo",
            "Johnny  Bravo  " => "Johnny Bravo",
            "  Johnny Bravo" => "Johnny Bravo",
            "  Danny Robinson    " => "Danny Robinson",
            "" => "",
        ];

        foreach ($cases as $case => $expected) {
            $this->assertEquals($expected, StringHelper::cleanString($case));
        }
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
            // TODO Add more test cases: Omer bo poslal seznam caseou
        ];

        foreach ($cases as $input => $expected) {
            $this->assertEquals($expected, StringHelper::splitAddress($input));
        }
    }

    public function testInt2Roman()
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
            $this->assertEquals($expected, StringHelper::int2roman($case));
        }
    }
}
