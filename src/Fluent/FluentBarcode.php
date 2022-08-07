<?php

namespace DataLinx\PhpUtils\Fluent;

use Exception;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * Fluent barcode utility
 *
 * When used together with the helper function, you could include barcodes into your HTML as simply as:
 * <code>
 * <img src="<?= barcode('9313920040041') ?>" />
 * </code>
 */
class FluentBarcode
{
    /* File formats */
    public const FORMAT_SVG = "svg";
    public const FORMAT_PNG = "png";
    public const FORMAT_JPG = "jpg";
    public const FORMAT_HTML = "html";

    /**
     * @var string Code to display
     */
    private string $code;

    /**
     * @var string Type of the code we want to generate (defaults to EAN-13)
     */
    protected string $type = BarcodeGenerator::TYPE_EAN_13;

    /**
     * @var int Width factor (defaults to 2)
     */
    protected int $widthFactor = 2;

    /**
     * @var int Total code height in px (defaults to 30)
     */
    protected int $height = 30;

    /**
     * @var array|string Foreground color — for PNG and JPG, this must be an RGB array, e.g. [255, 0, 0] (defaults to black)
     */
    protected $color;

    /**
     * @var string Content format (SVG/PNG/JPG/HTML, defaults to SVG)
     */
    protected string $format = self::FORMAT_SVG;

    /**
     * Internal BarcodeGenerator instances
     *
     * @var BarcodeGenerator[]
     */
    private static array $generators;

    /**
     * @param string $code Code to display
     * @param string|null $type Type of barcode — see BarcodeGenerator TYPE constants for options
     * @throws Exception
     */
    public function __construct(string $code, string $type = null)
    {
        if (! class_exists("Picqer\Barcode\Barcode")) {
            throw new Exception("You need to install the picqer/php-barcode-generator package to use the FluentBarcode utility!");
        }

        $this->code = $code;

        if ($type !== null) {
            $this->type = $type;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        return $this->embed();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return FluentBarcode
     */
    public function setCode(string $code): FluentBarcode
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type Type of barcode — see BarcodeGenerator TYPE constants for options
     * @return FluentBarcode
     */
    public function setType(string $type): FluentBarcode
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidthFactor(): int
    {
        return $this->widthFactor;
    }

    /**
     * @param int $widthFactor
     * @return FluentBarcode
     */
    public function setWidthFactor(int $widthFactor): FluentBarcode
    {
        $this->widthFactor = $widthFactor;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return FluentBarcode
     */
    public function setHeight(int $height): FluentBarcode
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param array|string $color
     * @return FluentBarcode
     * @throws Exception
     */
    public function setColor($color): FluentBarcode
    {
        if ($color) {
            $this->validateColor($color);
        }

        $this->color = $color;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return FluentBarcode
     * @throws Exception
     */
    public function setFormat(string $format): FluentBarcode
    {
        if ($this->color) {
            $this->validateColor($this->color, $format);
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Get embeddable code.
     * For SVG, PNG and JPG, this returns the contents that you can use in the "src" attribute of the img element.
     * For HTML, this simply returns standalone HTML code.
     *
     * @param string|null $format Optional embed format (SVG, PNG, JPG or HTML)
     * @return string
     * @throws Exception
     */
    public function embed(?string $format = null): string
    {
        switch ($format ?: $this->format) {
            case "svg":
                $embed = "data:image/svg+xml;base64,";
                break;
            case "png":
                $embed = "data:image/png;base64,";
                break;
            case "jpg":
                $embed = "data:image/jpg;base64,";
                break;
            default:
                // For HTML, simply return the contents
                return $this->getContents();
        }

        return $embed . base64_encode($this->getContents());
    }

    /**
     * Save barcode to a file on the local filesystem.
     * If a filename is not passed, the file is generated with a random unique name in the system tmp dir.
     *
     * @param string|null $filename Optional full file name (with directory)
     * @return string The saved file location
     * @throws Exception
     */
    public function save(string $filename = null)
    {
        if ($filename) {
            // Attempt to set format by the specified file's extension
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            switch ($extension) {
                case self::FORMAT_SVG:
                case self::FORMAT_PNG:
                case self::FORMAT_JPG:
                case self::FORMAT_HTML:
                    $this->setFormat($extension);
            }
        } else {
            $filename = tempnam(sys_get_temp_dir(), $this->code) . "." . $this->format;
        }

        file_put_contents($filename, $this->getContents());

        return $filename;
    }

    /**
     * Get generated barcode contents, as provided by the underlying library
     *
     * @return string
     * @throws Exception
     */
    private function getContents(): string
    {
        $color = $this->color;

        if (empty($color)) {
            switch ($this->format) {
                case self::FORMAT_HTML:
                case self::FORMAT_SVG:
                    $color = "black";
                    break;

                default:
                    // For PNG and JPG, we need an RGB format
                    $color = [0, 0, 0];
                    break;
            }
        }

        return $this->getGenerator()->getBarcode($this->code, $this->type, $this->widthFactor, $this->height, $color);
    }

    /**
     * Get Barcode generator instance for the specified format.
     *
     * @return BarcodeGeneratorSVG|BarcodeGeneratorPNG|BarcodeGeneratorJPG|BarcodeGeneratorHTML
     * @throws Exception
     */
    private function getGenerator()
    {
        if (empty($this->format)) {
            throw new Exception("Barcode format is required");
        }

        if (!isset(self::$generators[$this->format])) {
            switch ($this->format) {
                case self::FORMAT_SVG:
                case self::FORMAT_PNG:
                case self::FORMAT_JPG:
                case self::FORMAT_HTML:
                    $class = "\Picqer\Barcode\BarcodeGenerator" . strtoupper($this->format);
                    self::$generators[$this->format] = new $class();
                    if ($this->format === self::FORMAT_JPG && function_exists("imagecreate")) {
                        // For JPG, always use GD if available, since with imagick it only shows a completely black block
                        self::$generators[$this->format]->useGd();
                    }
                    break;
                default:
                    throw new Exception("Barcode format $this->format is unknown!");
            }
        }

        return self::$generators[$this->format];
    }

    /**
     * Validate if the given color is in a correct format (hex or RGB array) regarding the chosen file format (svg, html, png and jpg).
     *
     * @param string|array $color
     * @param string|null $format
     * @return void
     * @throws Exception
     */
    public function validateColor($color, ?string $format = null): void
    {
        switch ($format ?? $this->format) {
            case self::FORMAT_HTML:
            case self::FORMAT_SVG:
                if (!is_string($color)) {
                    throw new Exception("The selected format requires a hex code or color name.");
                }
                break;

            default:
                // For PNG and JPG, we need an RGB format
                $colorFormatErrMsg = "When using the PNG or JPG format the color must be in a valid RGB format (example: [55, 85, 155])";
                if (is_array($color)) {
                    if (! count($color) == 3 and min($color) >= 0 and max($color) <= 255) {
                        throw new Exception($colorFormatErrMsg);
                    }
                } else {
                    throw new Exception($colorFormatErrMsg);
                }


                break;
        }
    }
}
