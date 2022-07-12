<?php

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use PHPUnit\Framework\TestCase;

require_once './src/fluent_helpers.php';

class FluentBarcodeTest extends TestCase
{
    public function testBasic()
    {
        if (! file_exists('./build')) {
            mkdir('./build');
        }

        $barcode = barcode('9313920040041')->setFormat('svg');

        $file = $barcode->save();
        $this->assertFileExists($file);

        $file = $barcode->setFormat('png')->save();
        $this->assertFileExists($file);

        // Save to certain file
        $file = $barcode->save('./build/9313920040041.svg');
        $this->assertFileExists($file);
    }

    public function testEmbed()
    {
        $this->assertNotEmpty(barcode('9313920040041')->embed());
    }
}
