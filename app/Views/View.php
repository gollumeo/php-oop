<?php

namespace app\Views;

abstract class View
{
    protected string $header = 'partials/header.php';
    protected string $footer = 'partials/footer.php';
    protected string $title;

    protected function render(string $view, array $data = []): void
    {
        $data['title'] = $this->title ?? null;
        extract($data);

        if (file_exists(__DIR__ . "/../../Views/{$this->header}")) {
            include __DIR__ . "/../../Views/{$this->header}";
        }

        include __DIR__ . "/../../Views/$view.php";

        if (file_exists(__DIR__ . "/../../Views/{$this->footer}")) {
            include __DIR__ . "/../../Views/{$this->footer}";
        }
    }
}