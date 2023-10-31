<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use DataLinx\PhpUtils\Fluent\FluentPhoneNumber;
use PHPUnit\Framework\TestCase;

class FluentPhoneNumberTest extends TestCase
{
    public function testFrom(): void
    {
        // Microsoft support in Slovenia :)
        $number = FluentPhoneNumber::from('(01) 584 61 00', 'si');

        $this->assertIsObject($number);
        $this->assertEquals('386', $number->getCountryCode());
        $this->assertEquals('15846100', $number->getNationalNumber());

        // MS support in Croatia, international format, uppercase country code
        $number = FluentPhoneNumber::from('+385 1 4802 500', 'HR');

        $this->assertIsObject($number);
        $this->assertEquals('385', $number->getCountryCode());

        // Google Switzerland, invalid number for region
        $number = FluentPhoneNumber::from('044 668 1800', 'si');
        $this->assertNull($number);

        $number = FluentPhoneNumber::from('044 668 1800', 'ch');
        $this->assertIsObject($number);
    }

    public function testFormat(): void
    {
        $number = FluentPhoneNumber::from('(01) 584 61 00', 'si');
        $this->assertEquals('+386 1 584 61 00', $number->format());
        $this->assertEquals('(01) 584 61 00', $number->formatNational());
        $this->assertEquals('tel:+386-1-584-61-00', $number->formatURI());

        $number = FluentPhoneNumber::from('+385 1 4802 500', 'HR');
        $this->assertEquals('01 4802 500', $number->formatNational());
    }
}
