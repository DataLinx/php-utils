<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use PHPUnit\Framework\TestCase;

require_once './src/fluent_helpers.php';

class FluentStringTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetAndGet()
    {
        $sentence = str("First string");

        $this->assertEquals("First string", $sentence->getValue());

        $sentence->setValue("Second string");

        $this->assertEquals("Second string", $sentence->getValue());
    }

    /**
     * @return void
     */
    public function testHtml2Plain()
    {
        $cases = [
            ["input" => "<p>This is a test paragraph.</p>", "expected" => "This is a test paragraph."],
            ["input" => "<p>This is a test paragraph.</p><p>This is another paragraph,<br/>but it has a line break.</p>", "expected" => "This is a test paragraph.\n\nThis is another paragraph,\nbut it has a line break."],
            ["input" => "This is the first line break.<br>This is the second line break.<br/>And this is the third one.<br />", "expected" => "This is the first line break.\nThis is the second line break.\nAnd this is the third one."],
            ["input" => "  <p>This is a test paragraph.</p>No paragraph.  ", "expected" => "This is a test paragraph.\n\nNo paragraph."],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], str($case["input"])->htmlToPlain());
        }
    }

    /**
     * @return void
     */
    public function testLinkHashtags()
    {
        $cases = [
            "#this is something" => "<a class=\"hashtag\" href=\"https://www.example.com/this\" data-tag=\"this\">#this</a> is something",
            "this #is something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a> something",
            "this is #something" => "this is <a class=\"hashtag\" href=\"https://www.example.com/something\" data-tag=\"something\">#something</a>",
            "#this is #something" => "<a class=\"hashtag\" href=\"https://www.example.com/this\" data-tag=\"this\">#this</a> is <a class=\"hashtag\" href=\"https://www.example.com/something\" data-tag=\"something\">#something</a>",
            "#something" => "<a class=\"hashtag\" href=\"https://www.example.com/something\" data-tag=\"something\">#something</a>",
            "this is something" => "this is something",
            "" => "",
            "this #is. something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>. something",
            "this #is, something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>, something",
            "this #is; something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>; something",
            "this #is? something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>? something",
            "this #is! something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>! something",
            "this #is: something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>: something",
            "this is #something!" => "this is <a class=\"hashtag\" href=\"https://www.example.com/something\" data-tag=\"something\">#something</a>!",
            "#this? is #something," => "<a class=\"hashtag\" href=\"https://www.example.com/this\" data-tag=\"this\">#this</a>? is <a class=\"hashtag\" href=\"https://www.example.com/something\" data-tag=\"something\">#something</a>,",
            "this #is... something" => "this <a class=\"hashtag\" href=\"https://www.example.com/is\" data-tag=\"is\">#is</a>... something",
        ];

        foreach ($cases as $case => $expected) {
            $this->assertEquals($expected, str($case)->linkHashtags("https://www.example.com/"));
        }
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testSnake2Camel()
    {
        $this->assertEquals("PascalCase", str("pascal_case")->snakeToCamel());

        $this->assertEquals("camelCase", str("camel_case")->snakeToCamel(false));
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
            'Aljaževa, 20 a' => [
                'Aljaževa',
                '20a'
            ],
            'Aškerčeva cesta, 22' => [
                'Aškerčeva cesta',
                '22'
            ],
            'B. Radić 88,' => [
                'B. Radić',
                '88'
            ],
            'Bakovci, Cvetna ulica 24' => [
                'Bakovci, Cvetna ulica',
                '24'
            ],
            'Cesta 15.aprila 35' => [
                'Cesta 15.aprila',
                '35'
            ],
            'Cesta 20. Julija 13' => [
                'Cesta 20. Julija',
                '13'
            ],
            'Cesta II. Grupe Odredov 13c, 13c' => [
                'Cesta II. Grupe Odredov',
                '13c'
            ],
            'Delavska C.57,' => [
                'Delavska C.',
                '57'
            ],
        ];

        foreach ($cases as $input => $expected) {
            $address = str($input)->toAddressArray();
            $this->assertIsArray($address, "Input address \"$input\" failed to parse to address array.");
            $this->assertEquals($expected, $address);
        }

        $invalidAddress = "?+*/**, 15='+";
        $this->assertEquals(null, str($invalidAddress)->toAddressArray());
    }

    /**
     * @return void
     */
    public function testParsePlaceholders()
    {
        // Set encoding detection order - from widest to narrowest
        // See this comment from 17 years ago: https://www.php.net/manual/en/function.mb-detect-encoding.php#51389
        // This is needed because we use mb_detect_encoding() in the function implementation
        mb_detect_order(['UTF-8', 'ISO-8859-2']);

        // UTF-8 encoded strings
        // -------------
        $subject = 'Hello, {name} from {place}!';
        $placeholders = [
            'name' => 'George',
            'place' => 'the Jungle',
        ];

        $this->assertEquals('Hello, George from the Jungle!', str($subject)->parsePlaceholders($placeholders));

        // UTF-8 strings with caron characters
        // -------------
        $placeholders = [
            'name' => 'Frančiška Žorž',
            'place' => 'Šared',
        ];

        $this->assertEquals("Hello, Frančiška Žorž from Šared!", (string)str($subject)->parsePlaceholders($placeholders));

        // Mixed encodings — UTF-8 subject and ISO-8859-2 placeholder values
        // -------------
        $subject = 'Živjo, {name} iz dišečega kraja {place} s {amount} € ✅!';
        $this->assertEquals('UTF-8', mb_detect_encoding($subject));

        $placeholders = [
            'name' => mb_convert_encoding('Frančiška Žorž', 'ISO-8859-2', 'UTF-8'),
            'place' => mb_convert_encoding('Šared', 'ISO-8859-2', 'UTF-8'),
            'amount' => 100,
        ];

        $this->assertEquals('ISO-8859-2', mb_detect_encoding($placeholders['name']));
        $this->assertEquals('ISO-8859-2', mb_detect_encoding($placeholders['place']));

        $str = (string)str($subject)->parsePlaceholders($placeholders);

        $this->assertEquals('UTF-8', mb_detect_encoding($str));
        $this->assertEquals('Živjo, Frančiška Žorž iz dišečega kraja Šared s 100 € ✅!', $str);

        // All ISO-8859-2 strings
        // -------------
        $subject = mb_convert_encoding('Živjo, {name} iz dišečega kraja {place}!', 'ISO-8859-2', 'UTF-8');
        $this->assertEquals('ISO-8859-2', mb_detect_encoding($subject));

        $placeholders = [
            'name' => mb_convert_encoding('Frančiška Žorž', 'ISO-8859-2', 'UTF-8'),
            'place' => mb_convert_encoding('Šared', 'ISO-8859-2', 'UTF-8'),
        ];

        $this->assertEquals('ISO-8859-2', mb_detect_encoding($placeholders['name']));
        $this->assertEquals('ISO-8859-2', mb_detect_encoding($placeholders['place']));

        $str = (string)str($subject)->parsePlaceholders($placeholders);

        $this->assertEquals('ISO-8859-2', mb_detect_encoding($str));
        $this->assertEquals(mb_convert_encoding('Živjo, Frančiška Žorž iz dišečega kraja Šared!', 'ISO-8859-2', 'UTF-8'), $str);
    }
}