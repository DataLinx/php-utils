<?php

namespace DataLinx\PhpUtils\Fluent;

use InvalidArgumentException;

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

    /**
     * @return string
     */
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
        $html = preg_replace("/\R/", "", $this->value);

        // Replace breaks and paragraphs with newlines
        $html = str_replace([
            "<br>",
            "<br/>",
            "<br />",
            "</p><p>",
            "<p>",
            "</p>",
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
    public function linkHashtags(string $hrefTpl, string $class = "hashtag"): self
    {
        $m = [];

        if (preg_match_all("/#([a-z0-9]+)/i", $this->value, $m)) {
            foreach ($m[1] as $tag) {
                $this->value = preg_replace("/#$tag/", '<a class="'. $class .'" href="'. str_replace('{tag}', $tag, $hrefTpl) . $tag . '" data-tag="'. $tag .'">#'. $tag .'</a>', $this->value);
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
            "#(^|[a-z])([A-Z])#",
            static function (array $matches) {
                if ($matches[1] === '') {
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
     * @param bool $upper Capitalize the first character
     * @return $this
     */
    public function snakeToCamel(bool $upper = true): self
    {
        $this->value = str_replace("_", "", ucwords($this->value, "_"));

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
            $this->value = preg_replace("/\s{2,}/", " ", trim($this->value));
        }

        return $this;
    }

    /**
     * Best-effort attempt to split a german-style address to two parts: street name and house number.
     *
     * E.g. <b>Pot v X 123b</b> will return:
     * <pre>
     * [
     * 		0 => "Pot v X",
     *		1 => "123b",
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
        $address = trim($this->clean(), ".,");

        $street_name = ".+[.,\p{L}]+"; // Any string that ends with a dot, comma or letter
        $separator = "[ \/]*"; // Zero or more spaces or slashes
        $house_number = "\d+[ \/]*\p{L}?"; // Starts with a digit, is optionally separated with a space or slash and has a single trailing letter

        if (preg_match("/^($street_name)$separator($house_number)$/iu", $address, $m)) {
            $m[1] = trim($m[1], ","); // Trim any commas trailing the "street name"

            // Remove house number duplicates if present
            if (preg_match("/(" . preg_quote($m[2], "/") . ")$/i", $m[1])) {
                $m[1] = trim(substr($m[1], 0, -mb_strlen($m[2])));
            }

            return [
                $m[1],
                str_replace(" ", "", $m[2]), // Remove all spaces in the house number
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
     *      "some_placeholder" => "some final value"
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
    public function parsePlaceholders(array $placeholders): self
    {
        $from = [];
        $to = [];

        $subject_encoding = mb_detect_encoding($this->value);

        foreach ($placeholders as $key => $val) {
            $key_encoding = mb_detect_encoding($key);
            $val_encoding = mb_detect_encoding($val);
            $from[] = "{". ($key_encoding !== $subject_encoding ? mb_convert_encoding($key, $subject_encoding, $key_encoding) : $key) ."}";
            $to[] = $val_encoding !== $subject_encoding ? mb_convert_encoding($val, $subject_encoding, $val_encoding) : $val;
        }

        $this->value = str_replace($from, $to, $this->value);

        return $this;
    }

    /**
     * Truncate string to a certain length.
     * Copied from Smarty modifiers:
     * https://github.com/smarty-php/smarty/blob/support/3.1/libs/plugins/modifier.truncate.php
     *
     * @param int $length Length of truncated text
     * @param string|null $etc String to put on the end (defaults to ...)
     * @param bool $break_words Truncate at word boundary
     * @param bool $middle Truncate in the middle of text
     * @return $this
     */
    public function truncate(int $length = 80, ?string $etc = null, bool $break_words = false, bool $middle = false): self
    {
        if ($length === 0) {
            return $this;
        }

        if ($etc === null) {
            $etc = "...";
        }

        if (mb_strlen($this->value) > $length) {
            $length -= min($length, mb_strlen($etc));

            if (! $break_words && ! $middle) {
                $this->value = preg_replace("/\s+?(\S+)?$/", "", mb_substr($this->value, 0, $length + 1));
            }

            $endings = ".,!?-;:(\" ";
            if (! $middle) {
                $this->value = trim(mb_substr($this->value, 0, $length), $endings) . $etc;
            } else {
                $this->value = trim(mb_substr($this->value, 0, floor($length / 2)), $endings) . $etc . ltrim(mb_substr($this->value, -floor($length / 2), $length), $endings);
            }
        }

        return $this;
    }

    /**
     * Make a string appropriate for an HTML meta description.
     * Strip tags, decodes any HTML entities, trims, replaces multiple spaces and shortens it to 155 chars.
     *
     * @param int $length
     * @return $this
     */
    public function prepMetaDescription(int $length = 155): self
    {
        $this->value = html_entity_decode(strip_tags($this->value), ENT_COMPAT, "UTF-8");

        $this->clean()
             ->truncate($length);

        $this->value = htmlspecialchars($this->value);

        return $this;
    }

    /**
     * Extract the YouTube video hash ID from a link. Supports multiple link formats.
     *
     * @return string|null
     */
    public function extractYouTubeHash(): ?string
    {
        // Format: https://www.youtube.com/watch?v=FQPbLJ__wdQ
        $query = parse_url($this->value, PHP_URL_QUERY);

        if ($query) {
            parse_str($query, $params);

            if (isset($params["v"])) {
                return $params["v"];
            }
        }

        // Format: http://youtu.be/FQPbLJ__wdQ
        $matches = [];

        if (preg_match("/youtu\.be\/(.*)/", $this->value, $matches)) {
            return $matches[1];
        }

        // Format: https://www.youtube.com/shorts/W6eQhzKb0lc
        if (preg_match("/\/shorts\/(.*)/i", $this->value, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Make a string's first character uppercase - supports multibyte/accented characters
     *
     * @return $this
     */
    public function uppercaseFirst(): self
    {
        $this->value = mb_strtoupper(mb_substr($this->value, 0, 1)) . mb_substr($this->value, 1);

        return $this;
    }

    /**
     * Trim the string - supports unicode spaces
     *
     * @return $this
     */
    public function trim(): self
    {
        $this->value = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $this->value);

        return $this;
    }

    /**
     * Check if the email domain is a valid domain with MX records
     *
     * @return bool
     */
    public function isEmailDomainValid(): bool
    {
        $arr = explode('@', $this->value);

        if (is_array($arr) && count($arr) === 2) {
            return checkdnsrr(end($arr));
        }

        return false;
    }

    /**
     * <p>Break the string into chunks.</p>
     * <p>If word breaking is allowed (which is by default), the function simply splits the string into pieces of desired length. Spaces will not be trimmed.</p>
     * <p>If word breaking is not allowed, then the function will split the string into optimal chunks without breaking words and spaces will be trimmed.</p>
     * <p>If a single word is longer than the chunk length, it will be abbreviated with a dot at the end.
     * E.g.:
     * <p><code>str('Something short')->chunks(6, true)</code></p>
     * ... will be chunked into:
     * <pre>
     *  [
     *      'Somet.',
     *      'short',
     *  ]
     * </pre>
     *
     * @param int $length Chunk length
     * @param bool $prevent_word_break Prevent word break
     * @return string[]
     */
    public function chunks(int $length, bool $prevent_word_break = false): array
    {
        if ($length < 2) {
            throw new InvalidArgumentException('Chunk length must be at least 2.');
        }

        if (mb_strlen($this->value) > $length) {
            $chunks = [];
            $str = $this->value;
            if ($prevent_word_break) {
                $words = explode(' ', $str);
                $buffer = '';
                foreach ($words as $word) {
                    $add = null;
                    $word_length = mb_strlen($word);
                    if (mb_strlen(trim($buffer .' '. $word)) <= $length) {
                        // The string can be appended to the previous chunk
                        $buffer .= (empty($buffer) ? '' : ' ') . $word;
                    } elseif ($word_length > $length) {
                        // The word is longer than the chunk length, so abbreviate it and put a dot at the end
                        $add = mb_substr($word, 0, $length - 1) . '.';
                    } elseif ($word_length === $length) {
                        // The word is long exactly as the chunk length
                        $add = $word;
                    } else {
                        // The word cannot be appended to the buffer
                        if (! empty($buffer)) {
                            $chunks[] = $buffer;
                        }
                        $buffer = $word;
                    }

                    if (isset($add)) {
                        // Is there something in the buffer?
                        if (! empty($buffer)) {
                            // Add it to the array and clear the buffer
                            $chunks[] = $buffer;
                            $buffer = '';
                        }
                        // Add the new chunk
                        $chunks[] = $add;
                    }
                }
                // If something is left in the buffer, add it
                if (! empty($buffer)) {
                    $chunks[] = $buffer;
                }
            } else {
                while ($str !== '') {
                    $chunk = mb_substr($str, 0, $length);
                    $chunks[] = $chunk;
                    $str = mb_substr($str, $length);
                }
            }

            return $chunks;
        }

        return [$this->value];
    }
}
