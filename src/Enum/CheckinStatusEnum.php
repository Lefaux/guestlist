<?php

namespace App\Enum;

final class CheckinStatusEnum
{
    public const OPEN = 'OPEN';
    public const CHECKED_IN = 'CHECKED_IN';
    public const CHECKED_IN_WITH_NOSHOWS = 'CHECKED_IN_WITH_NOSHOWS';
    public const CANCELLED = 'CANCELLED';

    /**
     * @var array<string, string>
     */
    protected static array $optionNames = [
        self::OPEN => 'Guest is planned',
        self::CHECKED_IN => 'Guest checked in with all pluses',
        self::CHECKED_IN_WITH_NOSHOWS => 'Guest checked in with fewer pluses',
        self::CANCELLED => 'Guest will not be here',
    ];

    public static function getName(?string $option): string
    {
        return self::$optionNames[$option] ?? ('Unknown option (' . $option . ')');
    }

    /**
     * @param bool $withDescription
     * @return array<int|string, string>
     */
    public static function getAvailableOptions(bool $withDescription = false): array
    {
        return $withDescription ? self::$optionNames : array_keys(self::$optionNames);
    }

    public static function isOption(string $option): bool
    {
        return isset(self::$optionNames[$option]);
    }
}