<?php
declare(strict_types=1);

function renderView(string $view, array $data = [], string $title = 'Employee Management System', string $page = 'dashboard'): void
{
    extract($data);
    $content = APP_ROOT . '/views/' . $view;
    if (!file_exists($content)) {
        throw new RuntimeException('View not found: ' . $view);
    }
    include APP_ROOT . '/views/layout.php';
}
