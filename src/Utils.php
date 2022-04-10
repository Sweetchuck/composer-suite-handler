<?php

declare(strict_types = 1);

namespace Sweetchuck\ComposerSuiteHandler;

class Utils
{
    public static int $jsonEncodeFlags = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE;

    /**
     * @param array<int|string, mixed> $items
     */
    public static function isVector(array $items): bool
    {
        if (!$items) {
            return false;
        }

        return array_keys($items) === range(0, count($items) - 1);
    }

    public static function isDefaultComposer(string $fileName): bool
    {
        return preg_match('@(^|/)?composer\.json$@', $fileName) === 1;
    }

    /**
     * @param array<int|string, mixed> $data
     *
     * @return string|false
     */
    public static function encode(array $data)
    {
        return json_encode($data, static::$jsonEncodeFlags);
    }

    /**
     * @return array<int|string, mixed>
     */
    public static function decode(string $encoded): array
    {
        return json_decode($encoded, true);
    }
}
