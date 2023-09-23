<?php

namespace DataLinx\PhpUtils\Fluent;

use InvalidArgumentException;
use NumberFormatter;
use RuntimeException;

class FluentNumber
{
    const TYPE_DECIMAL = 'd';
    const TYPE_INT = 'i';

    /**
     * @var float|int|string Number value
     */
    protected $value;

    /**
     * @var string Number type (see class constants)
     */
    protected string $type;

    /**
     * Create a FluentNumber object
     *
     * @param float|int|string $value
     */
    public function __construct($value)
    {
        $this->value = $value;

        if (is_int($value)) {
            $this->type = self::TYPE_INT;
        } elseif (is_numeric($value)) {
            $this->type = self::TYPE_DECIMAL;
        } else {
            throw new InvalidArgumentException('Value is not numeric');
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * @param float $value
     * @return FluentNumber
     */
    public function setValue(float $value): FluentNumber
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Convert an integer number to roman notation
     * C/P from http://www.go4expert.com/forums/showthread.php?t=4948
     *
     * @return FluentString
     */
    public function toRoman(): FluentString
    {
        // Make sure that we only use the integer portion of the value
        $n = (int)$this->value;
        $result = "";

        // Declare a lookup array that we will use to traverse the number:
        static $lookup = [
            "M" => 1000,
            "CM" => 900,
            "D" => 500,
            "CD" => 400,
            "C" => 100,
            "XC" => 90,
            "L" => 50,
            "XL" => 40,
            "X" => 10,
            "IX" => 9,
            "V" => 5,
            "IV" => 4,
            "I" => 1,
        ];

        foreach ($lookup as $roman => $value) {
            // Determine the number of matches
            $matches = (int)($n / $value);

            // Store that many characters
            $result .= str_repeat($roman, $matches);

            // Subtract that from the number
            $n %= $value;
        }

        // The Roman numeral should be built, return it
        return new FluentString($result);
    }

    /**
     * Was the object instance created with an integer value?
     *
     * @return bool
     */
    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INT;
    }

    /**
     * Was the object instance created with a decimal value?
     *
     * @return bool
     */
    public function isDecimal(): bool
    {
        return $this->type === self::TYPE_DECIMAL;
    }

    /**
     * Get NumberFormatter instance
     *
     * @param string|null $locale Specific locale or null to use the current locale
     * @param int|null $style NumberFormatter style parameter or null to use the default style
     * @param int|null $decimals Number of decimals to show
     * @param int|null $trim_to_decimals Trim trailing decimal zeros but always keep the specified number of decimals
     * @return NumberFormatter
     */
    protected function getFormatter(?string $locale, ?int $style, ?int $decimals, ?int $trim_to_decimals): NumberFormatter
    {
        $formatter = new NumberFormatter($locale ?: setlocale(LC_MESSAGES, '0'), $style ?? NumberFormatter::DEFAULT_STYLE);

        if ($decimals === null) {
            // Use default number of decimal digits
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $trim_to_decimals ?? ($this->isInteger() ? 0 : 2));
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 10);
        } else {
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $trim_to_decimals ?? $decimals);
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
        }

        return $formatter;
    }

    /**
     * Format the number as a string.
     *
     * By default, formatting depends on the type of value that you created the FluentNumber object with (integer or float).
     *
     * **Please note!** By default for decimals numbers, up to 10 decimal digits are shown, unless you provide a value for the `decimals` parameter.
     *
     * @param int|null $decimals (Optional) Number of decimal digits to show. Defaults to 2 for decimals numbers.
     * @param int|null $trim_to_decimals (Optional) Trim trailing decimal zeros but always keep the specified number of decimals
     * @param string|null $locale (Optional) Override the locale
     * @return string
     */
    public function format(?int $decimals = NULL, ?int $trim_to_decimals = NULL, ?string $locale = NULL): string
    {
        $formatter = $this->getFormatter($locale, null, $decimals, $trim_to_decimals);

        $str = $formatter->format($this->value);

        if ($str === false) {
            throw new RuntimeException('NumberFormatter::format() error: '. $formatter->getErrorMessage() ." ({$formatter->getErrorCode()})");
        }

        return $str;
    }

    /**
     * Format the number as a percentage.
     *
     * By default, formatting depends on the type of value that you created the FluentNumber object with (integer or float).
     *
     * **Please note!** By default for decimals numbers, up to 10 decimal digits are shown, unless you provide a value for the `decimals` parameter.
     *
     * @param int|null $decimals (Optional) Number of decimal digits to show. Defaults to 2 for decimals numbers.
     * @param int|null $trim_to_decimals (Optional) Trim trailing decimal zeros but always keep the specified number of decimals
     * @param string|null $locale (Optional) Override the locale
     * @return string
     */
    public function asPercent(?int $decimals = NULL, ?int $trim_to_decimals = NULL, ?string $locale = NULL): string
    {
        $formatter = $this->getFormatter($locale, NumberFormatter::PERCENT, $decimals, $trim_to_decimals);

        $str = $formatter->format($this->value / 100);

        if ($str === false) {
            throw new RuntimeException('NumberFormatter::format() error: '. $formatter->getErrorMessage() ." ({$formatter->getErrorCode()})");
        }

        return $str;
    }

    /**
     * Format the number as a money amount.
     *
     * **Please note!** By default for decimals numbers, up to 10 decimal digits are shown, unless you provide a value for the `decimals` parameter.
     *
     * @param string $currency Currency ISO code (EUR, USD, CAD...)
     * @param int|null $decimals (Optional) Number of decimal digits to show. Defaults to 2 for decimals numbers.
     * @param int|null $trim_to_decimals (Optional) Trim trailing decimal zeros but always keep the specified number of decimals
     * @param string|null $locale (Optional) Override the locale
     * @return string
     */
    public function asMoney(string $currency, ?int $decimals = NULL, ?int $trim_to_decimals = NULL, ?string $locale = NULL): string
    {
        $formatter = $this->getFormatter($locale, NumberFormatter::CURRENCY, $decimals, $trim_to_decimals);

        $str = $formatter->formatCurrency($this->value, $currency);

        if ($str === false) {
            throw new RuntimeException('NumberFormatter::formatCurrency() error: '. $formatter->getErrorMessage() ." ({$formatter->getErrorCode()})");
        }

        return $str;
    }
}
