<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    protected function render(string $viewPath, array $data = []): void
    {
        // Trích xuất biến từ mảng dữ liệu để view có thể dùng trực tiếp
        extract($data);

        $viewFile = ROOT_PATH . '/resources/views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) {
            die("Không tìm thấy view: {$viewPath} (Đường dẫn: {$viewFile})");
        }

        require $viewFile;
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        response_json($data, $statusCode);
    }
}
