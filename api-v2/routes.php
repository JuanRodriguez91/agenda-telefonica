<?php

use App\Controller\HomeController;
use App\Controller\ContactController;
use App\Controller\UserController;


return [
    // Home (check conection)
    ['GET', '/', HomeController::class, 'index'],

    // Contacts
    ['GET', '/contacts', ContactController::class, 'list'],
    ['GET', '/contacts/{id}', ContactController::class, 'show'],
    ['POST', '/contacts', ContactController::class, 'create'],
    ['PUT', '/contacts/{id}', ContactController::class, 'update'],
    ['DELETE', '/contacts/{id}', ContactController::class, 'de..lete'],

    // User
    ['POST', '/user/login', UserController::class, 'login'],
    ['GET', '/user/logout', UserController::class, 'logout'],
    ['GET', '/user/get-logged', UserController::class, 'getLogged'],
    ['POST', '/user', UserController::class, 'create'],
];