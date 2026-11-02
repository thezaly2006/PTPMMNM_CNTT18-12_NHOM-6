<?php

declare(strict_types=1);

namespace App\Middleware;

class CsrfMiddleware
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!verify_csrf($token)) {
                http_response_code(419);
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    response_json(['success' => false, 'message' => 'Phiên làm việc hết hạn (CSRF Token không hợp lệ). Vui lòng tải lại trang.'], 419);
                }
                die("Lỗi bảo mật 419: CSRF Token không hợp lệ hoặc đã hết hạn. Vui lòng quay lại và tải lại trang.");
            }
        }
    }
}
