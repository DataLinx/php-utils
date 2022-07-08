<?php

namespace DataLinx\PhpUtils;

class StringHelper
{
    /**
     * Best-effort conversion of HTML to plain text
     *
     * @param string $html HTML text
     * @param string $newline Newline character
     * @return string
     */
    public static function html2plain(string $html, string $newline = PHP_EOL): string
    {
        // Remove all existing newlines, since they have no role in HTML
        $html = preg_replace('/\R/', '', $html);

        // Replace breaks and paragraphs with newlines
        $html = str_replace([
            '<br>',
            '<br/>',
            '<br />',
            '<p>',
        ], [
            $newline,
            $newline,
            $newline,
            $newline . $newline,
        ], $html);

        return trim(strip_tags($html));
    }

    /**
     * Link hashtags in the supplier text using the href template.
     *
     * Example input:
     * - text:
     * <pre>
     * Check out these #holiday gift ideas!
     * </pre>
     * - hrefTpl:
     * https://www.instagram.com/{tpl}
     *
     * Output:
     * <pre>
     * Check out these <a href="https://www.instagram.com/holiday" class="hashtag" data-tag="holiday">#holiday</a> gift ideas!
     * </pre>
     *
     * @param string $text Text with hashtags
     * @param string $hrefTpl Link template with {tag} placeholder, e.g. https://www.example.com/{tag}
     * @param string $class HTML class for the anchor element
     * @return string
     */
    public static function linkHashtags(string $text, string $hrefTpl, string $class = 'hashtag'): string
    {
        $m = [];


        if (preg_match_all('/#{1}([a-z0-9]+)/i', $text, $m)) {
            foreach ($m[1] as $tag) {
                // TODO \s|$ tukaj manjka še en ali [string terminator] najdi regex ki nadomešča . , ! ? ; :  itd... kar konča stavek (ločila)
                $text = preg_replace("/#$tag(\s|$)/", '<a class="'. $class .'" href="'. str_replace('{tag}', $tag, $hrefTpl) .'" data-tag="'. $tag .'">#'. $tag .'</a> ', $text);
            }
        }

        return $text;
    }

    /**
     * Convert a string from camelCase or PascalCase to snake_case
     *
     * @param string $str Input string
     * @return string snake_cased string
     */
    public static function camel2snake(string $str): string
    {
        return preg_replace_callback(
            '#(^|[a-z])([A-Z])#',
            function (array $matches) {
                if (0 === strlen($matches[1])) {
                    $result = $matches[2];
                } else {
                    $result = "$matches[1]_$matches[2]";
                }
                return strtolower($result);
            },
            $str
        );
    }

    /**
     * Convert a string from snake_case to camelCase or UpperCamelCase/PascalCase
     *
     * @param string $str Input string
     * @param boolean $upper Capitalize the first character
     * @return string
     */
    public static function snake2camel(string $str, bool $upper = true)
    {
        $cc = str_replace('_', '', ucwords($str, '_'));

        return $upper ? $cc : lcfirst($cc);
    }

    /**
     * Common string cleaning for user inputs:
     * - replace any occurrence of 2+ spaces with a single space
     * - trim the string
     *
     * @param string $str
     * @return string
     */
    public static function cleanString(string $str): string
    {
        if (! empty($str)) {
            return preg_replace('/\s{2,}/', ' ', trim($str));
        }

        return $str;
    }

    /**
     * Best-effort attempt to split a german-style address to two parts: street name and house number.
     *
     * E.g. <b>Pot v X 123b</b> will return:
     * <pre>
     * [
     * 		0 => 'Pot v X',
     *		1 => '123b',
     * ]
     * </pre>
     *
     * If the split is not successful, null is returned.
     *
     * @param string $address Address to split
     * @return array|null
     */
    public static function splitAddress(string $address): ?array
    {
        $m = [];

        // Sanitize the address - remove double spaces, trim trailing dots and commas
        $address = self::cleanString(trim($address, '.,'));

        $street_name = '.+[.,a-z]+'; // Any string that ends with a dot, comma or letter
        $separator = '[ \/]*'; // Zero or more spaces or slashes
        $house_number = '\d+[ \/]*[a-z]?'; // Starts with a digit, is optionally separated with a space or slash and has a single trailing letter

        if (preg_match("/^($street_name)$separator($house_number)$/i", $address, $m)) {
            return [
                trim($m[1], ','), // Trim any commas trailing the "street name"
                $m[2],
            ];
        }

        return null;
    }

    /**
     * Convert an integer number to roman notation
     * C/P from http://www.go4expert.com/forums/showthread.php?t=4948
     * @param int|string $num
     * @return string
     */
    public static function int2roman($num): string
    {
        // Make sure that we only use the integer portion of the value
        $n = intval($num);
        $result = '';

        // Declare a lookup array that we will use to traverse the number:
        $lookup = [
            'M'		=> 1000,
            'CM'	=> 900,
            'D'		=> 500,
            'CD'	=> 400,
            'C'		=> 100,
            'XC'	=> 90,
            'L'		=> 50,
            'XL'	=> 40,
            'X'		=> 10,
            'IX'	=> 9,
            'V'		=> 5,
            'IV'	=> 4,
            'I'		=> 1,
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
        return $result;
    }
}
