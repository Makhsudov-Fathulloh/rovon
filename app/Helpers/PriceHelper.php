<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * Format price with optional currency symbol
     *
     * @param float|int $price
     * @param string|null $currency
     * @param bool $withSymbol
     * @return string
     */
    public static function format($price, $currency, bool $withSymbol = true)
    {
        $precision = $currency === \App\Services\StatusService::CURRENCY_UZS ? 0 : 2;

        $symbol = match ($currency) {
            \App\Services\StatusService::CURRENCY_UZS => 'сўм',
            \App\Services\StatusService::CURRENCY_USD => '$',
            default => $currency,
        };

        $formatted = number_format($price, $precision, $precision ? '.' : '', ' ');

        return $withSymbol ? $formatted . ' ' . $symbol : $formatted;
    }

    /**
     * Format array of prices per currency
     * Example: ['1' => 12500, '2' => 1.5]
     * @param array $prices
     * @param array $currencies
     * @param bool $withSymbol
     * @return string
     */
    public static function formatArray($data, $currencies, $isDesktop = true)
    {
        // 🔹 Agar null bo‘lsa, bo‘sh qator qaytaramiz
        if (empty($data)) {
            return '-';
        }

        // 🔹 Agar string bo‘lsa — JSON sifatida decode qilamiz
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        // 🔹 Agar decode xato bo‘lsa yoki array bo‘lmasa
        if (!is_array($data)) {
            return '-';
        }

        // 🔹 Har bir valyuta uchun formatlash
        $formatted = [];
        foreach ($currencies as $currency => $label) {
            $amount = $data[$currency] ?? 0;
            $formatted[] = self::format($amount, $currency);
        }

        // 🔹 Natijani bir qatorda chiqaramiz (masalan: "12 500 сўм / 1.37$")
//        return implode(' / ', $formatted);
        return $isDesktop ? implode('<br>', $formatted) : implode(' / ', $formatted);
    }
}
