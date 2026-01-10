<?php

namespace App\Services;

use App\Models\CashReport;

class StatusService
{
    const STATUS_DELETED  = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const UNIT_PSC = 1;
    const UNIT_KG = 2;
    const UNIT_METER = 3;
    const UNIT_LITER = 4;

    const CURRENCY_UZS = 1;
    const CURRENCY_USD = 2;

    public static function getList()
    {
        return [
            self::STATUS_ACTIVE => 'Фаол',
            self::STATUS_INACTIVE => 'Фаол эмас',
            self::STATUS_DELETED  => 'Ўчирилган',
        ];
    }

    public static function getTypeCount()
    {
        return [
            self::UNIT_PSC => 'дона',
            self::UNIT_KG => 'кг',
            // self::UNIT_METER => 'метр',
            // self::UNIT_LITER => 'литр',
        ];
    }

    public static function getCurrency()
    {
        return [
            self::CURRENCY_UZS => 'сўм',
            self::CURRENCY_USD => '$',
        ];
    }
}
