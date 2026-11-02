<?php

declare(strict_types=1);

use App\Helpers\Env;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($_SESSION['user']) && empty($_SESSION['user']['role'])) {
            try {
                $u = \App\Models\User::findById((int)$_SESSION['user']['id']);
                if ($u) {
                    $_SESSION['user']['role'] = $u['role'] ?? 'user';
                }
            } catch (\Throwable $e) {}
        }
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('auth_id')) {
    function auth_id(): ?int
    {
        $user = auth_user();
        return $user ? (int)$user['id'] : null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $user = auth_user();
        return !empty($user) && ($user['role'] ?? 'user') === 'admin';
    }
}

if (!function_exists('response_json')) {
    function response_json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $message = null): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return null;
        }
        $val = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $val;
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('render_pagination')) {
    function render_pagination(
        int $currentPage,
        int $totalPages,
        int $totalItems,
        int $perPage,
        string $baseUrl,
        array $queryParams = []
    ): string {
        if ($totalItems === 0) {
            return '';
        }

        if ($totalPages <= 1) {
            return '
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top bg-light-subtle small text-muted">
                <div>Tổng cộng <strong>' . $totalItems . '</strong> mục</div>
            </div>';
        }

        $from = (($currentPage - 1) * $perPage) + 1;
        $to = min($currentPage * $perPage, $totalItems);

        $buildUrl = function (int $page) use ($baseUrl, $queryParams): string {
            $params = array_merge($queryParams, ['page' => $page]);
            return htmlspecialchars($baseUrl . '?' . http_build_query($params), ENT_QUOTES, 'UTF-8');
        };

        $html = '<div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top bg-light-subtle small">';
        $html .= '<div class="text-muted mb-2 mb-md-0">';
        $html .= 'Hiển thị <strong>' . $from . '</strong> – <strong>' . $to . '</strong> trong tổng số <strong>' . $totalItems . '</strong> mục';
        $html .= '</div>';

        $html .= '<nav aria-label="Phân trang"><ul class="pagination pagination-sm mb-0">';

        if ($currentPage > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl($currentPage - 1) . '">&laquo; Trước</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Trước</span></li>';
        }

        $range = 2;
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);

        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl(1) . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($p = $start; $p <= $end; $p++) {
            if ($p === $currentPage) {
                $html .= '<li class="page-item active" aria-current="page"><span class="page-link">' . $p . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl($p) . '">' . $p . '</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl($totalPages) . '">' . $totalPages . '</a></li>';
        }

        if ($currentPage < $totalPages) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $buildUrl($currentPage + 1) . '">Sau &raquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Sau &raquo;</span></li>';
        }

        $html .= '</ul></nav>';
        $html .= '</div>';

        return $html;
    }
}
