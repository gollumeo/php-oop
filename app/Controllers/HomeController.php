<?php

namespace app\Controllers;

use app\Views\View;

class HomeController extends Controller
{
    public function welcome(): View
    {
        return $this->view('welcome', data: [], title: 'Welcome!');
    }
}