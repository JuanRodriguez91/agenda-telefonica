<?php

declare(strict_types=1);

use App\Utils\Config;
use App\Utils\Router;

require_once __DIR__ . '/../app/Utils/Autoload.php';

Config::loadEnv(__DIR__ . '/../.env');
(new Router(__DIR__ . '/../routes.php'))->dispatch();
