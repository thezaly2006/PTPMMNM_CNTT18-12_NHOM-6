<?php

declare(strict_types=1);

// Hỗ trợ PHP CLI Built-in Web Server phục vụ file tĩnh và route URL
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if ($path !== '/' && file_exists(__DIR__ . $path)) {
        return false;
    }
}

// Thiết lập múi giờ thời gian thực Việt Nam (UTC+7)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Thiết lập bảo mật Session
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Lax');

// Bắt đầu Session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));

// 1. Nạp Composer Vendor nếu có
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// 2. Tự động nạp các class trong namespace App\ (đảm bảo hoạt động ngay không cần chờ dump-autoload)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Nạp các hàm trợ giúp toàn cục
require_once ROOT_PATH . '/app/Helpers/functions.php';

// Nạp biến môi trường từ .env
\App\Helpers\Env::load(ROOT_PATH . '/.env');

// Nạp các tuyến đường (Routes)
require_once ROOT_PATH . '/routes/web.php';

// Điều hướng request
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

try {
    \App\Helpers\Router::dispatch($requestMethod, $requestUri);
} catch (\Throwable $e) {
    // Ghi log lỗi ra storage/logs/system.log
    $logDir = ROOT_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    file_put_contents(
        $logDir . '/system.log',
        sprintf("[%s] %s in %s:%d\nStack Trace:\n%s\n\n", date('Y-m-d H:i:s'), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()),
        FILE_APPEND
    );

    if (env('APP_DEBUG', true)) {
        http_response_code(500);
        echo "<div style='font-family:sans-serif;padding:30px;background:#fef2f2;color:#991b1b;border:1px solid #f87171;border-radius:8px;max-width:800px;margin:40px auto;'>";
        echo "<h3 style='margin-top:0;'>Đã xảy ra lỗi hệ thống (500)</h3>";
        echo "<p><strong>Thông báo:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Vị trí:</strong> " . htmlspecialchars($e->getFile()) . " (Dòng " . $e->getLine() . ")</p>";
        echo "<pre style='background:#fff;padding:15px;border-radius:4px;overflow:auto;border:1px solid #fca5a5;font-size:12px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        echo "<h3>500 - Lỗi máy chủ nội bộ. Vui lòng thử lại sau.</h3>";
    }
}
