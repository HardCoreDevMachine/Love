<?php

namespace App\Helper;


class CompabilityHelper
{
    public static function compabilityCheck($woman, $man): bool
    {
        return (
            abs(strlen($woman->name) - strlen($man->name)) < 10
            && abs($woman->age - $man->age) < 10
        );
    }
}
