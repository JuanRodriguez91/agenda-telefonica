<?php

namespace App\Controller;

use App\Utils\Response;

abstract class ApiJsonAbstractController
{
    public function __construct()
    {
        header("Content-Security-Policy: default-src 'none'");
        header("X-Frame-Options: DENY");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: no-referrer");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: true");
    }

    protected function json($data = [], int $status = Response::HTTP_OK): void
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        http_response_code($status);

        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if ($json === false) {
            http_response_code(Response::HTTP_INTERNAL_SERVER_ERROR);
            echo json_encode([
                'success' => false,
                'error' => 'Error generating JSON',
            ]);
            exit;
        }

        echo $json;
        exit;
    }


    protected function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf_token'];
    }


    protected function verifyCsrf(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }
}