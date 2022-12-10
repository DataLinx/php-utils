<?php

namespace DataLinx\PhpUtils;

class Email
{
    /**
     * Check if the email domain is a valid domain with MX records
     *
     * @param string $email
     * @return bool
     */
    public static function isValidDomain(string $email): bool
    {
        $arr = explode('@', $email);

        if (is_array($arr) and count($arr) === 2) {
            return checkdnsrr(end($arr));
        }

        return false;
    }
}
