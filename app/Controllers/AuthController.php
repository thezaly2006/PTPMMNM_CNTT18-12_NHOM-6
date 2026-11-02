<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Database;
use App\Models\User;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        $this->render('auth.login', [
            'pageTitle' => 'Đăng nhập - PDF Workspace',
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flash('error', 'Vui lòng nhập đầy đủ email và mật khẩu.');
            redirect('/login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Định dạng email không hợp lệ.');
            redirect('/login');
        }

        // Tự động khởi tạo hoặc cập nhật tài khoản Demo (User) nếu chưa khớp
        if ($email === 'demo@pdfworkspace.test' && $password === 'Password@123') {
            $checkDemo = User::findByEmail($email);
            if (!$checkDemo) {
                User::createWithRole('Sinh Viên Thử Nghiệm', 'demo@pdfworkspace.test', 'Password@123', 'user');
            } else {
                if (($checkDemo['role'] ?? '') !== 'user' || !password_verify($password, $checkDemo['password_hash'])) {
                    $newHash = password_hash('Password@123', PASSWORD_BCRYPT);
                    Database::query("UPDATE users SET password_hash = :hash, role = 'user', is_active = 1 WHERE id = :id", [
                        'hash' => $newHash,
                        'id' => $checkDemo['id']
                    ]);
                }
            }
        }

        // Tự động khởi tạo hoặc cập nhật tài khoản Admin riêng biệt nếu chưa khớp
        if ($email === 'admin@pdfworkspace.test' && $password === 'Admin@123') {
            $checkAdmin = User::findByEmail($email);
            if (!$checkAdmin) {
                User::createWithRole('Quản Trị Viên Hệ Thống', 'admin@pdfworkspace.test', 'Admin@123', 'admin');
            } else {
                if (($checkAdmin['role'] ?? '') !== 'admin' || !password_verify($password, $checkAdmin['password_hash'])) {
                    $adminHash = password_hash('Admin@123', PASSWORD_BCRYPT);
                    Database::query("UPDATE users SET password_hash = :hash, role = 'admin', is_active = 1 WHERE id = :id", [
                        'hash' => $adminHash,
                        'id' => $checkAdmin['id']
                    ]);
                }
            }
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Email hoặc mật khẩu không chính xác.');
            redirect('/login');
        }

        // Chặn đăng nhập nếu tài khoản đã bị quản trị viên khóa
        if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
            flash('error', 'Tài khoản của bạn đã bị quản trị viên tạm khóa. Vui lòng liên hệ quản trị viên để được hỗ trợ.');
            redirect('/login');
        }

        // Tái tạo session ID chống Session Fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
        ];

        if (($user['role'] ?? 'user') === 'admin') {
            flash('success', 'Chào mừng Quản trị viên ' . htmlspecialchars($user['name']) . ' đăng nhập vào Cổng Quản Trị!');
            redirect('/admin');
        } else {
            flash('success', 'Chào mừng bạn quay trở lại, ' . htmlspecialchars($user['name']) . '!');
            redirect('/dashboard');
        }
    }

    public function showRegister(): void
    {
        $this->render('auth.register', [
            'pageTitle' => 'Đăng ký tài khoản - PDF Workspace',
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            flash('error', 'Vui lòng điền đầy đủ tất cả các trường.');
            redirect('/register');
        }

        if (mb_strlen($name) < 2) {
            flash('error', 'Họ tên phải có ít nhất 2 ký tự.');
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Địa chỉ email không đúng định dạng.');
            redirect('/register');
        }

        if (mb_strlen($password) < 6) {
            flash('error', 'Mật khẩu phải có độ dài tối thiểu 6 ký tự.');
            redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Xác nhận mật khẩu không trùng khớp.');
            redirect('/register');
        }

        $existing = User::findByEmail($email);
        if ($existing) {
            flash('error', 'Email này đã được sử dụng. Vui lòng chọn email khác hoặc đăng nhập.');
            redirect('/register');
        }

        $userId = User::create($name, $email, $password);

        // Tự động đăng nhập sau khi đăng ký
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
        ];

        flash('success', 'Tạo tài khoản thành công! Chào mừng bạn đến với PDF Workspace.');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        flash('success', 'Bạn đã đăng xuất thành công.');
        redirect('/login');
    }
}
