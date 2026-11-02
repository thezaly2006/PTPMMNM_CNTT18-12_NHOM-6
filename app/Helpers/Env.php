<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Lop doc cau hinh tu file .env don gian, manh me
 */
class Env
{
    private static bool $loaded = false;

    public static function load(string $filePath): void
    {
        if (self::$loaded || !file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            // Bo qua comment bat dau bang dau #
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Phan tach KEY=VALUE
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                // Loai bo dau ngoac kep/don neu co
                if (
                    (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))
                ) {
                    $value = substr($value, 1, -1);
                }

                // Xu ly boolean va null
                $lowered = strtolower($value);
                if ($lowered === 'true') {
                    $parsedValue = true;
                } elseif ($lowered === 'false') {
                    $parsedValue = false;
                } elseif ($lowered === 'null') {
                    $parsedValue = null;
                } else {
                    $parsedValue = $value;
                }

                $_ENV[$key] = $parsedValue;
                $_SERVER[$key] = $parsedValue;
                putenv("{$key}={$value}");
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}
