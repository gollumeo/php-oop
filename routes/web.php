<?php

use app\Controllers\HomeController;
use app\Route;

Route::get('/', [HomeController::class, 'welcome']);