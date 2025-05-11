<?php

declare(strict_types=1);

namespace App\Enums;

enum StockOperations: string
{
    case INCREMENT = 'increment';
    case DECREMENT = 'decrement';

    /**
     * @return string[]
     */
    public static function valuesList(): array
    {
        $arr = [];

        foreach (self::cases() as $case) {
            $arr[] = $case->value;
        }

        return $arr;
    }
}
