<?php

use DataLinx\PhpUtils\Fluent\FluentArray;
use DataLinx\PhpUtils\Fluent\FluentBarcode;
use DataLinx\PhpUtils\Fluent\FluentDirectory;
use DataLinx\PhpUtils\Fluent\FluentNumber;
use DataLinx\PhpUtils\Fluent\FluentString;

if (! function_exists('num')) {
    /**
     * Create a new FluentNumber object
     *
     * @param float|int|string $value
     * @return FluentNumber
     */
    function num($value): FluentNumber
    {
        return new FluentNumber($value);
    }
}

if (! function_exists('str')) {
    /**
     * Create a new FluentString object
     *
     * @param string $value
     * @return FluentString
     */
    function str(string $value): FluentString
    {
        return new FluentString($value);
    }
}

if (! function_exists('arr')) {
    function arr(array $arr): FluentArray
    {
        return new FluentArray($arr);
    }
}

if (! function_exists('barcode')) {
    /**
     * Create a new FluentBarcode object
     *
     * @param string $code Code to display
     * @param string|null $type Barcode type (defaults to EAN13)
     * @return FluentBarcode
     * @throws Exception
     */
    function barcode(string $code, string $type = null): FluentBarcode
    {
        return new FluentBarcode($code, $type);
    }
}

if (! function_exists('directory')) {
    /**
     * Create a new FluentDirectory object
     *
     * @param string $path Directory path
     * @return FluentDirectory
     */
    function directory(string $path): FluentDirectory
    {
        return new FluentDirectory($path);
    }
}

if (! function_exists('parse_number')) {
    /**
     * Parse a numeric value that is formatted for the current locale.
     * This is a simplified helper function of the FluentNumber::parse() method. When more control is desired, use that instead.
     *
     * Example:
     * ```
     * setlocale(LC_MESSAGES, 'en_US');
     * echo parse_number('123.45'); // 123.45
     * echo parse_number('123,45'); // NULL
     * echo parse_number('123,45', 'sl_SI'); // 123.45
     * ```
     *
     * @param string $value Value to parse
     * @param string|null $locale Override locale (PHP setting for `LC_MESSAGES` is used by default)
     * @return float|null Float value or `null` on failure
     */
    function parse_number(string $value, string $locale = null): ?float
    {
        try {
            return FluentNumber::parse($value, $locale)->getValue();
        } catch (Exception $exception) {
            return null;
        }
    }
}
