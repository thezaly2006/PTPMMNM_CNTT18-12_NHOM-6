<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'PDF Workspace'); ?></title>
    <!-- Google Fonts: Plus Jakarta Sans (Headings) & Inter (UI/Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- PDF.js Mozilla -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.468.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="https://unpkg.com/lucide@latest"><\/script>');
        }
    </script>
    <style>
        :root {
            /* Theme Core Palette */
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            
            /* Feature Dedicated Multi-Color Tokens */
            --color-hub: #4f46e5;
            --color-hub-rgb: 79, 70, 229;
            --grad-hub: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            
            --color-merge: #059669;
            --color-merge-rgb: 5, 150, 105;
            --grad-merge: linear-gradient(135deg, #059669 0%, #10b981 50%, #14b8a6 100%);
            
            --color-split: #ea580c;
            --color-split-rgb: 234, 88, 12;
            --grad-split: linear-gradient(135deg, #ea580c 0%, #f97316 50%, #f43f5e 100%);
            
            --color-editor: #7c3aed;
            --color-editor-rgb: 124, 58, 237;
            --grad-editor: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
            
            --color-pricing: #d97706;
            --color-pricing-rgb: 217, 119, 6;
            --grad-pricing: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #fbbf24 100%);
            
            --color-docs: #0284c7;
            --color-docs-rgb: 2, 132, 199;
            --grad-docs: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);

            --sidebar-width: 260px;
            --sidebar-bg: rgba(255, 255, 255, 0.94);
            --card-border: rgba(226, 232, 240, 0.85);
            --radius-card: 18px;
            --radius-btn: 11px;
        }

        /* Modern Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Aurora Ambient Mesh Background */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 45%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.10) 0px, transparent 40%),
                radial-gradient(at 50% 50%, rgba(244, 63, 94, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.08) 0px, transparent 45%),
                radial-gradient(at 0% 100%, rgba(139, 92, 246, 0.10) 0px, transparent 40%);
            background-attachment: fixed;
            color: #0f172a;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6, .font-heading, .sidebar-brand, .modal-title {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            letter-spacing: -0.02em;
        }

        /* Frosted Glass Cards (Bento Style) */
        .card-aurora {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
            border-radius: var(--radius-card);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .card-aurora:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 36px -6px rgba(15, 23, 42, 0.08), 0 8px 16px -4px rgba(15, 23, 42, 0.03);
            border-color: rgba(99, 102, 241, 0.35);
        }

        /* Micro-interactions & Glows */
        .hover-lift {
            transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .hover-lift:hover {
            transform: translateY(-3px);
        }
        .hover-lift:active {
            transform: translateY(0);
        }
        .hover-bg-subtle {
            transition: background-color 0.18s ease;
        }
        .hover-bg-subtle:hover {
            background-color: rgba(241, 245, 249, 0.75) !important;
        }

        /* Sidebar Glass Styling */
        .app-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226, 232, 240, 0.85);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.03);
        }
        .sidebar-brand {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.8) 100%);
            text-decoration: none;
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            flex-shrink: 0;
        }
        .sidebar-brand:hover .brand-icon {
            transform: scale(1.08) rotate(4deg);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.45);
        }
        .sidebar-menu {
            padding: 14px 12px;
            flex-grow: 1;
            overflow-y: auto;
        }
        .menu-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 12px 10px 4px 10px;
            font-weight: 700;
        }
        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            color: #475569;
            text-decoration: none;
            border-radius: 11px;
            font-size: 0.86rem;
            font-weight: 500;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 3px;
            position: relative;
        }
        .nav-link-custom:hover {
            background-color: rgba(241, 245, 249, 0.9);
            color: #0f172a;
            transform: translateX(3px);
        }
        .nav-link-custom.active {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.10) 0%, rgba(124, 58, 237, 0.06) 100%);
            color: #4338ca;
            font-weight: 600;
        }
        .nav-link-custom.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 7px;
            bottom: 7px;
            width: 3.5px;
            background: linear-gradient(180deg, #4f46e5 0%, #ec4899 100%);
            border-radius: 0 4px 4px 0;
        }
        .nav-icon-box {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .nav-link-custom:hover .nav-icon-box {
            transform: scale(1.1);
        }

        /* Sidebar Token Progress Box */
        .sidebar-token-box {
            margin: 0 10px 10px 10px;
            padding: 12px 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.10) 0%, rgba(251, 191, 36, 0.04) 100%);
            border: 1px solid rgba(245, 158, 11, 0.25);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.06);
        }

        .sidebar-footer {
            padding: 12px 12px;
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            background: rgba(255, 255, 255, 0.96);
        }
        .user-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 8px 10px;
            border-radius: 11px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        }

        /* Main Content */
        .app-main {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
            overflow-x: hidden;
        }
        .app-topbar {
            height: 64px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        }
        .app-content {
            padding: 24px 28px 48px 28px;
            flex-grow: 1;
        }

        /* Modern Gradient Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: var(--radius-btn);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.38);
            color: #ffffff;
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        /* Pulsing Dot Animation */
        .pulse-dot {
            width: 7px;
            height: 7px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 1.8s infinite;
        }
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Dropzone */
        .dropzone-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 34px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.22s ease;
        }
        .dropzone-area:hover, .dropzone-area.dragover {
            border-color: #4f46e5;
            background: linear-gradient(180deg, #eef2ff 0%, #ede9fe 100%);
            transform: scale(1.01);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="app-sidebar">
        <a href="/dashboard" class="sidebar-brand">
            <div class="brand-icon">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; letter-spacing: -0.01em;">PDF Workspace</h6>
                    <span class="badge rounded-pill text-white fw-bold" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); font-size: 0.6rem; padding: 2px 5px;">v2.0</span>
                </div>
                <small class="text-muted" style="font-size: 0.72rem;">Không gian xử lý PDF trực tuyến</small>
            </div>
        </a>

        <div class="sidebar-menu">
            <div class="menu-label">Tổng quan</div>
            <a href="/dashboard" class="nav-link-custom <?php echo ($activeNav ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(79, 70, 229, 0.12); color: #4f46e5;">
                    <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Tổng quan</span>
            </a>
            <a href="/documents" class="nav-link-custom <?php echo ($activeNav ?? '') === 'documents' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                    <i data-lucide="folder" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Tài liệu của tôi</span>
            </a>

            <div class="menu-label mt-3">Công cụ xử lý PDF</div>
            <a href="/editor" class="nav-link-custom <?php echo ($activeNav ?? '') === 'editor' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(168, 85, 247, 0.14); color: #9333ea;">
                    <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-grow-1">
                    <span>PDF Editor Pro</span>
                    <span class="badge text-white" style="background: linear-gradient(135deg, #a855f7, #ec4899); font-size: 0.6rem; border-radius: 6px; padding: 2px 6px;">Hot</span>
                </div>
            </a>
            <a href="/merge" class="nav-link-custom <?php echo ($activeNav ?? '') === 'merge' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(16, 185, 129, 0.14); color: #059669;">
                    <i data-lucide="combine" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Ghép PDF</span>
            </a>
            <a href="/split" class="nav-link-custom <?php echo ($activeNav ?? '') === 'split' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(244, 63, 94, 0.14); color: #e11d48;">
                    <i data-lucide="split" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Tách PDF</span>
            </a>

            <div class="menu-label mt-3">Dịch vụ & Lưu trữ</div>
            <a href="/pricing" class="nav-link-custom <?php echo ($activeNav ?? '') === 'pricing' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">
                    <i data-lucide="coins" style="width: 16px; height: 16px;"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-grow-1">
                    <span>Bảng giá & Token</span>
                    <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.6rem; border-radius: 6px; padding: 2px 6px;">VIP</span>
                </div>
            </a>
            <a href="/exports" class="nav-link-custom <?php echo ($activeNav ?? '') === 'exports' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(14, 165, 233, 0.14); color: #0284c7;">
                    <i data-lucide="history" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Lịch sử xuất file</span>
            </a>

            <div class="menu-label mt-3">Hệ thống</div>
            <a href="/tests" class="nav-link-custom <?php echo ($activeNav ?? '') === 'tests' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                    <i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Kiểm thử nghiệm thu</span>
            </a>

            <div id="sidebarAdminMenuArea">
                <?php if (is_admin()): ?>
                    <div class="menu-label mt-3" style="color: #d97706;">Quản trị hệ thống</div>
                    <a href="/admin" class="nav-link-custom" style="background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.4);">
                        <div class="nav-icon-box" style="background: rgba(245, 158, 11, 0.2); color: #d97706;">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-grow-1">
                            <span class="fw-bold" style="color: #b45309;">Cổng Quản Trị</span>
                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.6rem;">ADMIN</span>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Token Widget -->
        <div class="sidebar-token-box">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.74rem;">
                    <i data-lucide="zap" style="width: 13px; height: 13px; color: #f59e0b;"></i>
                    <span>Ví Token & Gói</span>
                </span>
                <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.6rem;">
                    <?php 
                        if (!auth_check()) {
                            echo 'KHÁCH';
                        } elseif (is_admin()) {
                            echo 'ADMIN';
                        } else {
                            echo strtoupper(auth_user()['plan_tier'] ?? 'FREE');
                        }
                    ?>
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-baseline mb-1">
                <span class="fw-bold text-dark" style="font-size: 0.92rem;">
                    <?php 
                        if (!auth_check()) {
                            echo '50 <small class="text-muted fw-normal" style="font-size: 0.7rem;">tokens</small>';
                        } elseif (has_unlimited_tokens()) {
                            echo '∞ Vô hạn';
                        } else {
                            echo number_format((int)(auth_user()['tokens'] ?? 0)) . ' <small class="text-muted fw-normal" style="font-size: 0.7rem;">tokens</small>';
                        }
                    ?>
                </span>
                <a href="/pricing" class="text-primary text-decoration-none fw-bold" style="font-size: 0.71rem;">Nâng cấp &rarr;</a>
            </div>
            <?php if (auth_check() && has_unlimited_tokens() && !empty(auth_user()['plan_expires_at'])): ?>
                <div class="text-muted small mb-2" style="font-size: 0.68rem;">
                    Hạn: 00:00 <?php echo date('d/m/Y', strtotime(auth_user()['plan_expires_at'])); ?> (UTC+7)
                </div>
            <?php else: ?>
                <div class="text-muted small mb-2" style="font-size: 0.68rem;">
                    <?php echo (auth_check() && is_admin()) ? 'Đặc quyền Quản trị viên' : '5 token / lần xuất file'; ?>
                </div>
            <?php endif; ?>
            <div class="progress" style="height: 4px; border-radius: 999px; background: rgba(245, 158, 11, 0.15);">
                <?php 
                    $pct = 100;
                    if (auth_check() && !has_unlimited_tokens()) {
                        $tokens = (int)(auth_user()['tokens'] ?? 0);
                        $pct = min(100, max(5, round(($tokens / 50) * 100)));
                    }
                ?>
                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $pct; ?>%; border-radius: 999px;"></div>
            </div>
        </div>

        <div class="sidebar-footer" id="sidebarFooterArea">
            <?php if (auth_check()): ?>
                <div class="user-pill mb-2">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.84rem; flex-shrink: 0; background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                            <?php echo strtoupper(substr(auth_user()['name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;"><?php echo htmlspecialchars(auth_user()['name'] ?? 'Người dùng'); ?></div>
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;"><?php echo htmlspecialchars(auth_user()['email'] ?? ''); ?></div>
                        </div>
                    </div>
                </div>
                <form action="/logout" method="POST" class="m-0">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1" style="border-radius: 8px; font-size: 0.76rem; padding: 4px 8px;">
                        <i data-lucide="log-out" style="width: 13px; height: 13px;"></i>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            <?php else: ?>
                <div class="user-pill mb-2 border-warning-subtle" style="background: rgba(245, 158, 11, 0.08);">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.84rem; flex-shrink: 0; background: linear-gradient(135deg, #f59e0b, #d97706);">
                            K
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">Khách trải nghiệm</div>
                            <div class="text-warning text-truncate" style="font-size: 0.68rem; font-weight: 600;">Chưa đăng nhập</div>
                        </div>
                    </div>
                </div>
                <a href="/login" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm" style="border-radius: 8px; font-size: 0.78rem; padding: 6px 8px;">
                    <i data-lucide="log-in" style="width: 14px; height: 14px;"></i>
                    <span>Đăng nhập ngay</span>
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
        <!-- Topbar -->
        <header class="app-topbar">
            <?php 
                $cleanTitle = trim(str_ireplace(['- PDF Workspace', 'PDF Workspace'], '', $pageTitle ?? 'Trang chủ'));
                if (empty($cleanTitle)) $cleanTitle = 'Trang chủ';
            ?>
            <div class="d-flex align-items-center gap-2 text-muted small flex-nowrap overflow-hidden">
                <div class="p-1 rounded bg-indigo-50 flex-shrink-0" style="background: #eef2ff;">
                    <i data-lucide="layers" style="width: 15px; height: 15px; color: #4f46e5;"></i>
                </div>
                <span class="text-secondary fw-medium">PDF Workspace</span>
                <span>/</span>
                <span class="text-dark fw-bold text-truncate" style="max-width: 240px;"><?php echo htmlspecialchars($cleanTitle); ?></span>
                <?php if (!auth_check()): ?>
                    <span id="topbarGuestBadge" class="badge bg-warning-subtle text-dark border border-warning-subtle ms-1 d-none d-lg-inline-flex align-items-center gap-1 flex-shrink-0" style="font-size: 0.68rem;">
                        <i data-lucide="sparkles" style="width: 11px; height: 11px; color: #d97706;"></i>
                        Khách trải nghiệm
                    </span>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center gap-2 flex-nowrap flex-shrink-0">
                <div id="topbarAdminBtnArea">
                    <?php if (is_admin()): ?>
                        <a href="/admin" class="btn btn-warning btn-sm d-flex align-items-center gap-1 shadow-sm px-2 text-dark fw-semibold" style="border-radius: 8px; font-size: 0.78rem;">
                            <i data-lucide="shield" style="width: 13px; height: 13px;"></i>
                            <span>Admin</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div id="topbarLoginBtnArea">
                    <?php if (!auth_check()): ?>
                        <a href="/login" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 px-3 shadow-sm rounded-pill" style="border-radius: 20px; font-size: 0.78rem;">
                            <i data-lucide="log-in" style="width: 13px; height: 13px;"></i>
                            <span>Đăng nhập</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Token Balance Pill -->
                <a href="/pricing" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 text-dark rounded-pill py-1 px-2" id="topbarTokenPill" style="font-size: 0.76rem; text-decoration: none;" title="Xem chi tiết Token & Nâng cấp gói">
                    <i data-lucide="coins" style="width: 14px; height: 14px; color: #f59e0b;"></i>
                    <span id="topbarTokensCount" class="fw-bold">
                        <?php 
                            if (!auth_check()) {
                                echo '50';
                            } elseif (has_unlimited_tokens()) {
                                echo '∞ Vô hạn';
                            } else {
                                echo number_format((int)(auth_user()['tokens'] ?? 0));
                            }
                        ?>
                    </span>
                    <span class="badge bg-warning text-dark border border-warning rounded-pill" style="font-size: 0.6rem; padding: 1px 5px;">
                        + Nạp
                    </span>
                </a>

                <div class="d-none d-xl-flex align-items-center gap-1 px-2 py-1 bg-white border rounded-pill shadow-2xs small text-muted">
                    <span class="pulse-dot"></span>
                    <span style="font-size: 0.74rem; font-weight: 500;">Sẵn sàng</span>
                </div>

                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal" style="font-size: 0.78rem;">
                    <i data-lucide="upload-cloud" style="width: 14px; height: 14px;"></i>
                    <span>Tải PDF lên</span>
                </button>
            </div>
        </header>

        <!-- Main Body -->
        <div class="app-content">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-left: 4px solid #10b981 !important;">
                    <i data-lucide="check-circle" class="me-2 text-success" style="width: 20px; height: 20px;"></i>
                    <div class="text-success fw-medium"><?php echo htmlspecialchars($success); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); border-left: 4px solid #f43f5e !important;">
                    <i data-lucide="alert-octagon" class="me-2 text-danger" style="width: 20px; height: 20px;"></i>
                    <div class="text-danger fw-medium"><?php echo htmlspecialchars($error); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Modal Tải lên PDF Đa luồng (Hỗ trợ nhiều tệp cùng lúc) -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; backdrop-filter: blur(20px);">
                <div class="modal-header border-bottom py-3 px-4" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                            <i data-lucide="upload-cloud" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0" id="uploadModalLabel">Tải lên nhiều tài liệu PDF cùng lúc</h6>
                            <small class="text-muted">Chọn hoặc kéo thả nhiều tệp PDF để đưa vào không gian làm việc</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Khu vực kéo thả / duyệt file -->
                    <div class="dropzone-area" id="dropzone" onclick="document.getElementById('fileInput').click()">
                        <i data-lucide="file-up" style="width: 44px; height: 44px; color: #4f46e5; stroke-width: 1.5;" class="mb-2"></i>
                        <h6 class="fw-semibold mb-1">Kéo thả các tệp PDF vào đây, hoặc <span class="text-primary text-decoration-underline">chọn từ máy tính</span></h6>
                        <p class="text-muted small mb-1">Bạn có thể chọn <strong>nhiều tệp cùng lúc</strong> (giữ phím <code>Ctrl</code> hoặc <code>Shift</code> khi chọn) hoặc bấm chọn nhiều lần liên tiếp.</p>
                        <span class="badge bg-light text-secondary border">Hỗ trợ tối đa 50MB/tệp &bull; Đuôi .pdf</span>
                        <input type="file" id="fileInput" name="files[]" multiple accept=".pdf,application/pdf" style="display: none;">
                    </div>

                    <!-- Danh sách các tệp đã chọn sẵn sàng tải lên -->
                    <div id="selectedFilesContainer" class="mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-dark d-flex align-items-center gap-1">
                                <i data-lucide="list" style="width: 15px; height: 15px; color: #4f46e5;"></i>
                                <span>Danh sách tệp chờ tải lên:</span>
                                <span class="badge bg-primary px-2" id="selectedCountBadge">0 tệp</span>
                                <span class="text-muted ms-1 small" id="selectedTotalSizeText">(0 MB)</span>
                            </span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 d-flex align-items-center gap-1" style="font-size: 0.78rem;" onclick="document.getElementById('fileInput').click()">
                                    <i data-lucide="plus" style="width: 12px; height: 12px;"></i>
                                    <span>Chọn thêm tệp</span>
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size: 0.78rem;" onclick="clearAllSelectedFiles()">
                                    Xóa hết
                                </button>
                            </div>
                        </div>

                        <div id="selectedFilesList" class="border rounded-3 p-2 bg-light-subtle" style="max-height: 220px; overflow-y: auto;">
                            <!-- Danh sách các tệp chi tiết -->
                        </div>
                    </div>

                    <!-- Tiến trình tải lên -->
                    <div id="uploadProgressContainer" class="mt-3" style="display: none;">
                        <div class="progress mb-2" style="height: 10px; border-radius: 6px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span id="uploadStatusText" class="fw-medium text-dark">Đang tải lên...</span>
                            <span id="uploadPercent" class="fw-bold text-primary">0%</span>
                        </div>
                    </div>

                    <!-- Thông báo kết quả sau khi upload xong -->
                    <div id="uploadResultAlert" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer border-top bg-light d-flex justify-content-between py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-4 rounded-pill" id="btnStartUpload" disabled onclick="startUpload()">
                        <i data-lucide="upload" style="width: 15px; height: 15px;"></i>
                        <span id="btnUploadText">Tải lên ngay</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

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

        // Xử lý Upload PDF Đa tệp
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const btnStartUpload = document.getElementById('btnStartUpload');
        const btnUploadText = document.getElementById('btnUploadText');
        const uploadProgressContainer = document.getElementById('uploadProgressContainer');
        const uploadProgressBar = document.getElementById('uploadProgressBar');
        const uploadStatusText = document.getElementById('uploadStatusText');
        const uploadPercent = document.getElementById('uploadPercent');
        const uploadResultAlert = document.getElementById('uploadResultAlert');

        let selectedFiles = [];

        if (dropzone && fileInput) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    addFilesToQueue(Array.from(e.dataTransfer.files));
                }
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files.length > 0) {
                    addFilesToQueue(Array.from(e.target.files));
                }
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function addFilesToQueue(newFiles) {
            for (let f of newFiles) {
                if (!f.name.toLowerCase().endsWith('.pdf') && f.type !== 'application/pdf') {
                    continue;
                }
                const exists = selectedFiles.some(item => item.name === f.name && item.size === f.size);
                if (!exists) {
                    selectedFiles.push(f);
                }
            }
            if (fileInput) fileInput.value = '';
            renderSelectedFiles();
        }

        function removeFileFromQueue(index) {
            selectedFiles.splice(index, 1);
            renderSelectedFiles();
        }

        function clearAllSelectedFiles() {
            selectedFiles = [];
            if (fileInput) fileInput.value = '';
            renderSelectedFiles();
        }

        function renderSelectedFiles() {
            const container = document.getElementById('selectedFilesContainer');
            const list = document.getElementById('selectedFilesList');
            const countBadge = document.getElementById('selectedCountBadge');
            const totalSizeText = document.getElementById('selectedTotalSizeText');

            if (!container || !list || !btnStartUpload || !btnUploadText) return;

            if (selectedFiles.length === 0) {
                container.style.display = 'none';
                btnStartUpload.disabled = true;
                btnUploadText.textContent = 'Tải lên ngay';
                return;
            }

            container.style.display = 'block';
            btnStartUpload.disabled = false;
            countBadge.textContent = `${selectedFiles.length} tệp`;

            const totalBytes = selectedFiles.reduce((acc, f) => acc + f.size, 0);
            totalSizeText.textContent = `(${formatFileSize(totalBytes)})`;
            btnUploadText.textContent = `Tải lên ${selectedFiles.length} tệp (${formatFileSize(totalBytes)})`;

            list.innerHTML = '';
            selectedFiles.forEach((f, idx) => {
                const item = document.createElement('div');
                item.className = 'd-flex justify-content-between align-items-center py-2 px-3 border-bottom bg-white rounded-2 mb-1';
                item.id = `file_item_${idx}`;
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                        <i data-lucide="file-text" style="width: 16px; height: 16px; color: #4f46e5; flex-shrink: 0;"></i>
                        <div class="text-truncate">
                            <span class="fw-semibold text-dark small text-truncate d-inline-block" style="max-width: 380px;" title="${f.name}">
                                ${f.name}
                            </span>
                            <span class="text-muted ms-1" style="font-size: 0.75rem;">${formatFileSize(f.size)}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="badge bg-light text-secondary border small" id="file_status_${idx}" style="font-size: 0.72rem;">Sẵn sàng</span>
                        <button type="button" class="btn btn-xs btn-link text-danger p-0" title="Bỏ tệp này" onclick="removeFileFromQueue(${idx})">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });

            refreshIcons();
        }

        async function startUpload() {
            if (selectedFiles.length === 0) return;

            btnStartUpload.disabled = true;
            uploadProgressContainer.style.display = 'block';
            uploadResultAlert.style.display = 'none';

            const total = selectedFiles.length;
            let successCount = 0;
            let failCount = 0;
            let results = [];

            for (let i = 0; i < total; i++) {
                const f = selectedFiles[i];
                const statusBadge = document.getElementById(`file_status_${i}`);

                if (statusBadge) {
                    statusBadge.className = 'badge bg-primary text-white small';
                    statusBadge.innerHTML = `<span class="spinner-border spinner-border-sm me-1" style="width: 9px; height: 9px;"></span>Đang tải...`;
                }

                uploadStatusText.textContent = `Đang xử lý (${i + 1}/${total}): ${f.name}...`;
                const progressPct = Math.round(((i) / total) * 100);
                uploadProgressBar.style.width = progressPct + '%';
                uploadPercent.textContent = progressPct + '%';

                const formData = new FormData();
                formData.append('csrf_token', '<?php echo csrf_token(); ?>');
                formData.append('files[]', f);

                try {
                    const resp = await fetch('/documents/upload', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    const data = await resp.json();
                    if (data.results && data.results.length > 0) {
                        const r = data.results[0];
                        results.push(r);
                        if (r.success) {
                            successCount++;
                            if (statusBadge) {
                                statusBadge.className = 'badge bg-success text-white small';
                                statusBadge.innerHTML = `✓ ${r.pages} trang`;
                            }
                        } else {
                            failCount++;
                            if (statusBadge) {
                                statusBadge.className = 'badge bg-danger text-white small';
                                statusBadge.innerHTML = `✕ Thất bại`;
                                statusBadge.title = r.error;
                            }
                        }
                    } else {
                        failCount++;
                        results.push({ success: false, filename: f.name, error: data.message || 'Lỗi không xác định.' });
                        if (statusBadge) {
                            statusBadge.className = 'badge bg-danger text-white small';
                            statusBadge.textContent = '✕ Lỗi';
                        }
                    }
                } catch (err) {
                    failCount++;
                    results.push({ success: false, filename: f.name, error: err.message || 'Mất kết nối máy chủ.' });
                    if (statusBadge) {
                        statusBadge.className = 'badge bg-danger text-white small';
                        statusBadge.textContent = '✕ Lỗi mạng';
                    }
                }
            }

            uploadProgressBar.style.width = '100%';
            uploadPercent.textContent = '100%';
            uploadStatusText.textContent = 'Hoàn tất quá trình tải lên!';

            setTimeout(() => {
                uploadProgressContainer.style.display = 'none';
                uploadResultAlert.style.display = 'block';

                if (failCount === 0) {
                    uploadResultAlert.className = 'alert alert-success small mb-0 rounded-3';
                    uploadResultAlert.innerHTML = `<strong>Thành công xuất sắc!</strong> Đã tải lên toàn bộ ${successCount} tệp PDF vào không gian làm việc.`;
                    selectedFiles = [];
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                    let summaryHtml = `<div class="fw-semibold">Kết quả tải lên: ${successCount} thành công, ${failCount} thất bại:</div><ul class="mb-0 mt-1 ps-3">`;
                    results.forEach(r => {
                        if (r.success) {
                            summaryHtml += `<li class="text-success"><strong>${r.filename}:</strong> Đã thêm (${r.pages} trang)</li>`;
                        } else {
                            summaryHtml += `<li class="text-danger"><strong>${r.filename}:</strong> ${r.error}</li>`;
                        }
                    });
                    summaryHtml += `</ul>`;
                    uploadResultAlert.className = 'alert alert-warning small mb-0 rounded-3';
                    uploadResultAlert.innerHTML = summaryHtml;

                    if (successCount > 0) {
                        setTimeout(() => { location.reload(); }, 3500);
                    } else {
                        btnStartUpload.disabled = false;
                        btnUploadText.textContent = 'Thử lại';
                    }
                }
            }, 500);
        }
    </script>

    <script>
        window.IS_LOGGED_IN = <?php echo auth_check() ? 'true' : 'false'; ?>;
        window.CURRENT_USER = <?php echo json_encode(auth_user() ?: null); ?>;

        window.syncAuthUserUI = function(user, csrfToken) {
            if (!user) return;
            window.IS_LOGGED_IN = true;
            window.CURRENT_USER = user;

            const guestBadge = document.getElementById('topbarGuestBadge');
            if (guestBadge) guestBadge.remove();

            const topbarLoginArea = document.getElementById('topbarLoginBtnArea');
            if (topbarLoginArea) topbarLoginArea.innerHTML = '';

            if (user.role === 'admin') {
                const topbarAdminArea = document.getElementById('topbarAdminBtnArea');
                if (topbarAdminArea) {
                    topbarAdminArea.innerHTML = `
                        <a href="/admin" class="btn btn-warning btn-sm d-flex align-items-center gap-1 shadow-sm px-2 text-dark fw-semibold" style="border-radius: 8px; font-size: 0.78rem;">
                            <i data-lucide="shield" style="width: 13px; height: 13px;"></i>
                            <span>Admin</span>
                        </a>
                    `;
                }

                const sidebarAdminArea = document.getElementById('sidebarAdminMenuArea');
                if (sidebarAdminArea) {
                    sidebarAdminArea.innerHTML = `
                        <div class="menu-label mt-3" style="color: #d97706;">Quản trị hệ thống</div>
                        <a href="/admin" class="nav-link-custom" style="background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.4);">
                            <div class="nav-icon-box" style="background: rgba(245, 158, 11, 0.2); color: #d97706;">
                                <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-grow-1">
                                <span class="fw-bold" style="color: #b45309;">Cổng Quản Trị</span>
                                <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.6rem;">ADMIN</span>
                            </div>
                        </a>
                    `;
                }
            }

            const sidebarFooter = document.getElementById('sidebarFooterArea');
            if (sidebarFooter) {
                const firstLetter = (user.name || 'U').charAt(0).toUpperCase();
                const token = csrfToken || '<?php echo csrf_token(); ?>';
                sidebarFooter.innerHTML = `
                    <div class="user-pill mb-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.84rem; flex-shrink: 0; background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                                ${firstLetter}
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">${escapeHtml(user.name || 'Người dùng')}</div>
                                <div class="text-muted text-truncate" style="font-size: 0.68rem;">${escapeHtml(user.email || '')}</div>
                            </div>
                        </div>
                    </div>
                    <form action="/logout" method="POST" class="m-0">
                        <input type="hidden" name="csrf_token" value="${token}">
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1" style="border-radius: 8px; font-size: 0.76rem; padding: 4px 8px;">
                            <i data-lucide="log-out" style="width: 13px; height: 13px;"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                `;
            }

            if (csrfToken) {
                document.querySelectorAll('input[name="csrf_token"]').forEach(el => el.value = csrfToken);
                const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                if (metaCsrf) metaCsrf.setAttribute('content', csrfToken);
            }

            const tokensCount = document.getElementById('topbarTokensCount');
            if (tokensCount) {
                if (user.role === 'admin' || user.has_unlimited || ['plus', 'pro', 'premium'].includes(user.plan_tier)) {
                    tokensCount.textContent = '∞ Vô hạn';
                } else if (user.tokens !== undefined) {
                    tokensCount.textContent = Number(user.tokens).toLocaleString();
                }
            }

            if (window.lucide && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }

            window.dispatchEvent(new CustomEvent('auth:updated', { detail: { user, csrfToken } }));
        };

        window.updateUserTokensBadge = function(newTokens, planTier) {
            const tokensCount = document.getElementById('topbarTokensCount');
            if (tokensCount) {
                if (planTier === 'admin' || ['plus', 'pro', 'premium'].includes(planTier)) {
                    tokensCount.textContent = '∞ Vô hạn';
                } else if (newTokens !== undefined) {
                    tokensCount.textContent = (newTokens === 'unlimited') ? '∞ Vô hạn' : Number(newTokens).toLocaleString();
                }
            }
        };

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <?php require ROOT_PATH . '/resources/views/partials/universal_preview_modal.php'; ?>
    <?php require ROOT_PATH . '/resources/views/partials/page_zoom_modal.php'; ?>
    <?php require ROOT_PATH . '/resources/views/partials/require_login_modal.php'; ?>
    <?php require ROOT_PATH . '/resources/views/partials/out_of_tokens_modal.php'; ?>
</body>
</html>
