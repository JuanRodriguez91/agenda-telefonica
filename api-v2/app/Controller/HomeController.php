<?php

namespace App\Controller;

use App\Repository\HomeRepository;
use PDOException;

class HomeController
{
    public function index(): void
    {
        try {
            (new HomeRepository())->checkConnection();

            $message = '✅ Conexión a la base de datos correcta';
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            $message = '❌ Conexión a la base de datos fallida';
        }

        echo "<html><body><h1>$message</h1></body></html>";
    }
}