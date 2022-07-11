<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use PHPUnit\Framework\TestCase;

require_once './src/fluent_helpers.php';

class FluentStringTest extends TestCase
{
    public function testHtml2Plain()
    {
        // TODO Put all samples and expected results in an array
        $sampleHtml = "<p>This is a test paragraph.</p>";
        $expectedText = "This is a test paragraph.";

        $this->assertEquals($expectedText, str($sampleHtml)->htmlToPlain());

        $sampleHtml = "<p>This is a test paragraph.</p><p>This is another paragraph,<br/>but it has a line break.</p>";
        $expectedText = "This is a test paragraph.\n\nThis is another paragraph,\nbut it has a line break.";

        $this->assertEquals($expectedText, str($sampleHtml)->htmlToPlain());

        $sampleHtml = "This is the first line break.<br>This is the second line break.<br/>And this is the third one.<br />";
        $expectedText = "This is the first line break.\nThis is the second line break.\nAnd this is the third one.";

        $this->assertEquals($expectedText, str($sampleHtml)->htmlToPlain());

        $sampleHtml = "  <p>This is a test paragraph.</p>No paragraph.  ";
        $expectedText = "This is a test paragraph.\n\nNo paragraph.";

        $this->assertEquals($expectedText, str($sampleHtml)->htmlToPlain());
    }

    public function testLinkHashtags()
    {
        // TODO Fix all anchor hrefs - they should include the tag
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
            $this->assertEquals($expected, str($case)->linkHashtags("https://www.example.com/"));
        }
    }

    public function testCamel2Snake()
    {
        $cases = [
            "camelCase" => "camel_case",
            "PascalCase" => "pascal_case",
        ];

        foreach ($cases as $case => $expected) {
            $this->assertEquals($expected, str($case)->camelToSnake());
        }
    }

    public function testSnake2Camel()
    {
        $this->assertEquals("PascalCase", str("pascal_case")->snakeToCamel());

        $this->assertEquals("camelCase", str("camel_case")->snakeToCamel(false));
    }

    public function testClean()
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
            $this->assertEquals($expected, str($case)->clean());
        }
    }

    public function testToAddressArray()
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
            $this->assertEquals($expected, str($input)->toAddressArray());
        }
    }
}
