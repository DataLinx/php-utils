<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit;

use DataLinx\PhpUtils\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testIsValidDomain(): void
    {
        $this->assertFalse(Email::isValidDomain('test'));
        $this->assertFalse(Email::isValidDomain('test@snailmailgmail123456789.com'));
        $this->assertTrue(Email::isValidDomain('test@gmail.com'));
        $this->assertTrue(Email::isValidDomain('test@hotmail.com'));
    }
}