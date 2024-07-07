<?php

namespace app\Views;

class View
{
    protected string $header = 'partials/header.php';
    protected string $footer = 'partials/footer.php';
    protected string $title;

    public function render(string $view, array $data = [], $title = null): void
    {
        $this->title = $title ?? null;
        $data['title'] = $this->title ?? null;
        extract($data);

        if (file_exists(__DIR__ . "/{$this->header}")) {
            include __DIR__ . "/{$this->header}";
        }

        include __DIR__ . "/$view.php";

        if (file_exists(__DIR__ . "/{$this->footer}")) {
            include __DIR__ . "/{$this->footer}";
        }
    }
}