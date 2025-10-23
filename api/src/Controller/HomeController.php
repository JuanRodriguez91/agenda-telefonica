<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Connection $connection): Response
    {
        try {
            $connection->executeQuery('SELECT 1');
            $message = '✅ Conexión a la base de datos correcta';
        } catch (\Exception $e) {
            $message = '❌ Conexión a la base de datos fallida';
        }

        return new Response(
            '<html><body><h1>'.$message.'</h1></body></html>'
        );
    }
}
