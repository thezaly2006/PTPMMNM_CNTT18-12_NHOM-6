<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Quản trị - PDF Workspace'); ?></title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.468.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="https://unpkg.com/lucide@latest"><\/script>');
        }
    </script>
    <style>
        :root {
            --admin-sidebar-width: 275px;
            --admin-sidebar-bg: #0f172a;
            --admin-main-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.05), transparent 45%),
                        radial-gradient(circle at 90% 85%, rgba(245, 158, 11, 0.04), transparent 40%),
                        #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Quản trị Tối tân */
        .admin-sidebar {
            width: var(--admin-sidebar-width);
            background: #0f172a;
            color: #f1f5f9;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand {
            padding: 22px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.95);
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 60%, #b45309 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
        }

        .sidebar-menu {
            padding: 16px 14px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-label {
            font-size: 0.70rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 12px 12px 4px 12px;
            font-weight: 700;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 4px;
            position: relative;
        }

        .nav-link-custom:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transform: translateX(3px);
        }

        .nav-link-custom.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.25) 0%, rgba(139, 92, 246, 0.18) 100%);
            color: #ffffff;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, 0.4);
        }

        .nav-link-custom.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 3.5px;
            background: linear-gradient(180deg, #f59e0b 0%, #ec4899 100%);
            border-radius: 0 4px 4px 0;
        }

        .nav-icon-box {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.98);
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--admin-sidebar-width);
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--admin-sidebar-width));
        }

        .admin-topbar {
            height: 70px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 36px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.02);
        }

        .admin-content {
            padding: 32px 36px;
            flex-grow: 1;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-custom:hover {
            box-shadow: 0 12px 28px -4px rgba(99, 102, 241, 0.08), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
            border-color: rgba(99, 102, 241, 0.25);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 1.8s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>
</head>
<body>
    <!-- Sidebar Admin -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon-box">
                <i data-lucide="shield-alert" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-white">PDF Workspace</h6>
                    <span class="badge bg-warning text-dark px-1 py-0 fw-bold" style="font-size: 0.62rem;">ADMIN</span>
                </div>
                <small class="text-white-50" style="font-size: 0.72rem;">Quản trị Đề tài 12 - Mã Nguồn Mở</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Trung tâm Điều khiển</div>
            <a href="/admin" class="nav-link-custom <?php echo ($activeNav ?? '') === 'admin_dashboard' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(99, 102, 241, 0.2); color: #818cf8;">
                    <i data-lucide="layout-dashboard" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Tổng quan đề tài</span>
            </a>

            <div class="menu-label mt-3">Quản lý Tài nguyên</div>
            <a href="/admin/users" class="nav-link-custom <?php echo ($activeNav ?? '') === 'admin_users' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8;">
                    <i data-lucide="users" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Quản lý người dùng</span>
            </a>

            <a href="/admin/documents" class="nav-link-custom <?php echo ($activeNav ?? '') === 'admin_documents' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(168, 85, 247, 0.2); color: #c084fc;">
                    <i data-lucide="folder-archive" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Kho tài liệu hệ thống</span>
            </a>

            <a href="/admin/exports" class="nav-link-custom <?php echo ($activeNav ?? '') === 'admin_exports' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">
                    <i data-lucide="history" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Giám sát xuất & Snapshot</span>
            </a>

            <div class="menu-label mt-3">Hạ tầng & Đánh giá</div>
            <a href="/admin/system" class="nav-link-custom <?php echo ($activeNav ?? '') === 'admin_system' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                    <i data-lucide="server" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Bảo trì & Bộ nhớ</span>
            </a>

            <a href="/tests" class="nav-link-custom">
                <div class="nav-icon-box" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                    <i data-lucide="check-circle-2" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Kiểm thử nghiệm thu</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="/dashboard" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2 mb-2" style="border-radius: 8px; border-color: rgba(255, 255, 255, 0.2); font-size: 0.8rem;">
                <i data-lucide="external-link" style="width: 14px; height: 14px;"></i>
                <span>Vào PDF Workspace</span>
            </a>
            <form action="/logout" method="POST" class="m-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px; font-size: 0.8rem;">
                    <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-2 text-muted small">
                <div class="p-1 rounded bg-amber-50" style="background: #fef3c7;">
                    <i data-lucide="shield" style="width: 16px; height: 16px; color: #d97706;"></i>
                </div>
                <span class="text-secondary fw-semibold">Cổng Quản Trị Đề Tài</span>
                <span>/</span>
                <span class="text-dark fw-bold"><?php echo htmlspecialchars($pageTitle ?? 'Admin'); ?></span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-white border rounded-pill shadow-sm small text-muted">
                    <span class="pulse-dot"></span>
                    <span style="font-size: 0.78rem; font-weight: 500;">Máy chủ Sẵn sàng (UTC+7)</span>
                </div>
                <a href="/dashboard" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 shadow-sm rounded-pill">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Quay về Workspace</span>
                </a>
            </div>
        </header>

        <div class="admin-content">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i data-lucide="check-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                    <div><?php echo htmlspecialchars($success); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i data-lucide="alert-octagon" class="me-2" style="width: 20px; height: 20px;"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshIcons() {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        }
        refreshIcons();
        document.addEventListener('DOMContentLoaded', refreshIcons);
        window.addEventListener('load', refreshIcons);
    </script>

    <?php require ROOT_PATH . '/resources/views/partials/universal_preview_modal.php'; ?>
</body>
</html>
