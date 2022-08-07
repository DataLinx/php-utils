<?php

namespace DataLinx\PhpUtils\Fluent;

class FluentNumber
{
    /**
     * @var float Number value
     */
    protected float $value;

    /**
     * Create a FluentNumber object
     *
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
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
            "M"		=> 1000,
            "CM"	=> 900,
            "D"		=> 500,
            "CD"	=> 400,
            "C"		=> 100,
            "XC"	=> 90,
            "L"		=> 50,
            "XL"	=> 40,
            "X"		=> 10,
            "IX"	=> 9,
            "V"		=> 5,
            "IV"	=> 4,
            "I"		=> 1,
        ];

        foreach ($lookup as $roman => $value) {
            // Determine the number of matches
            $matches = intval($n / $value);

            // Store that many characters
            $result .= str_repeat($roman, $matches);

            // Subtract that from the number
            $n = $n % $value;
        }

        // The Roman numeral should be built, return it
        return new FluentString($result);
    }
}
