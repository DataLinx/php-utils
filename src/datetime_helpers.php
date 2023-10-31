<?php

namespace DataLinx\PhpUtils;

use Carbon\Carbon;
use Exception;

/**
 * Parse a localized date string and return it in the ISO 8601 format (`YYYY-MM-DD`). This is a simplified wrapper for the `Carbon::createFromIsoFormat()` method.
 *
 * The `iso_format` parameter accepts the format string made from characters as specified in [Carbon docs](https://carbon.nesbot.com/docs/#api-localization).
 *
 * Usage:
 * ```
 * echo parse_date('01/24/2023'); // 2023-01-24
 * echo parse_date('1/24/2023', 'l'); // 2023-01-24
 * echo parse_date('24.1.2023', null, 'sl'); // 2023-01-24
 * ```
 *
 * @see https://carbon.nesbot.com/docs/#api-localization
 *
 * @param string $input Date string in localized notation (e.g. `01/24/2023`)
 * @param string|null $iso_format ISO format as documented in Carbon docs (default: `L`)
 * @param string|null $locale Override locale when using macro formats (default: using current locale setting for `LC_TIME` category)
 * @return string|null Parsed date in ISO format or `null` if the date failed to parse
 */
function parse_date(string $input, ?string $iso_format = null, string $locale = null): ?string
{
    try {
        return Carbon::createFromIsoFormat($iso_format ?: 'L', $input, null, $locale ?: setlocale(LC_TIME, '0'))
            ->toDateString();
    } catch (Exception $exception) {
        return null;
    }
}

/**
 * Parse a localized time string and return it in the ISO 8601 extended format (`T[hh]:[mm]:[ss]`). This is a simplified wrapper for the `Carbon::createFromIsoFormat()` method.
 *
 * The `iso_format` parameter accepts the format string made from characters as specified in [Carbon docs](https://carbon.nesbot.com/docs/#api-localization).
 *
 * Usage:
 * ```
 * echo parse_time('5:04 PM'); // 17:04:00
 * echo parse_time('5:04:12 PM', 'LTS'); // 17:04:12
 * echo parse_time('17:04', null, 'sl'); // 17:04:00
 * ```
 *
 * @see https://carbon.nesbot.com/docs/#api-localization
 *
 * @param string $input Time string in localized notation (e.g. `5:04 PM`)
 * @param string|null $iso_format ISO format as documented in Carbon docs (default: `LT`)
 * @param string|null $locale Override locale when using macro formats (default: using current locale setting for `LC_TIME` category)
 * @return string|null Parsed time in ISO format or `null` if the string failed to parse
 */
function parse_time(string $input, ?string $iso_format = null, string $locale = null): ?string
{
    try {
        return Carbon::createFromIsoFormat($iso_format ?: 'LT', $input, null, $locale ?: setlocale(LC_TIME, '0'))
            ->toTimeString();
    } catch (Exception $exception) {
        return null;
    }
}

/**
 * Parse a localized string with date and time and return it in the ISO 8601 extended format (`YYYY-MM-DD T[hh]:[mm]:[ss]`). This is a simplified wrapper for the `Carbon::createFromIsoFormat()` method.
 *
 * The `iso_format` parameter accepts the format string made from characters as specified in [Carbon docs](https://carbon.nesbot.com/docs/#api-localization).
 *
 * Usage:
 * ```
 * echo parse_date_time('1/24/2023 5:04 PM'); // 2023-01-24 17:04:00
 * echo parse_date_time('1/24/2023 5:04:12 PM', 'l LTS'); // 2023-01-24 17:04:12
 * echo parse_date_time('24.1.2023 17:04', null, 'sl'); // 2023-01-24 17:04:00
 * ```
 *
 * @see https://carbon.nesbot.com/docs/#api-localization
 *
 * @param string $input Date and time string in localized notation (e.g. `01/24/2023 5:04 PM`)
 * @param string|null $iso_format ISO format as documented in Carbon docs (default: `l LT`)
 * @param string|null $locale Override locale when using macro formats (default: using current locale setting for `LC_TIME` category)
 * @return string|null Parsed date and time in ISO format or `null` if the string failed to parse
 */
function parse_date_time(string $input, ?string $iso_format = null, string $locale = null): ?string
{
    try {
        return Carbon::createFromIsoFormat($iso_format ?: 'l LT', $input, null, $locale ?: setlocale(LC_TIME, '0'))
            ->toDateTimeString();
    } catch (Exception $exception) {
        return null;
    }
}

