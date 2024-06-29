<?php

use app\Route;
use Dotenv\Dotenv;

require __DIR__ .  '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

Route::init();

require __DIR__ . '/../routes/web.php';