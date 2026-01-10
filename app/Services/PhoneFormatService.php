<?php

namespace App\Services;

class PhoneFormatService
{
    public static function uzPhone($phone)
    {
        return preg_replace(
            "/(\+998)(\d{2})(\d{3})(\d{2})(\d{2})/",
            "$1 $2 $3 $4 $5",
            $phone
        );
    }
}