/**
 * Format a date in the current locale format from a standard ISO date (`YYYY-MM-DD`) or timestamp.
 *
 * The `iso_format` parameter accepts the format string made from characters as specified in [Carbon docs](https://carbon.nesbot.com/docs/#api-localization).
 *
 * Usage:
 * ```
 * echo format_date(1674518400); // 1/24/2023
 * echo format_date('2023-01-24'); // 1/24/2023
 * echo format_date(); // Current date in YYYY-MM-DD format
 * echo format_date('2023-01-24', 'L'); // 01/24/2023
 * echo format_date('2023-01-24', null, 'sl'); // 24.1.2023
 * echo format_date('1111-31-31'); // null
 * ```
 *
 * @see https://carbon.nesbot.com/docs/#api-localization
 *
 * @param int|string|null $input Timestamp, ISO date or `null˙ to use the current date
 * @param string|null $iso_format ISO format as documented in Carbon docs (default: `l`)
 * @param string|null $locale Override locale when using macro formats (default: using current locale setting for `LC_TIME` category)
 * @return string|null Formatted date or `null` if the input string failed to parse as a date string
 */
function format_date($input = null, ?string $iso_format = null, ?string $locale = null): ?string
{
    if (is_int($input)) {
        $date = Carbon::createFromTimestamp($input);
    } elseif (is_string($input) and ! empty($input)) {
        $timestamp = strtotime($input);

        if ($timestamp === false) {
            return null;
        }

        $date = Carbon::createFromTimestamp($timestamp);
    } else {
        $date = Carbon::now();
    }

    $date->locale($locale ?? setlocale(LC_TIME, '0'));

    return $date->isoFormat($iso_format ?: 'l');
}

/**
 * Format time in the current locale format from an ISO 8601 extended format (`T[hh]:[mm]:[ss]`) or timestamp.
 *
 * The `iso_format` parameter accepts the format string made from characters as specified in [Carbon docs](https://carbon.nesbot.com/docs/#api-localization).
 *
 * Usage:
 * ```
 * echo format_time(1674518400); // 5:04 PM
 * echo format_time('17:04:12'); // 5:04 PM
 * echo format_time(); // Current time in hh:mm:ss format
 * echo format_time('17:04:12', 'LTS'); // 5:04:12 PM
 * echo format_time('07:04:12', 'LTS', 'sl'); // 7:04:12
 * echo format_time('30:30'); // null
 * ```
 *
 * @see https://carbon.nesbot.com/docs/#api-localization
 *
 * @param int|string|null $input Timestamp, ISO time string or `null˙ to use the current time
 * @param string|null $iso_format ISO format as documented in Carbon docs (default: `LT`)
 * @param string|null $locale Override locale when using macro formats (default: using current locale setting for `LC_TIME` category)
 * @return string|null Formatted time string or `null` if the input string failed to parse as a date string
 */
function format_time($input = null, ?string $iso_format = null, ?string $locale = null): ?string
{
    return format_date($input, $iso_format ?: 'LT', $locale);
}

/**
 * Format date and time in the current locale format from an ISO 8601 format (`YYYY-MM-DD T[hh]:[mm]:[ss]``) or timestamp.
 *
 * The `iso_format` parameter accepts the format string made from characters as specified in [Carbon docs](https://carbon.nesbot.com/docs/#api-localization).
 *
 * Usage:
 * ```
 * echo format_date_time(1674579852); // 1/24/2023 5:04 PM
 * echo format_date_time('2023-01-24 17:04:12'); // 1/24/2023 5:04 PM
 * echo format_date_time('2023-01-24 07:04:12', 'l LTS', 'sl'); // 24.1.2023 7:04:12
 * ```
 *
 * @see https://carbon.nesbot.com/docs/#api-localization
 *
 * @param int|string|null $input Timestamp, ISO time string or `null˙ to use the current date and time
 * @param string|null $iso_format ISO format as documented in Carbon docs (default: `l LT`)
 * @param string|null $locale Override locale when using macro formats (default: using current locale setting for `LC_TIME` category)
 * @return string|null Formatted time string or `null` if the input string failed to parse as a date string
 */
function format_date_time($input = null, ?string $iso_format = null, ?string $locale = null): ?string
{
    return format_date($input, $iso_format ?: 'l LT', $locale);
}
