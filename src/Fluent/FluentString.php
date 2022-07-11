<?php

namespace DataLinx\PhpUtils\Fluent;

class FluentString
{
    protected string $value;

    /**
     * Create a FluentString object
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return FluentString
     */
    public function setValue(string $value): FluentString
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Best-effort conversion of HTML to plain text
     *
     * @param string $newline Newline character
     * @return $this
     */
    public function htmlToPlain(string $newline = PHP_EOL): self
    {
        // Remove all existing newlines, since they have no role in HTML
        $html = preg_replace('/\R/', '', $this->value);

        // Replace breaks and paragraphs with newlines
        $html = str_replace([
            '<br>',
            '<br/>',
            '<br />',
            '</p><p>',
            '<p>',
            '</p>',
        ], [
            $newline,
            $newline,
            $newline,
            $newline . $newline,
            $newline . $newline,
            $newline . $newline,
        ], $html);

        $this->value = trim(strip_tags($html));

        return $this;
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
     * @param string $hrefTpl Link template with {tag} placeholder, e.g. https://www.example.com/{tag}
     * @param string $class HTML class for the anchor element
     * @return $this
     */
    public function linkHashtags(string $hrefTpl, string $class = 'hashtag'): self
    {
        $m = [];

        if (preg_match_all('/#([a-z0-9]+)/i', $this->value, $m)) {
            foreach ($m[1] as $tag) {
                $this->value = preg_replace("/#$tag/", '<a class="'. $class .'" href="'. str_replace('{tag}', $tag, $hrefTpl) .'" data-tag="'. $tag .'">#'. $tag .'</a>', $this->value);
            }
        }

        return $this;
    }

    /**
     * Convert a string from camelCase or PascalCase to snake_case
     *
     * @return $this
     */
    public function camelToSnake(): self
    {
        $this->value = preg_replace_callback(
            '#(^|[a-z])([A-Z])#',
            function (array $matches) {
                if (0 === strlen($matches[1])) {
                    $result = $matches[2];
                } else {
                    $result = "$matches[1]_$matches[2]";
                }
                return strtolower($result);
            },
            $this->value
        );

        return $this;
    }

    /**
     * Convert a string from snake_case to camelCase or UpperCamelCase/PascalCase
     *
     * @param boolean $upper Capitalize the first character
     * @return $this
     */
    public function snakeToCamel(bool $upper = true): self
    {
        $this->value = str_replace('_', '', ucwords($this->value, '_'));

        if (! $upper) {
            $this->value = lcfirst($this->value);
        }

        return $this;
    }

    /**
     * Common string cleaning for user inputs:
     * - replace any occurrence of 2+ spaces with a single space
     * - trim the string
     *
     * @return $this
     */
    public function clean(): self
    {
        if (! empty($this->value)) {
            $this->value = preg_replace('/\s{2,}/', ' ', trim($this->value));
        }

        return $this;
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
     * @return array|null
     */
    public function toAddressArray(): ?array
    {
        $m = [];

        // Sanitize the address - remove double spaces, trim trailing dots and commas
        $address = trim($this->clean(), '.,');

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
     * Parse text placeholders in curly braces. This ensures correct encoding on the operated strings.
     *
     * Provide the string as:
     * <code>
     *  Some sample text with {some_placeholder}.
     * </code
     *
     * Provide the array as:
     * <code>
     *  array(
     *      'some_placeholder' => 'some final value'
     *  )
     * </code>
     *
     * The processed string will then be:
     * <code>
     *  Some sample text with some final value.
     * </code>
     *
     * @param array $placeholders
     * @return $this
     */
    public function parsePlaceholders(array $placeholders)
    {
        $from = [];
        $to = [];

        if (mb_detect_encoding($this->value) !== 'UTF-8') {
            $this->value = mb_convert_encoding($this->value, 'UTF-8');
        }

        foreach ($placeholders as $key => $val) {
            $from[] = '{'. (mb_detect_encoding($key) != 'UTF-8' ? mb_convert_encoding($key, 'UTF-8') : $key) .'}';
            $to[] = mb_detect_encoding($val) != 'UTF-8' ? mb_convert_encoding($val, 'UTF-8') : $val;
        }

        $this->value = str_replace($from, $to, $this->value);

        return $this;
    }
}
