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
    function directory(string $path)
    {
        return new FluentDirectory($path);
    }
}
