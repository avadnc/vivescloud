<?php

namespace Dummy;

class Type
{

    public static function requireArray(array $var)
    {
        return $var;
    }

    public static function requireString(array $var)
    {
        return $var;
    }

    public function toArray($var)
    {
        return (array) $var;
    }

    public function toString($var)
    {
        return (string) $var;
    }
}
