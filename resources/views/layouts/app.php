<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'PDF Workspace'); ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            --sidebar-width: 270px;
            --sidebar-bg: #ffffff;
            --main-bg: #f8fafc;
            --card-border: #e2e8f0;
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

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.05), transparent 45%),
                        radial-gradient(circle at 90% 85%, rgba(236, 72, 153, 0.04), transparent 40%),
                        #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .app-sidebar {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226, 232, 240, 0.9);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.02);
        }
        .sidebar-brand {
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.8) 0%, rgba(255, 255, 255, 0.4) 100%);
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #ec4899 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            transition: transform 0.25s ease;
        }
        .sidebar-brand:hover .brand-icon {
            transform: scale(1.08) rotate(3deg);
        }
        .sidebar-menu {
            padding: 16px 14px;
            flex-grow: 1;
            overflow-y: auto;
        }
        .menu-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 10px 12px 4px 12px;
            font-weight: 700;
        }
        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            color: #475569;
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 3px;
            position: relative;
        }
        .nav-link-custom:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            transform: translateX(3px);
        }
        .nav-link-custom.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(139, 92, 246, 0.08) 100%);
            color: #4f46e5;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
        }
        .nav-link-custom.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 3.5px;
            background: linear-gradient(180deg, #6366f1 0%, #ec4899 100%);
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
            transition: all 0.2s ease;
        }
        .nav-link-custom:hover .nav-icon-box {
            transform: scale(1.1);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            background: #ffffff;
        }
        .user-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        /* Main Content */
        .app-main {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }
        .app-topbar {
            height: 68px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 36px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.02);
        }
        .app-content {
            padding: 32px 36px;
            flex-grow: 1;
        }

        /* Modern Gradient Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 9px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.38);
            color: #ffffff;
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        /* Modern Cards */
        .card-custom {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-custom:hover {
            box-shadow: 0 12px 28px -4px rgba(99, 102, 241, 0.08), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* Pulsing Dot Animation */
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
            border-radius: 14px;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 36px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .dropzone-area:hover, .dropzone-area.dragover {
            border-color: #6366f1;
            background: linear-gradient(180deg, #eef2ff 0%, #ede9fe 100%);
            transform: scale(1.01);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">PDF Workspace</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Xử lý PDF trực tuyến</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-label">Tổng quát</div>
            <a href="/dashboard" class="nav-link-custom <?php echo ($activeNav ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                    <i data-lucide="layout-dashboard" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Tổng quan</span>
            </a>
            <a href="/documents" class="nav-link-custom <?php echo ($activeNav ?? '') === 'documents' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                    <i data-lucide="folder" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Tài liệu của tôi</span>
            </a>

            <div class="menu-label mt-3">Công cụ xử lý PDF</div>
            <a href="/editor" class="nav-link-custom <?php echo ($activeNav ?? '') === 'editor' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(168, 85, 247, 0.12); color: #9333ea;">
                    <i data-lucide="sparkles" style="width: 17px; height: 17px;"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-grow-1">
                    <span>PDF Editor</span>
                    <span class="badge bg-gradient text-white" style="background: linear-gradient(135deg, #a855f7, #ec4899); font-size: 0.65rem; border-radius: 6px; padding: 2px 6px;">Hot</span>
                </div>
            </a>
            <a href="/merge" class="nav-link-custom <?php echo ($activeNav ?? '') === 'merge' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                    <i data-lucide="combine" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Ghép PDF</span>
            </a>
            <a href="/split" class="nav-link-custom <?php echo ($activeNav ?? '') === 'split' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                    <i data-lucide="split" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Tách PDF</span>
            </a>

            <div class="menu-label mt-3">Lưu trữ & Lịch sử</div>
            <a href="/exports" class="nav-link-custom <?php echo ($activeNav ?? '') === 'exports' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(244, 63, 94, 0.12); color: #e11d48;">
                    <i data-lucide="history" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Lịch sử xuất file</span>
            </a>

            <div class="menu-label mt-3">Kiểm thử & Đánh giá</div>
            <a href="/tests" class="nav-link-custom <?php echo ($activeNav ?? '') === 'tests' ? 'active' : ''; ?>">
                <div class="nav-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                    <i data-lucide="check-circle-2" style="width: 17px; height: 17px;"></i>
                </div>
                <span>Kiểm thử nghiệm thu</span>
            </a>

            <?php if (is_admin()): ?>
                <div class="menu-label mt-3" style="color: #d97706;">Khu vực Quản trị</div>
                <a href="/admin" class="nav-link-custom" style="background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.4);">
                    <div class="nav-icon-box" style="background: rgba(245, 158, 11, 0.2); color: #d97706;">
                        <i data-lucide="shield-alert" style="width: 17px; height: 17px;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-grow-1">
                        <span class="fw-bold" style="color: #b45309;">Cổng Quản Trị</span>
                        <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.65rem;">ADMIN</span>
                    </div>
                </a>
            <?php endif; ?>
        </div>

        <div class="sidebar-footer">
            <div class="user-pill mb-2">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.85rem; flex-shrink: 0; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <?php echo strtoupper(substr(auth_user()['name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="text-truncate">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;"><?php echo htmlspecialchars(auth_user()['name'] ?? 'Người dùng'); ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem;"><?php echo htmlspecialchars(auth_user()['email'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
            <form action="/logout" method="POST" class="m-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px;">
                    <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
        <!-- Topbar -->
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-2 text-muted small">
                <div class="p-1 rounded bg-indigo-50" style="background: #eef2ff;">
                    <i data-lucide="layers" style="width: 16px; height: 16px; color: #6366f1;"></i>
                </div>
                <span class="text-secondary">PDF Workspace</span>
                <span>/</span>
                <span class="text-dark fw-bold"><?php echo htmlspecialchars($pageTitle ?? 'Trang chủ'); ?></span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <?php if (is_admin()): ?>
                    <a href="/admin" class="btn btn-warning btn-sm d-flex align-items-center gap-1 shadow-sm px-3 text-dark fw-semibold me-1" style="border-radius: 8px;">
                        <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                        <span>Cổng Quản Trị</span>
                    </a>
                <?php endif; ?>
                <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-white border rounded-pill shadow-sm small text-muted">
                    <span class="pulse-dot"></span>
                    <span style="font-size: 0.78rem; font-weight: 500;">Hệ thống sẵn sàng</span>
                </div>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 shadow" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i>
                    <span>Tải PDF lên</span>
                </button>
            </div>
        </header>

        <!-- Main Body -->
        <div class="app-content">
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

    <!-- Modal Tải lên PDF Đa luồng (Hỗ trợ nhiều tệp cùng lúc) -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded bg-indigo-50 text-primary" style="background-color: #eef2ff;">
                            <i data-lucide="upload-cloud" style="width: 20px; height: 20px; color: #6366f1;"></i>
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
                        <i data-lucide="file-up" style="width: 44px; height: 44px; color: #6366f1; stroke-width: 1.5;" class="mb-2"></i>
                        <h6 class="fw-semibold mb-1">Kéo thả các tệp PDF vào đây, hoặc <span class="text-primary text-decoration-underline">chọn từ máy tính</span></h6>
                        <p class="text-muted small mb-1">Bạn có thể chọn <strong>nhiều tệp cùng lúc</strong> (giữ phím <code>Ctrl</code> hoặc <code>Shift</code> khi chọn) hoặc bấm chọn nhiều lần liên tiếp.</p>
                        <span class="badge bg-light text-secondary border">Hỗ trợ tối đa 50MB/tệp &bull; Đuôi .pdf</span>
                        <input type="file" id="fileInput" name="files[]" multiple accept=".pdf,application/pdf" style="display: none;">
                    </div>

                    <!-- Danh sách các tệp đã chọn sẵn sàng tải lên -->
                    <div id="selectedFilesContainer" class="mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-dark d-flex align-items-center gap-1">
                                <i data-lucide="list" style="width: 15px; height: 15px; color: #6366f1;"></i>
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
                <div class="modal-footer border-top bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3" id="btnStartUpload" disabled onclick="startUpload()">
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

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Thêm dồn tệp mới vào hàng đợi (không ghi đè tệp đã chọn trước đó)
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
            fileInput.value = ''; // Reset input để có thể chọn lại
            renderSelectedFiles();
        }

        function removeFileFromQueue(index) {
            selectedFiles.splice(index, 1);
            renderSelectedFiles();
        }

        function clearAllSelectedFiles() {
            selectedFiles = [];
            fileInput.value = '';
            renderSelectedFiles();
        }

        function renderSelectedFiles() {
            const container = document.getElementById('selectedFilesContainer');
            const list = document.getElementById('selectedFilesList');
            const countBadge = document.getElementById('selectedCountBadge');
            const totalSizeText = document.getElementById('selectedTotalSizeText');

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
                        <i data-lucide="file-text" style="width: 16px; height: 16px; color: #6366f1; flex-shrink: 0;"></i>
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

        // Tải lên thông minh từng tệp một cách tuần tự (tránh lỗi post_max_size quá tải và hiện tiến trình chi tiết)
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
                    uploadResultAlert.className = 'alert alert-success small mb-0';
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
                    uploadResultAlert.className = 'alert alert-warning small mb-0';
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

    <?php require ROOT_PATH . '/resources/views/partials/universal_preview_modal.php'; ?>
    <?php require ROOT_PATH . '/resources/views/partials/page_zoom_modal.php'; ?>
</body>
</html>
