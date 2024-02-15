<?php

namespace App\EnumTypes;

class EnumType
{
    const VALUES = [];

    public static function getFormChoices()
    {
        return array_flip(static::VALUES);
    }

    public static function getLibelle(?string $value): string
    {
        if (!$value) {
            return '';
        }

        if (isset(static::VALUES[$value])) {
            return static::VALUES[$value];
        }

        return '';
    }

    public static function equals(?string $value, string $constant): bool
    {
        if(null === $value) {
            return false;
        }

        return strcasecmp($value, $constant) == 0;
    }
}
