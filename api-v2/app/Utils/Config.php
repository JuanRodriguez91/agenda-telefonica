<?php

namespace App\Utils;

class Config
{
    private static $data = [];


    public static function loadEnv(string $file)
    {
        if (!file_exists($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$key, $val] = array_map('trim', explode('=', $line, 2) + [1 => '']);
            self::$data[$key] = $val;
        }


        if (!empty(self::get('APP_TIMEZONE'))) {
            date_default_timezone_set(self::get('APP_TIMEZONE'));
        }


        if (self::get('APP_DEBUG') === 'false' || self::get('APP_DEBUG') === '0') {
            ini_set('display_errors', '0');
        } else {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        }
    }


    public static function get(string $key, $default = null)
    {
        return self::$data[$key] ?? $default;
    }
}