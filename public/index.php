<?php

use app\Route;
use Dotenv\Dotenv;

require __DIR__ .  '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..' );
$dotenv->load();

require_once __DIR__ . '/../helpers.php';

Route::init();

require __DIR__ . '/../routes/web.php';

Route::run();