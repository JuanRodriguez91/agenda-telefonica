<?php

namespace App\Utils;

class Session
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function exist(string $key): bool
    {
        return isset($_SESSION[$key]);
    }


    public function remove(string $key): void
    {
        if ($this->exist($key)) {
            unset($_SESSION[$key]);
        }
    }
}
