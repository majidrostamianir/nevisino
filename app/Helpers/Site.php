<?php

namespace App\Helpers;

class Site
{
    public static function isGhesti(): bool
    {
        return str_starts_with(request()->getHost(), 'ghesti.');
    }

    public static function isMain(): bool
    {
        return ! self::isGhesti();
    }

    public static function otherHost(): string
    {
        return self::isGhesti() ? 'nevisino.ir' : 'ghesti.nevisino.ir';
    }

    public static function otherUrl(string $path = '/'): string
    {
        return 'https://' . self::otherHost() . '/' . ltrim($path, '/');
    }
}