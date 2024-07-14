<?php

namespace app\Controllers;

use app\Views\View;

abstract class Controller
{
    public function view(string $view, array $data = [], $title = null): View
    {
        $viewInstance = new View();
        extract($data);
        $viewInstance->render($view, $data, $title);
        return $viewInstance;
    }
}
