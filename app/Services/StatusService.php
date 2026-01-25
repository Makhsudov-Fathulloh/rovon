<?php

namespace App\Services;

class StatusService
{
    const TYPE_ALL = 1;
    const TYPE_SPARE_PART = 2;
    const TYPE_RAW_MATERIAL = 3;
    const TYPE_PRODUCT = 4;

    const DEFECT_RAW_MATERIAL = 1;
    const DEFECT_PREVIOUS_STAGE = 2;

    const STATUS_DELETED  = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const UNIT_PSC = 1;
    const UNIT_KG = 2;
    const UNIT_METER = 3;
    const UNIT_LITER = 4;

    const CURRENCY_UZS = 1;
    const CURRENCY_USD = 2;


    public static function getType()
    {
        return [
            self::TYPE_ALL => 'Умумий',
            self::TYPE_SPARE_PART => 'Эхтиёт қисм',
            self::TYPE_RAW_MATERIAL => 'Хомашё',
            self::TYPE_PRODUCT => 'Тайёр маҳсулот',
        ];
    }

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
