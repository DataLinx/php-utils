<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use Exception;
use PHPUnit\Framework\TestCase;
use Picqer\Barcode\BarcodeGenerator;

/**
 * @covers \DataLinx\PhpUtils\Fluent\FluentBarcode
 */
class FluentBarcodeTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testBasic(): void
    {
        if (! file_exists("./build")) {
            mkdir("./build");
        }

        $barcode = barcode("9313920040041")
            ->setFormat("svg")
            ->setColor("black")
            ->setHeight(45)
            ->setType(BarcodeGenerator::TYPE_EAN_13)
            ->setWidthFactor(3);

        $this->assertEquals("svg", $barcode->getFormat());
        $this->assertEquals("black", $barcode->getColor());
        $this->assertEquals(45, $barcode->getHeight());
        $this->assertEquals(BarcodeGenerator::TYPE_EAN_13, $barcode->getType());
        $this->assertEquals(3, $barcode->getWidthFactor());

        $barcode->setCode(strrev("9313920040041"));
        $this->assertEquals(strrev("9313920040041"), $barcode->getCode());

        $this->assertNotEmpty($barcode->getCode());

        $barcode = barcode("9313920040041", BarcodeGenerator::TYPE_EAN_13);
        $file = $barcode->save();
        $this->assertFileExists($file);

        $file = $barcode->setFormat("png")->save();
        $this->assertFileExists($file);

        // Save to a certain file
        $file = $barcode->setFormat("svg")->save("./build/9313920040041.svg");
        $this->assertFileExists($file);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testAllParameters(): void
    {
        $barcode = barcode("9313920040041")
            ->setFormat("svg")
            ->setColor("black")
            ->setHeight(60)
            ->setWidthFactor(4)
            ->setType(BarcodeGenerator::TYPE_EAN_13);

        $file = $barcode->save();
        $this->assertFileExists($file);

        $barcode
            ->setColor(null)
            ->setFormat("png")
            ->setColor([55,55,55]);
        $file = $barcode->save();
        $this->assertFileExists($file);

        $barcode
            ->setFormat("jpg")
            ->setColor([55,55,55]);
        $file = $barcode->save();
        $this->assertFileExists($file);

        $barcode
            ->setColor(null)
            ->setFormat("html")
            ->setColor("black");

        $file = $barcode->save();
        $this->assertFileExists($file);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testEmbed(): void
    {
        $barcode = barcode("9313920040041");

        $this->assertNotEmpty($barcode->embed());

        $barcode->setFormat("png");
        $this->assertNotEmpty($barcode->embed());

        $barcode->setFormat("jpg");
        $this->assertNotEmpty($barcode->embed());

        $barcode->setFormat("html");
        $this->assertNotEmpty((string)$barcode);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSave(): void
    {
        $barcode = barcode("9313920040041");

        $cases = [
            ["9313920040041", "svg"],
            ["9313920040041", "png"],
            ["9313920040041", "jpg"],
            ["9313920040041", "html"],
        ];

        foreach ($cases as $case) {
            $filename = $case[0] . "." . $case[1];

            if (file_exists("./build/" . $filename)) {
                unlink("./build/" . $filename);
            }

            $barcode->setFormat($case[1]);
            $file = $barcode->save("./build/" . $filename);
            $this->assertFileExists($file);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetColor(): void
    {
        $barcode = barcode("9313920040041");

        $this->expectExceptionMessage("When using the PNG or JPG format the color must be in a valid RGB format (example: [55, 85, 155])");
        $barcode->setFormat("png")
            ->setColor("black");
    }

    /**
     * @return void
     * @throws Exception

     */
    public function testValidateColor(): void
    {
        $barcode = barcode("9313920040041");
        $this->expectExceptionMessage("The selected format requires a hex code or color name.");
        $barcode->validateColor([55, 66, 77], "svg");
    }
}
