<?php

namespace DataLinx\PhpUtils\Fluent;

use Exception;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Wrapper for `PhoneNumber` with a few added methods to make common usage easier.
 */
class FluentPhoneNumber extends PhoneNumber
{
    /**
     * Parse and validate the specified number, considering the country ID.
     *
     * If the number fails to parse or if it's not valid for the specified country, `null` is returned.
     *
     * Example:
     * ```
     * use DataLinx\PhpUtils\Fluent\FluentPhoneNumber;
     * $number = FluentPhoneNumber::from('(01) 584 61 00', 'SI');
     * $number = FluentPhoneNumber::from('044 668 1800', 'ch');
     * ```
     *
     * @param string $number Phone number in any known format that is valid for the specified country
     * @param string $country_id Country ID - ISO Alpha-2 code
     * @return self|null Object instance or `null` on failure
     */
    public static function from(string $number, string $country_id): ?self
    {
        $phone_util = PhoneNumberUtil::getInstance();

        try {
            $number = $phone_util->parse($number, $country_id);

            if ($phone_util->isValidNumberForRegion($number, $country_id)) {
                return (new self())->mergeFrom($number);
            } else {
                return null;
            }
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Format the phone number in the international format.
     *
     * Example:
     * ```
     * use DataLinx\PhpUtils\Fluent\FluentPhoneNumber;
     * $number = FluentPhoneNumber::from('(01) 584 61 00', 'SI');
     * echo $number->format(); // +386 1 584 61 00
     * ```
     *
     * @return string
     */
    public function format(): string
    {
        return PhoneNumberUtil::getInstance()->format($this, PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * Format the phone number in the national/local format.
     *
     * Example:
     * ```
     * use DataLinx\PhpUtils\Fluent\FluentPhoneNumber;
     * $number = FluentPhoneNumber::from('+385 1 4802 500', 'HR');
     * echo $number->formatNational(); // 01 4802 500
     * ```
     *
     * @return string
     */
    public function formatNational(): string
    {
        return PhoneNumberUtil::getInstance()->format($this, PhoneNumberFormat::NATIONAL);
    }

    /**
     * Format the phone number in URI format, which can be used for HTML anchor href attribute.
     *
     * Example:
     * ```
     * use DataLinx\PhpUtils\Fluent\FluentPhoneNumber;
     * $number = FluentPhoneNumber::from('(01) 584 61 00', 'SI');
     * echo $number->formatURI(); // tel:+386-1-584-61-00
     * ```
     *
     * @return string
     */
    public function formatURI(): string
    {
        return PhoneNumberUtil::getInstance()->format($this, PhoneNumberFormat::RFC3966);
    }
}
