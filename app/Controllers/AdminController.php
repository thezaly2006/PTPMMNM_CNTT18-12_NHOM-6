<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Database;
use App\Models\Document;
use App\Models\ExportJob;
use App\Models\User;

class AdminController extends BaseController
{
    /**
     * Helper tính dung lượng một thư mục
     */
    private function getDirectorySize(string $dir): int
    {
        $size = 0;
        if (!is_dir($dir)) {
            return 0;
        }
        $files = scandir($dir);
        if ($files === false) {
            return 0;
        }
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $size += (int)filesize($path);
            } elseif (is_dir($path)) {
                $size += $this->getDirectorySize($path);
            }
        }
        return $size;
    }

    /**
     * 1. Dashboard Tổng quan Đề tài & Hệ thống
     */
    public function dashboard(): void
    {
        $stats = ExportJob::getOverallStats();

        // Kiểm tra dung lượng các thư mục lưu trữ
        $storageRoot = dirname(ROOT_PATH) . '/DeTai12/storage';
        $sourcesDir = ROOT_PATH . '/storage/sources';
        $outputsDir = ROOT_PATH . '/storage/outputs';
        $tempDir = ROOT_PATH . '/storage/temp';
        $logsDir = ROOT_PATH . '/storage/logs';

        $storageDetails = [
            'sources_size' => $this->getDirectorySize($sourcesDir),
            'outputs_size' => $this->getDirectorySize($outputsDir),
            'temp_size' => $this->getDirectorySize($tempDir),
            'logs_size' => $this->getDirectorySize($logsDir),
        ];
        $storageDetails['total_size'] = array_sum($storageDetails);

        // Kiểm tra môi trường hệ thống
        $pdo = Database::getConnection();
        $dbVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);

        // Kiểm tra binary QPDF
        $qpdfAvailable = false;
        $qpdfVersion = 'Chưa cài đặt';
        try {
            $output = @shell_exec('qpdf --version 2>&1');
            if ($output && stripos($output, 'qpdf version') !== false) {
                $qpdfAvailable = true;
                $lines = explode("\n", trim($output));
                $qpdfVersion = $lines[0] ?? 'Đã cài đặt';
            }
        } catch (\Throwable $e) {}

        $serverEnv = [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS . ' (' . PHP_OS_FAMILY . ')',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'timezone' => date_default_timezone_get(),
            'db_version' => $dbVersion,
            'qpdf_available' => $qpdfAvailable,
            'qpdf_version' => $qpdfVersion,
        ];

        // Lấy danh sách tài liệu và bản xuất gần nhất trên toàn hệ thống
        $recentUsers = User::getPaginated(1, 5, null, null, 'created_at_desc');
        $recentExports = ExportJob::getRecentAll(5);
        $recentDocs = Document::getPaginatedForAdmin(1, 5, null, null, 'created_at_desc');

        $this->render('admin.dashboard', [
            'pageTitle' => 'Tổng quan Quản trị - PDF Workspace',
            'activeNav' => 'admin_dashboard',
            'stats' => $stats,
            'storageDetails' => $storageDetails,
            'serverEnv' => $serverEnv,
            'recentUsers' => $recentUsers,
            'recentExports' => $recentExports,
            'recentDocs' => $recentDocs,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    /**
     * 2. Quản lý Người dùng
     */
    public function users(): void
    {
        $search = trim($_GET['q'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $sort = trim($_GET['sort'] ?? 'created_at_desc');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        if (!empty($_GET['per_page'])) {
            $pp = (int)$_GET['per_page'];
            if (in_array($pp, [10, 20, 50], true)) {
                $perPage = $pp;
            }
        }

        $totalUsers = User::countAll($search, $role);
        $totalPages = (int)ceil($totalUsers / $perPage);
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $users = User::getPaginated($page, $perPage, $search, $role, $sort);

        $this->render('admin.users', [
            'pageTitle' => 'Quản lý Người dùng - PDF Workspace',
            'activeNav' => 'admin_users',
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'sort' => $sort,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalUsers,
            'totalPages' => $totalPages,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function updateUserRole(): void
    {
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');

        if ($userId === auth_id()) {
            flash('error', 'Bạn không thể tự hạ quyền quản trị viên của chính mình.');
            redirect('/admin/users');
        }

        if (User::updateRole($userId, $role)) {
            flash('success', 'Đã cập nhật vai trò người dùng thành công.');
        } else {
            flash('error', 'Cập nhật vai trò thất bại. Vai trò không hợp lệ.');
        }
        redirect('/admin/users');
    }

    public function toggleUserStatus(): void
    {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId === auth_id()) {
            flash('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
            redirect('/admin/users');
        }

        User::toggleStatus($userId);
        flash('success', 'Đã thay đổi trạng thái kích hoạt tài khoản.');
        redirect('/admin/users');
    }

    public function resetUserPassword(): void
    {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';

        if (strlen($newPassword) < 6) {
            flash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
            redirect('/admin/users');
        }

        User::updatePassword($userId, $newPassword);
        flash('success', 'Đã đặt lại mật khẩu cho người dùng thành công.');
        redirect('/admin/users');
    }

    public function createUser(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'user');

        if (empty($name) || empty($email) || empty($password)) {
            flash('error', 'Vui lòng điền đầy đủ họ tên, email và mật khẩu.');
            redirect('/admin/users');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Email không hợp lệ.');
            redirect('/admin/users');
        }

        if (User::findByEmail($email)) {
            flash('error', 'Email này đã tồn tại trong hệ thống.');
            redirect('/admin/users');
        }

        User::createWithRole($name, $email, $password, $role);
        flash('success', "Đã tạo tài khoản {$role} thành công cho {$name}.");
        redirect('/admin/users');
    }

    public function deleteUser(): void
    {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId === auth_id()) {
            flash('error', 'Bạn không thể xóa tài khoản của chính mình.');
            redirect('/admin/users');
        }

        User::delete($userId);
        flash('success', 'Đã xóa người dùng và toàn bộ dữ liệu liên quan thành công.');
        redirect('/admin/users');
    }

    /**
     * 3. Quản lý Toàn bộ Tài liệu Hệ thống
     */
    public function documents(): void
    {
        $search = trim($_GET['q'] ?? '');
        $filterUserId = !empty($_GET['user_id']) ? (int)$_GET['user_id'] : null;
        $sort = trim($_GET['sort'] ?? 'created_at_desc');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        if (!empty($_GET['per_page'])) {
            $pp = (int)$_GET['per_page'];
            if (in_array($pp, [10, 20, 50], true)) {
                $perPage = $pp;
            }
        }

        $totalDocs = Document::countAllForAdmin($search, $filterUserId);
        $totalPages = (int)ceil($totalDocs / $perPage);
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $documents = Document::getPaginatedForAdmin($page, $perPage, $search, $filterUserId, $sort);
        $allUsers = User::getAll();

        $this->render('admin.documents', [
            'pageTitle' => 'Quản lý Tài liệu - PDF Workspace',
            'activeNav' => 'admin_documents',
            'documents' => $documents,
            'allUsers' => $allUsers,
            'search' => $search,
            'filterUserId' => $filterUserId,
            'sort' => $sort,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalDocs,
            'totalPages' => $totalPages,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function deleteDocument(): void
    {
        $docId = (int)($_POST['document_id'] ?? 0);
        if (Document::deleteByAdmin($docId)) {
            flash('success', 'Đã xóa tài liệu nguồn khỏi hệ thống thành công.');
        } else {
            flash('error', 'Không tìm thấy tài liệu cần xóa.');
        }
        redirect('/admin/documents');
    }

    /**
     * 4. Giám sát Xuất & Snapshot Toàn hệ thống
     */
    public function exports(): void
    {
        $search = trim($_GET['q'] ?? '');
        $type = trim($_GET['type'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $filterUserId = !empty($_GET['user_id']) ? (int)$_GET['user_id'] : null;
        $sort = trim($_GET['sort'] ?? 'created_at_desc');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        if (!empty($_GET['per_page'])) {
            $pp = (int)$_GET['per_page'];
            if (in_array($pp, [10, 20, 50], true)) {
                $perPage = $pp;
            }
        }

        $totalJobs = ExportJob::countAllForAdmin($search, $type, $status, $filterUserId);
        $totalPages = (int)ceil($totalJobs / $perPage);
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $exports = ExportJob::getPaginatedForAdmin($page, $perPage, $search, $type, $status, $filterUserId, $sort);
        $allUsers = User::getAll();

        $this->render('admin.exports', [
            'pageTitle' => 'Giám sát Xuất & Snapshot - PDF Workspace',
            'activeNav' => 'admin_exports',
            'exports' => $exports,
            'allUsers' => $allUsers,
            'search' => $search,
            'type' => $type,
            'status' => $status,
            'filterUserId' => $filterUserId,
            'sort' => $sort,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalJobs,
            'totalPages' => $totalPages,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function exportDetail(): void
    {
        $jobId = (int)($_GET['id'] ?? 0);
        $job = ExportJob::getJobDetailsForAdmin($jobId);

        if (!$job) {
            flash('error', 'Không tìm thấy thông tin bản xuất.');
            redirect('/admin/exports');
        }

        $snapshotPages = ExportJob::getJobPagesSnapshot($jobId);

        $this->render('admin.export_detail', [
            'pageTitle' => 'Chi tiết Snapshot #' . $job['id'] . ' - Admin PDF Workspace',
            'activeNav' => 'admin_exports',
            'job' => $job,
            'snapshotPages' => $snapshotPages,
        ]);
    }

    /**
     * 5. Cấu hình & Bảo trì Hệ thống
     */
    public function system(): void
    {
        $tempDir = ROOT_PATH . '/storage/temp';
        $tempFiles = [];
        if (is_dir($tempDir)) {
            $files = scandir($tempDir);
            if ($files) {
                foreach ($files as $f) {
                    if ($f === '.' || $f === '..' || $f === '.gitkeep') continue;
                    $filePath = $tempDir . '/' . $f;
                    if (is_file($filePath)) {
                        $tempFiles[] = [
                            'name' => $f,
                            'size' => filesize($filePath),
                            'mtime' => filemtime($filePath),
                        ];
                    }
                }
            }
        }

        $storageDetails = [
            'sources' => $this->getDirectorySize(ROOT_PATH . '/storage/sources'),
            'outputs' => $this->getDirectorySize(ROOT_PATH . '/storage/outputs'),
            'temp' => $this->getDirectorySize(ROOT_PATH . '/storage/temp'),
            'logs' => $this->getDirectorySize(ROOT_PATH . '/storage/logs'),
        ];

        $this->render('admin.system', [
            'pageTitle' => 'Bảo trì Hệ thống - PDF Workspace',
            'activeNav' => 'admin_system',
            'tempFiles' => $tempFiles,
            'storageDetails' => $storageDetails,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function cleanTemp(): void
    {
        $tempDir = ROOT_PATH . '/storage/temp';
        $deletedCount = 0;
        $freedBytes = 0;

        if (is_dir($tempDir)) {
            $files = scandir($tempDir);
            if ($files) {
                foreach ($files as $f) {
                    if ($f === '.' || $f === '..' || $f === '.gitkeep') continue;
                    $path = $tempDir . '/' . $f;
                    if (is_file($path)) {
                        $freedBytes += (int)filesize($path);
                        if (@unlink($path)) {
                            $deletedCount++;
                        }
                    }
                }
            }
        }

        flash('success', "Đã dọn dẹp thành công {$deletedCount} tệp tạm (giải phóng " . format_bytes($freedBytes) . ").");
        redirect('/admin/system');
    }
}
