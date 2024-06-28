<?php

namespace app\Controllers;

use app\Views\View;

abstract class Controller
{
    protected View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    abstract function index(): View;
}