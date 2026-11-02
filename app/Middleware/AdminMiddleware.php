<?php

declare(strict_types=1);

namespace App\Middleware;

class AdminMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            flash('error', 'Vui lòng đăng nhập với tài khoản quản trị viên để tiếp tục.');
            redirect('/login');
        }

        if (!is_admin()) {
            flash('error', 'Bạn không có quyền truy cập trang Quản trị Đề tài (Admin).');
            redirect('/dashboard');
        }
    }
}
