<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FluentStringTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetAndGet(): void
    {
        $sentence = str("First string");

        $this->assertEquals("First string", $sentence->getValue());

        $sentence->setValue("Second string");

        $this->assertEquals("Second string", $sentence->getValue());
    }

    /**
     * @return void
     */
    public function testHtml2Plain(): void
    {
        $cases = [
            ["input" => "<p>This is a test paragraph.</p>", "expected" => "This is a test paragraph."],
            ["input" => "<p>This is a test paragraph.</p><p>This is another paragraph,<br/>but it has a line break.</p>", "expected" => "This is a test paragraph." . PHP_EOL . PHP_EOL . "This is another paragraph," . PHP_EOL . "but it has a line break."],
            ["input" => "This is the first line break.<br>This is the second line break.<br/>And this is the third one.<br />", "expected" => "This is the first line break." . PHP_EOL . "This is the second line break." . PHP_EOL . "And this is the third one."],
            ["input" => "  <p>This is a test paragraph.</p>No paragraph.  ", "expected" => "This is a test paragraph." . PHP_EOL . PHP_EOL . "No paragraph."],
        ];

        foreach ($cases as $case) {
            $this->assertEquals($case["expected"], str($case["input"])->htmlToPlain());
        }
    }

    /**
     * @return void
     */
    public function testLinkHashtags(): void
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
    public function testCamel2Snake(): void
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
    public function testSnake2Camel(): void
    {
        $this->assertEquals("PascalCase", str("pascal_case")->snakeToCamel());

        $this->assertEquals("camelCase", str("camel_case")->snakeToCamel(false));
    }

    /**
     * @return void
     */
    public function testClean(): void
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
    public function testToAddressArray(): void
    {
        $cases = [
            "Pot v X 123b" => [
                "Pot v X",
                "123b",
            ],
            "Pot  v   X 123/b " => [
                "Pot v X",
                "123/b",
            ],
            "Aljaževa, 20 a" => [
                "Aljaževa",
                "20a",
            ],
            "Aškerčeva cesta, 22" => [
                "Aškerčeva cesta",
                "22",
            ],
            "B. Radić 88," => [
                "B. Radić",
                "88",
            ],
            "Bakovci, Cvetna ulica 24" => [
                "Bakovci, Cvetna ulica",
                "24",
            ],
            "Cesta 15.aprila 35" => [
                "Cesta 15.aprila",
                "35",
            ],
            "Cesta 20. Julija 13" => [
                "Cesta 20. Julija",
                "13",
            ],
            "Cesta II. Grupe Odredov 13c, 13c" => [
                "Cesta II. Grupe Odredov",
                "13c",
            ],
            "Delavska C.57," => [
                "Delavska C.",
                "57",
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
    public function testParsePlaceholders(): void
    {
        // Set encoding detection order - from widest to narrowest
        // See this comment from 17 years ago: https://www.php.net/manual/en/function.mb-detect-encoding.php#51389
        // This is needed because we use mb_detect_encoding() in the function implementation
        mb_detect_order(["UTF-8", "ISO-8859-2"]);

        // UTF-8 encoded strings
        // -------------
        $subject = "Hello, {name} from {place}!";
        $placeholders = [
            "name" => "George",
            "place" => "the Jungle",
        ];

        $this->assertEquals("Hello, George from the Jungle!", str($subject)->parsePlaceholders($placeholders));

        // UTF-8 strings with caron characters
        // -------------
        $placeholders = [
            "name" => "Frančiška Žorž",
            "place" => "Šared",
        ];

        $this->assertEquals("Hello, Frančiška Žorž from Šared!", (string)str($subject)->parsePlaceholders($placeholders));

        // Mixed encodings — UTF-8 subject and ISO-8859-2 placeholder values
        // -------------
        $subject = "Živjo, {name} iz dišečega kraja {place} s {amount} € ✅!";
        $this->assertEquals("UTF-8", mb_detect_encoding($subject));

        $placeholders = [
            "name" => mb_convert_encoding("Frančiška Žorž", "ISO-8859-2", "UTF-8"),
            "place" => mb_convert_encoding("Šared", "ISO-8859-2", "UTF-8"),
            "amount" => 100,
        ];

        $this->assertEquals("ISO-8859-2", mb_detect_encoding($placeholders["name"]));
        $this->assertEquals("ISO-8859-2", mb_detect_encoding($placeholders["place"]));

        $str = (string)str($subject)->parsePlaceholders($placeholders);

        $this->assertEquals("UTF-8", mb_detect_encoding($str));
        $this->assertEquals("Živjo, Frančiška Žorž iz dišečega kraja Šared s 100 € ✅!", $str);

        // All ISO-8859-2 strings
        // -------------
        $subject = mb_convert_encoding("Živjo, {name} iz dišečega kraja {place}!", "ISO-8859-2", "UTF-8");
        $this->assertEquals("ISO-8859-2", mb_detect_encoding($subject));

        $placeholders = [
            "name" => mb_convert_encoding("Frančiška Žorž", "ISO-8859-2", "UTF-8"),
            "place" => mb_convert_encoding("Šared", "ISO-8859-2", "UTF-8"),
        ];

        $this->assertEquals("ISO-8859-2", mb_detect_encoding($placeholders["name"]));
        $this->assertEquals("ISO-8859-2", mb_detect_encoding($placeholders["place"]));

        $str = (string)str($subject)->parsePlaceholders($placeholders);

        $this->assertEquals("ISO-8859-2", mb_detect_encoding($str));
        $this->assertEquals(mb_convert_encoding("Živjo, Frančiška Žorž iz dišečega kraja Šared!", "ISO-8859-2", "UTF-8"), $str);

        // Include time placeholders
        // -------------
        $expected = str('Hi {name}, the time is now {%r}!')->parsePlaceholders(['name' => 'George'], true)->getValue();

        $this->assertEquals($expected, 'Hi George, the time is now ' . date('r') .'!');
    }

    public function testParseTimePlaceholders(): void
    {
        // Common usage
        // -------------
        $actual = str('The time is {%H}:{%i}:{%s}, while the date is {%D} {%j} {%M} {%Y}!')->parseTimePlaceholders()->getValue();
        $expected = sprintf(
            'The time is %s:%s:%s, while the date is %s %s %s %s!',
            date('H'),
            date('i'),
            date('s'),
            date('D'),
            date('j'),
            date('M'),
            date('Y'),
        );

        $this->assertEquals($expected, $actual);

        // Full date time
        // -------------
        $expected = str('Full date time is {%r}')->parseTimePlaceholders()->getValue();

        $this->assertEquals($expected, 'Full date time is '. date('r'));

        // Specific time
        // -------------
        $expected = str('Full date time is {%r}')->parseTimePlaceholders(strtotime('2023-01-01 12:21:12'))->getValue();
        $this->assertEquals($expected, 'Full date time is '. date('r', strtotime('2023-01-01 12:21:12')));
    }

    /**
     * @return void
     */
    public function testTruncate(): void
    {
        $this->assertEquals(
            "This is a happy...",
            str("This is a happy, string.")->truncate(20, null, true)
        );

        $this->assertEquals(
            "This is a happy...",
            str("This is a happy, string.")->truncate(19)
        );

        $this->assertEquals(
            "This is a happy string.",
            str("This is a happy string.")->truncate(0)
        );

        $this->assertEquals(
            "This is a string...",
            str("This is a string that should be truncated after 20 characters.")->truncate(20)
        );

        $this->assertEquals(
            "This is a list with apples, strawberries, bananas etc.",
            str("This is a list with apples, strawberries, bananas and lemons.")->truncate(55, " etc.")
        );

        $this->assertEquals(
            "This is a list with apples, strawberries, bananas...",
            str("This is a list with apples, strawberries, bananas and lemons.")->truncate(52, null, true)
        );

        $this->assertEquals(
            "This is a tex...n the middle.",
            str("This is a text truncated in the middle.")->truncate(30, null, false, true)
        );
    }

    /**
     * @return void
     */
    public function testPrepMetaDescription(): void
    {
        $this->assertEquals("This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote. This is a very nice meta description...", str("This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote.")->prepMetaDescription());
        $this->assertEquals("This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote. This is a very nice meta description...", str("<p>This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote.</p>")->prepMetaDescription());
        $this->assertEquals("This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote. This is a very nice meta description...", str("<h1>This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote. This is a very nice meta description that we just wrote.</h1>")->prepMetaDescription());
        $this->assertEquals("This is a very nice meta description that we just...", str("This is a very nice meta description that we just wrote.")->prepMetaDescription(52));
        $this->assertEquals("This &lt; &gt; is a very nice meta description symbol...", str("This < > is a very nice meta description symbol we have here.")->prepMetaDescription(52));
    }

    /**
     * @return void
     * @noinspection HttpUrlsUsage
     */
    public function testExtractYouTubeHash(): void
    {
        $this->assertEquals("FQPbLJ__wdQ", str("https://www.youtube.com/watch?v=FQPbLJ__wdQ")->extractYouTubeHash());
        $this->assertEquals("FQPbLJ__wdQ", str("http://www.youtube.com/watch?v=FQPbLJ__wdQ")->extractYouTubeHash());
        $this->assertEquals("FQPbLJ__wdQ", str("www.youtube.com/watch?v=FQPbLJ__wdQ")->extractYouTubeHash());
        $this->assertEquals("FQPbLJ__wdQ", str("http://youtu.be/FQPbLJ__wdQ")->extractYouTubeHash());
        $this->assertEquals("FQPbLJ__wdQ", str("https://youtu.be/FQPbLJ__wdQ")->extractYouTubeHash());
        $this->assertNull(str("https://you.be/FQPbLJ__wdQ")->extractYouTubeHash());
        $this->assertEquals("W6eQhzKb0lc", str("https://www.youtube.com/shorts/W6eQhzKb0lc")->extractYouTubeHash());
    }

    public function testUppercaseFirst(): void
    {
        $this->assertEquals('Črt', str('črt')->uppercaseFirst());
        $this->assertEquals('Šerbi', str('šerbi')->uppercaseFirst());
        $this->assertEquals('Žan', str('žan')->uppercaseFirst());
        $this->assertEquals('Çakmak', str('çakmak')->uppercaseFirst());
    }

    public function testTrim(): void
    {
        $this->assertEquals('Test', str('﻿﻿Test﻿﻿')->trim());
        $this->assertEquals('Test', str("\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}Test\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}")->trim());
    }

    public function testIsEmailDomainValid(): void
    {
        $this->assertFalse(str('test')->isEmailDomainValid());
        $this->assertFalse(str('test@snailmailgmail123456789.com')->isEmailDomainValid());
        $this->assertTrue(str('test@gmail.com')->isEmailDomainValid());
        $this->assertTrue(str('test@hotmail.com')->isEmailDomainValid());
    }

    public function testChunks(): void
    {
        $this->assertEquals(['Too short'], str('Too short')->chunks(10));

        $str = 'The modification is a neutral cosmonaut.';
        $this->assertEquals([$str], str($str)->chunks(mb_strlen($str) + 1));
        $this->assertEquals([$str], str($str)->chunks(mb_strlen($str)));

        $this->assertEquals(['The modification is ', 'a neutral cosmonaut.'], str($str)->chunks(20));
        $this->assertEquals(['The modificatio', 'n is a neutral ', 'cosmonaut.'], str($str)->chunks(15));

        $this->assertEquals(['The modifica', 'tion is a ne', 'utral cosmon', 'aut.'], str($str)->chunks(12));

        // Test preventing word breaks
        $this->assertEquals(['The', 'modification', 'is a neutral', 'cosmonaut.'], str($str)->chunks(12, true));
        $this->assertEquals(['The', 'modificat.', 'is a', 'neutral', 'cosmonaut.'], str($str)->chunks(10, true));

        // Abbreviating the first word
        $str_2 = 'Something short';
        $this->assertEquals(['Somet.', 'short'], str($str_2)->chunks(6, true));

        // Abbreviating the last word
        $str_3 = 'Longer something';
        $this->assertEquals(['Longer', 'somet.'], str($str_3)->chunks(6, true));

        // Abbreviating all
        $str_4 = 'This looks strange';
        $this->assertEquals(['Th.', 'lo.', 'st.'], str($str_4)->chunks(3, true));

        // Multi-byte support
        $this->assertEquals(['Kar je več', 'od šest', 'črk, je že', 'preveč'], str('Kar je več od šest črk, je že preveč')->chunks(10, true));

        // Bad chunk size
        $this->expectException(InvalidArgumentException::class);
        str('test')->chunks(1);
    }

    public function testGetLength(): void
    {
        $this->assertEquals(0, str('')->getLength());
        $this->assertEquals(3, str('foo')->getLength());
        $this->assertEquals(8, str('foočćžšđ')->getLength());
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(str('')->isEmpty());
        $this->assertTrue(str(' ')->isEmpty());
        $this->assertTrue(str('    ')->isEmpty());
        $this->assertTrue(str("\u{2000}")->isEmpty());

        $this->assertFalse(str('0')->isEmpty());
        $this->assertFalse(str('abc')->isEmpty());
    }

    public function testHasHtmlTags(): void
    {
        $this->assertTrue(str('<p>Hi!</p>')->hasHtmlTags());
        $this->assertTrue(str('Hi!<hr/>')->hasHtmlTags());
        $this->assertTrue(str('<h1>Hello</h1>'. PHP_EOL . '<h2>world!</h2>')->hasHtmlTags());

        $this->assertFalse(str('Hi!')->hasHtmlTags());
        $this->assertTrue(str('Hello'. PHP_EOL . 'world!')->hasHtmlTags());
    }
}
