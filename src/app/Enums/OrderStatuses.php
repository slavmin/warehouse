<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatuses: string
{
    case STATUS_ACTIVE = 'active';
    case STATUS_COMPLETED = 'completed';
    case STATUS_CANCELED = 'canceled';

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
