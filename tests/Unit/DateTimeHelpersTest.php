<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit;

use function DataLinx\PhpUtils\{format_date, format_date_time, format_time, parse_date, parse_date_time, parse_time};

use PHPUnit\Framework\TestCase;

class DateTimeHelpersTest extends TestCase
{
    public function testParseDate(): void
    {
        setlocale(LC_TIME, 'en_US');

        $this->assertEquals('2023-01-24', parse_date('01/24/2023'));
        $this->assertEquals('2023-01-24', parse_date('1/24/2023', 'l'));

        $this->assertEquals('2023-01-24', parse_date('24.1.2023', null, 'sl'));
        $this->assertEquals('2023-01-24', parse_date('24.01.2023', null, 'sl'));

        setlocale(LC_TIME, 'sl_SI');

        $this->assertEquals('2023-01-24', parse_date('24.1.2023'));
        $this->assertEquals('2023-01-24', parse_date('24. 1. 2023.', null, 'hr'));
        $this->assertEquals('2023-01-24', parse_date('24. 01. 2023.', null, 'hr'));

        $this->assertNull(parse_date('24.1.2023 17:04'));
        $this->assertNull(parse_date('17:04'));
        $this->assertNull(parse_date('foo'));
    }

    public function testParseTime(): void
    {
        setlocale(LC_TIME, 'en_US');

        $this->assertEquals('17:04:00', parse_time('5:04 PM'));
        $this->assertEquals('17:04:12', parse_time('5:04:12 PM', 'LTS'));

        $this->assertEquals('17:04:00', parse_time('17:04', null, 'sl'));
        $this->assertEquals('17:04:12', parse_time('17:04:12', 'LTS', 'sl'));

        setlocale(LC_TIME, 'sl_SI');

        $this->assertEquals('17:04:00', parse_time('17:04'));
        $this->assertEquals('17:04:09', parse_time('17:04:09', 'H:mm:ss'));

        $this->assertNull(parse_time('24.1.2023 17:04'));
        $this->assertNull(parse_time('24.1.2023'));
        $this->assertNull(parse_time('foo'));
    }

    public function testParseDateTime(): void
    {
        setlocale(LC_TIME, 'en_US');

        $this->assertEquals('2023-01-24 17:04:00', parse_date_time('01/24/2023 5:04 PM'));
        $this->assertEquals('2023-01-24 17:04:00', parse_date_time('1/24/2023 5:04 PM'));
        $this->assertEquals('2023-01-24 17:04:12', parse_date_time('1/24/2023 5:04:12 PM', 'l LTS'));
        $this->assertEquals('2023-01-24 17:04:00', parse_date_time('24.01.2023 17:04', null, 'sl'));

        setlocale(LC_TIME, 'sl_SI');

        $this->assertEquals('2023-01-24 17:04:00', parse_date_time('24.1.2023 17:04'));
        $this->assertEquals('2023-01-24 17:04:12', parse_date_time('24.1.2023 17:04:12', 'l LTS'));

        $this->assertNull(parse_date_time('24.1.2023'));
        $this->assertNull(parse_date_time('17:04'));

        $this->assertNull(parse_date_time('24. 1. 2023. 17:04'));
        $this->assertEquals('2023-01-24 17:04:00', parse_date_time('24. 1. 2023. 17:04', null, 'hr'));
        $this->assertEquals('2023-01-24 17:04:12', parse_date_time('24. 1. 2023. 17:04:12', 'l LTS', 'hr'));
    }

    public function testFormatDate(): void
    {
        setlocale(LC_TIME, 'en_US');

        $this->assertEquals('1/24/2023', format_date(1674518400));
        $this->assertEquals('1/24/2023', format_date('2023-01-24'));
        $this->assertEquals(date('n/j/Y'), format_date()); // Just don't run the test at midnight :)
        $this->assertEquals('01/24/2023', format_date('2023-01-24', 'L'));
        $this->assertEquals('24.1.2023', format_date('2023-01-24', null, 'sl'));
        $this->assertEquals('24. 1. 2023.', format_date('2023-01-24', null, 'hr'));

        setlocale(LC_TIME, 'sl_SI');

        $this->assertEquals('24.1.2023', format_date('2023-01-24'));
        $this->assertEquals('24. jan 2023', format_date('2023-01-24', 'll'));
        $this->assertEquals('24. sij. 2023.', format_date('2023-01-24', 'll', 'hr'));

        $this->assertNull(format_date('1111-31-31'));
        $this->assertNull(format_date('foo'));
    }

    public function testFormatTime(): void
    {
        setlocale(LC_TIME, 'en_US');

        $this->assertEquals('5:04 PM', format_time(1674579852));
        $this->assertEquals('5:04 PM', format_time('17:04:12'));
        $this->assertEquals(date('g:i A'), format_time()); // There's a tiny chance this will fail
        $this->assertEquals('5:04:12 PM', format_time('17:04:12', 'LTS'));
        $this->assertEquals('7:04:12', format_time('07:04:12', 'LTS', 'sl'));

        setlocale(LC_TIME, 'sl_SI');

        $this->assertEquals('17:04', format_time('17:04:12'));

        $this->assertNull(format_time('30:30'));
        $this->assertNull(format_time('foo'));
    }

    public function testFormatDateTime(): void
    {
        setlocale(LC_TIME, 'en_US');

        $this->assertEquals('1/24/2023 5:04 PM', format_date_time(1674579852));
        $this->assertEquals('1/24/2023 5:04 PM', format_date_time('2023-01-24 17:04:12'));
        $this->assertEquals('1/24/2023 5:04 PM', format_date_time('2023-01-24 17:04:12', ''));
        $this->assertEquals('24.1.2023 7:04:12', format_date_time('2023-01-24 07:04:12', 'l LTS', 'sl'));
    }
}
