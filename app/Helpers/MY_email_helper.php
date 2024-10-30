<?php

namespace App\Helpers;

use CodeIgniter\HTTP\Exceptions\HTTPException;

if (!function_exists('valid_email')) {

    /**
     * Validate an email address using PHP's built-in filter.
     * 
     * @param string $address The email address to check.
     * @return bool
     */
    function valid_email(string $address): bool
    {
        return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('name_email_format')) {

    /**
     * Format the name and email into a string.
     * 
     * @param string $name The name to format.
     * @param string $email The email to format.
     * @return string Formatted name and email.
     */
    function name_email_format(string $name, string $email): string
    {
        return sprintf('%s <%s>', $name, $email);
    }
}
