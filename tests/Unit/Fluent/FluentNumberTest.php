<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FluentNumberTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetAndGet(): void
    {
        $amount = num(123);

        $this->assertEquals(123, $amount->getValue());

        $amount->setValue(22.55);

        $this->assertEquals(22.55, $amount->getValue());
    }

    /**
     * @return void
     */
    public function testToString(): void
    {
        $amount = num(55);

        $this->assertEquals('5545', $amount . 45);

        // Change locale
        setlocale(LC_MESSAGES, 'sl_SI');

        $decimal = num(123.456);

        $this->assertEquals('123,456', (string)$decimal);
    }

    /**
     * @return void
     */
    public function testToRoman(): void
    {
        $cases = [
            1 => "I",
            2 => "II",
            3 => "III",
            4 => "IV",
            5 => "V",
            '6.02' => "VI",
            7 => "VII",
            8 => "VIII",
            9 => "IX",
            10 => "X",
            30 => "XXX",
            '55.55' => "LV",
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

    public function testTypes(): void
    {
        // Valid integers
        $this->assertTrue(num(123)->isInteger());
        $this->assertTrue(num(-123)->isInteger());
        $this->assertTrue(num((int)'123')->isInteger());
        $this->assertTrue(num((int)'-123')->isInteger());

        // Invalid integers
        $this->assertFalse(num(123.45)->isInteger());
        $this->assertFalse(num('123.45')->isInteger());

        // Valid decimals
        $this->assertTrue(num(123.45)->isDecimal());
        $this->assertTrue(num('123.45')->isDecimal());
        $this->assertTrue(num('123')->isDecimal());

        // Invalid decimals
        $this->assertFalse(num(123)->isDecimal());
    }

    public function testNonNumericType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        num('abc');
    }

    public function testStringWithNumberType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        num('abc123');
    }

    public function testNumberWithStringType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        num('123abc');
    }

    public function testFormat(): void
    {
        // American style
        // ------------------------------
        setlocale(LC_MESSAGES, 'en_US');

        // Integers
        $integer = num(1234567);
        $this->assertEquals('1,234,567', (string)$integer);
        $this->assertEquals('1,234,567.00', $integer->format(2));

        // Decimals
        $decimal = num(1234.56789);
        $this->assertEquals('1,234.56789', (string)$decimal);
        $this->assertEquals('1,234.56789', $decimal->format(5));
        $this->assertEquals('1,234.567890', $decimal->format(6));
        $this->assertEquals('1,234.57', $decimal->format(2));
        $this->assertEquals('1,235', $decimal->format(0));

        $this->assertEquals('1.23456789', (string)num(1.23456789));
        $this->assertEquals('1.2345678987', (string)num(1.2345678987));
        $this->assertEquals('1.2345678988', (string)num(1.23456789876));
        $this->assertEquals('1.23456789876', num(1.23456789876)->format(11));

        // Decimals with trimming
        $trimmed = num(123.45);
        $this->assertEquals('123.45000', $trimmed->format(5));
        $this->assertEquals('123.45', $trimmed->format(5, 2));
        $this->assertEquals('123.45', $trimmed->format(null, 2));

        $trimmed_2 = num(1234.000);
        $this->assertEquals('1,234.00', (string)$trimmed_2);
        $this->assertEquals('1,234.000', $trimmed_2->format(3));
        $this->assertEquals('1,234', $trimmed_2->format(3, 0));
        $this->assertEquals('1,234.00', $trimmed_2->format(3, 2));

        // Other styles
        // ------------------------------
        // Common EU style
        setlocale(LC_MESSAGES, 'de_DE');

        $this->assertEquals('1.234.567', (string)$integer);
        $this->assertEquals('1.234,56789', (string)$decimal);

        // Frenchmen use Narrow non-breaking space for thousands separator
        setlocale(LC_MESSAGES, 'fr_FR');

        $this->assertEquals("1\u{202F}234\u{202F}567", (string)$integer);
        $this->assertEquals("1\u{202F}234,56789", (string)$decimal);

        // Override locale on-the-fly
        // ------------------------------
        setlocale(LC_MESSAGES, 'en_US');

        // Short locale notation
        $this->assertEquals('1.234.567', $integer->format(null, null, 'sl'));
        $this->assertEquals('1.234,56789', $decimal->format(null, null, 'sl'));

        // Long locale notation
        $this->assertEquals('1.234.567', $integer->format(null, null, 'sl_SI'));
        $this->assertEquals('1.234,56789', $decimal->format(null, null, 'sl_SI'));
    }

    public function testAsPercent(): void
    {
        setlocale(LC_MESSAGES, 'en_US');

        $value = num(75);

        $this->assertEquals('75%', $value->asPercent());
        $this->assertEquals('75.00%', $value->asPercent(2));
        $this->assertEquals('75.000%', $value->asPercent(3));

        $value = num(75.00);

        $this->assertEquals('75.00%', $value->asPercent());
        $this->assertEquals('75.00%', $value->asPercent(2));
        $this->assertEquals('75.000%', $value->asPercent(3));

        $value = num(123.456);

        $this->assertEquals('123.456%', $value->asPercent());
        $this->assertEquals('123.46%', $value->asPercent(2));
        $this->assertEquals('123.456%', $value->asPercent(3));

        $value = num(123.45678987655);

        $this->assertEquals('123.4567898766%', $value->asPercent());
        $this->assertEquals('123.46%', $value->asPercent(2));
        $this->assertEquals('123.456789876550%', $value->asPercent(12));

        $value = num(-123.456);

        $this->assertEquals('-123.456%', $value->asPercent());
        $this->assertEquals('-123.46%', $value->asPercent(2));
        $this->assertEquals('-123.456%', $value->asPercent(3));
    }

    public function testAsMoney(): void
    {
        setlocale(LC_MESSAGES, 'en_US');

        $amount = num(1234567.89);

        // Test formatting
        $this->assertEquals("€1,234,567.89", $amount->asMoney('EUR'));
        $this->assertEquals("$1,234,567.89", $amount->asMoney('USD'));
        $this->assertEquals("₹1,234,567.89", $amount->asMoney('INR'));
        $this->assertEquals("CN¥1,234,567.89", $amount->asMoney('CNY'));
        $this->assertEquals("¥1,234,567.89", $amount->asMoney('JPY'));
        $this->assertEquals("RUR\u{00A0}1,234,567.89", $amount->asMoney('RUR'));

        // Test decimals
        $this->assertEquals("€1,234.56789", num(1234.56789)->asMoney('EUR'));
        $this->assertEquals("€1,234.57", num(1234.56789)->asMoney('EUR', 2));
        $this->assertEquals("€1,234.56789", num(1234.56789)->asMoney('EUR', 5));
        $this->assertEquals("€1,234.56000", num(1234.56)->asMoney('EUR', 5));
        $this->assertEquals("€1,234.56", num(1234.56)->asMoney('EUR', 5, 2));
        $this->assertEquals("€1,234.567", num(1234.567)->asMoney('EUR', 5, 2));

        // Test more locale variants
        // ----------------------------
        setlocale(LC_MESSAGES, 'sl_SI');

        $this->assertEquals("1.234.567,89\u{00A0}€", $amount->asMoney('EUR'));
        $this->assertEquals("1.234.567,89\u{00A0}$", $amount->asMoney('USD'));
        $this->assertEquals("1.234.567,89\u{00A0}₹", $amount->asMoney('INR'));
        $this->assertEquals("1.234.567,89\u{00A0}CN¥", $amount->asMoney('CNY'));
        $this->assertEquals("1.234.567,89\u{00A0}¥", $amount->asMoney('JPY'));
        $this->assertEquals("1.234.567,89\u{00A0}RUR", $amount->asMoney('RUR'));

        setlocale(LC_MESSAGES, 'ru_RU');

        $this->assertEquals("1\u{00A0}234\u{00A0}567,89\u{00A0}€", $amount->asMoney('EUR'));
        $this->assertEquals("1\u{00A0}234\u{00A0}567,89\u{00A0}$", $amount->asMoney('USD'));
        $this->assertEquals("1\u{00A0}234\u{00A0}567,89\u{00A0}₹", $amount->asMoney('INR'));
        $this->assertEquals("1\u{00A0}234\u{00A0}567,89\u{00A0}CN¥", $amount->asMoney('CNY'));
        $this->assertEquals("1\u{00A0}234\u{00A0}567,89\u{00A0}¥", $amount->asMoney('JPY'));
        $this->assertEquals("1\u{00A0}234\u{00A0}567,89\u{00A0}р.", $amount->asMoney('RUR'));
    }

    public function testAsFileSize(): void
    {
        setlocale(LC_MESSAGES, 'en_US');

        $this->assertEquals("123\u{00A0}B", num(123)->asFileSize());

        $this->assertEquals("1.23\u{00A0}kB", num(1234)->asFileSize());
        $this->assertEquals("1.234\u{00A0}kB", num(1234)->asFileSize(3));

        $this->assertEquals("1.23\u{00A0}MB", num(1234567)->asFileSize());
        $this->assertEquals("1.23\u{00A0}GB", num(1234567890)->asFileSize());
        $this->assertEquals("1.23\u{00A0}TB", num(1234567890000)->asFileSize());
        $this->assertEquals("1.23\u{00A0}PB", num(1234567890000000)->asFileSize());
    }

    public function testInvalidFileSize(): void
    {
        $num = num(123.45);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only integer values can be formatted as file size!');

        $num->asFileSize();
    }
}
