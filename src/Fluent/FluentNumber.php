<?php

namespace DataLinx\PhpUtils\Fluent;

use InvalidArgumentException;
use NumberFormatter;
use RuntimeException;

class FluentNumber
{
    public const TYPE_DECIMAL = 'd';
    public const TYPE_INT = 'i';

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
        $this->setType($value);

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * @param float|int|string $value
     * @return FluentNumber
     */
    public function setValue($value): FluentNumber
    {
        $this->setType($value);

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
    public function format(?int $decimals = null, ?int $trim_to_decimals = null, ?string $locale = null): string
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
    public function asPercent(?int $decimals = null, ?int $trim_to_decimals = null, ?string $locale = null): string
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
    public function asMoney(string $currency, ?int $decimals = null, ?int $trim_to_decimals = null, ?string $locale = null): string
    {
        $formatter = $this->getFormatter($locale, NumberFormatter::CURRENCY, $decimals, $trim_to_decimals);

        $str = $formatter->formatCurrency($this->value, $currency);

        if ($str === false) {
            throw new RuntimeException('NumberFormatter::formatCurrency() error: '. $formatter->getErrorMessage() ." ({$formatter->getErrorCode()})");
        }

        return $str;
    }

    /**
     * Format number as file size to the largest unit possible, given that the value was in bytes.
     *
     * @param int $decimals Decimals to round to
     * @return string
     */
    public function asFileSize(int $decimals = 2): string
    {
        static $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];

        if (! $this->isInteger()) {
            throw new InvalidArgumentException('Only integer values can be formatted as file size!');
        }

        if ($this->value < 1000) {
            return $this->format() ."\u{00A0}B";
        } else {
            $base = log($this->value, 1000);

            return (new static(pow(1000, $base - floor($base))))->format($decimals) ."\u{00A0}". $suffixes[floor($base)];
        }
    }

    /**
     * Format number as simple time interval, given that the internal value is in seconds.
     * This is a simple function, suitable only for small intervals from a second up to a few days. For better output, use the `diffForHumans()` method from the Carbon library.
     *
     * E.g.
     * ```
     * echo num(60)->asTimeInterval(); // 1m
     * echo num(61)->asTimeInterval(); // 1m 1s
     * echo num(3600)->asTimeInterval(); // 1h
     * echo num(48 * 3600 + 5 * 3600 + 30)->asTimeInterval(); // 2d 5h 30s
     * ```
     *
     * @param string $precision Max. precision (s = seconds, m = minutes, h = hours, d = days)
     * @param string $start Starting unit (default: d, also supported: h, m)
     * @return string
     */
    public function asTimeInterval(string $precision = 's', string $start = 'd'): string
    {
        if ((int)$this->value === 0) {
            return '0'. $precision;
        }

        $seconds = $this->value;
        $hour = 60 * 60;
        $day = 24 * $hour;

        $parts = [
            'd' => 0,
            'h' => 0,
        ];

        if ($start === 'd' && $seconds >= $day) {
            $parts['d'] = floor($seconds / $day);

            $seconds -= $parts['d'] * $day;
        }

        if (($start === 'h' || $start === 'd') && $seconds >= $hour) {
            $parts['h'] = floor($seconds / $hour);

            $seconds -= $parts['h'] * $hour;
        }

        $parts['m'] = floor($seconds / 60);

        $parts['s'] = $seconds - $parts['m'] * 60;

        $str = '';

        static $units = ['d', 'h', 'm', 's'];

        foreach ($units as $unit) {
            if ($parts[$unit] > 0) {
                $str .= $parts[$unit] . $unit .' ';
            }

            if ($precision === $unit) {
                break;
            }
        }

        return empty($str) ? '0'. $precision : trim($str);
    }

    /**
     * Parse a numeric value that is formatted for the current locale.
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
     * @return self
     */
    public static function parse(string $value, string $locale = null): self
    {
        static $parser;

        if (empty($locale)) {
            $locale = setlocale(LC_MESSAGES, '0');
        }

        if (! isset($parser[$locale])) {
            $parser[$locale] = new NumberFormatter($locale, NumberFormatter::DEFAULT_STYLE);
        }

        $result = $parser[$locale]->parse($value);

        if ($result === false) {
            throw new RuntimeException(sprintf('NumberFormatter failed to parse the value "%s"! Error message: %s', $value, $parser[$locale]->getErrorMessage()));
        }

        return new static($result);
    }

    /**
     * Set internal type by value
     *
     * @param float|int|string $value
     * @return $this
     */
    protected function setType($value): self
    {
        if (is_int($value)) {
            $this->type = self::TYPE_INT;
        } elseif (is_numeric($value)) {
            $this->type = self::TYPE_DECIMAL;
        } else {
            throw new InvalidArgumentException('Value is not numeric');
        }

        return $this;
    }
}
