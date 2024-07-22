<?php

namespace App\Utils;

class InputValidator
{
    public static function testInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function isValidUsername($username)
    {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
