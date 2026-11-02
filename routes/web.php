<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\DocumentController;
use App\Controllers\EditorController;
use App\Controllers\ExportController;
use App\Controllers\TestController;
use App\Helpers\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;

// 1. Tuyến đường trang chủ (Chuyển hướng thông minh)
Router::get('/', [DashboardController::class, 'index'], [AuthMiddleware::class]);

// 2. Tuyến đường Xác thực (Auth)
Router::get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
Router::post('/login', [AuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);

Router::get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
Router::post('/register', [AuthController::class, 'register'], [GuestMiddleware::class, CsrfMiddleware::class]);

Router::post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CsrfMiddleware::class]);

// 3. Tuyến đường Dashboard
Router::get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

// 4. Tuyến đường Quản lý Tài liệu (Documents)
Router::get('/documents', [DocumentController::class, 'index'], [AuthMiddleware::class]);
Router::post('/documents/upload', [DocumentController::class, 'upload'], [AuthMiddleware::class, CsrfMiddleware::class]);
Router::post('/documents/delete', [DocumentController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);
Router::get('/documents/view', [DocumentController::class, 'viewInline'], [AuthMiddleware::class]);
Router::get('/documents/download', [DocumentController::class, 'download'], [AuthMiddleware::class]);

// 5. Tuyến đường PDF Editor (Chức năng trọng tâm)
Router::get('/editor', [EditorController::class, 'index'], [AuthMiddleware::class]);
Router::post('/api/plans/save', [EditorController::class, 'savePlan'], [AuthMiddleware::class, CsrfMiddleware::class]);
Router::get('/api/documents/pages', [EditorController::class, 'getDocPages'], [AuthMiddleware::class]);

// 6. Tuyến đường Ghép & Tách PDF (Màn hình 5 & 6)
Router::get('/merge', [ExportController::class, 'showMerge'], [AuthMiddleware::class]);
Router::get('/split', [ExportController::class, 'showSplit'], [AuthMiddleware::class]);
Router::post('/api/export', [ExportController::class, 'exportFromPlan'], [AuthMiddleware::class, CsrfMiddleware::class]);
Router::post('/api/split', [ExportController::class, 'executeSplit'], [AuthMiddleware::class, CsrfMiddleware::class]);

// 7. Tuyến đường Lịch sử & Chi tiết xuất PDF (Màn hình 7 & 8)
Router::get('/exports', [ExportController::class, 'showExports'], [AuthMiddleware::class]);
Router::get('/exports/detail', [ExportController::class, 'showExportDetail'], [AuthMiddleware::class]);
Router::get('/exports/download', [ExportController::class, 'downloadOutput'], []);

// 8. Tuyến đường Kiểm thử Nghiệm thu (Test Suite)
Router::get('/tests', [TestController::class, 'index'], [AuthMiddleware::class]);
Router::post('/api/tests/run', [TestController::class, 'run'], [AuthMiddleware::class, CsrfMiddleware::class]);

// 9. Cổng Quản Trị Đề Tài (Admin Portal - Dành riêng cho Quản trị viên)
Router::get('/admin', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/dashboard', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);

// Quản lý người dùng
Router::get('/admin/users', [AdminController::class, 'users'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/create', [AdminController::class, 'createUser'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
Router::post('/admin/users/role', [AdminController::class, 'updateUserRole'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
Router::post('/admin/users/toggle-status', [AdminController::class, 'toggleUserStatus'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
Router::post('/admin/users/reset-password', [AdminController::class, 'resetUserPassword'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
Router::post('/admin/users/delete', [AdminController::class, 'deleteUser'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);

// Quản lý kho tài liệu hệ thống
Router::get('/admin/documents', [AdminController::class, 'documents'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/documents/delete', [AdminController::class, 'deleteDocument'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);

// Giám sát xuất & Snapshot
Router::get('/admin/exports', [AdminController::class, 'exports'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/exports/detail', [AdminController::class, 'exportDetail'], [AuthMiddleware::class, AdminMiddleware::class]);

// Bảo trì hệ thống & dọn dẹp bộ nhớ tạm
Router::get('/admin/system', [AdminController::class, 'system'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/system/clean-temp', [AdminController::class, 'cleanTemp'], [AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]);
