<?php

namespace app\Views;

abstract class View
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../Views/$view.php";
    }
}