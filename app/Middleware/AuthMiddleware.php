<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\User;

class AuthMiddleware
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                response_json(['success' => false, 'message' => 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.'], 401);
            }
            flash('error', 'Vui lòng đăng nhập để truy cập trang này.');
            redirect('/login');
        }

        // Kiểm tra thời gian thực trạng thái tài khoản trong cơ sở dữ liệu
        $userId = (int)$_SESSION['user']['id'];
        $currentUser = User::findById($userId);

        if (!$currentUser || (isset($currentUser['is_active']) && (int)$currentUser['is_active'] === 0)) {
            unset($_SESSION['user']);
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
                session_start();
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                response_json(['success' => false, 'message' => 'Tài khoản của bạn đã bị quản trị viên tạm khóa.'], 403);
            }

            flash('error', 'Tài khoản của bạn đã bị quản trị viên tạm khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.');
            redirect('/login');
        }

        // Tự động đồng bộ vai trò và tên người dùng mới nhất vào session
        $_SESSION['user']['role'] = $currentUser['role'] ?? 'user';
        $_SESSION['user']['name'] = $currentUser['name'];
        $_SESSION['user']['email'] = $currentUser['email'];
    }
}
