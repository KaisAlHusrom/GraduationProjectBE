<?php

namespace App\Helpers;

class StringsHelper
{
    static function snakeToCamel($snake_case_string): string
    {
        return lcfirst(str_replace('_', '', ucwords($snake_case_string, '_')));
    }

    static function camelToSnake($camelCaseString): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $camelCaseString));
    }
}
