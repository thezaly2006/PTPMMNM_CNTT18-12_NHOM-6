<?php ob_start(); ?>

<!-- Hero Admin Welcome Banner -->
<div class="p-4 p-md-5 rounded-4 mb-4 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 75%, #b45309 100%);">
    <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: rgba(245, 158, 11, 0.15); border-radius: 50%; filter: blur(35px); pointer-events: none;"></div>
    <div style="position: absolute; right: 140px; bottom: -50px; width: 180px; height: 180px; background: rgba(99, 102, 241, 0.20); border-radius: 50%; filter: blur(30px); pointer-events: none;"></div>

    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold rounded-pill shadow-sm" style="font-size: 0.78rem;">
                <i data-lucide="shield-check" style="width: 13px; height: 13px;" class="me-1"></i> Bảng Điều Khiển Quản Trị Hệ Thống
            </span>
            <h2 class="fw-bold mb-2">Đề tài 12: Quản lý Công cụ Xử lý PDF Trực tuyến</h2>
            <p class="mb-3 text-white-50" style="font-size: 0.95rem; max-width: 680px;">
                Theo dõi toàn diện tài khoản người dùng, kho tài liệu nguồn, lịch sử xuất file, giám sát bản chụp bất biến (Snapshot) và quản lý tài nguyên lưu trữ của máy chủ.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="/admin/users" class="btn btn-warning text-dark fw-semibold btn-sm px-3 d-flex align-items-center gap-2 shadow-sm rounded-3">
                    <i data-lucide="users" style="width: 16px; height: 16px;"></i>
                    <span>Quản lý Người dùng</span>
                </a>
                <a href="/admin/subscriptions" class="btn btn-outline-warning fw-semibold btn-sm px-3 d-flex align-items-center gap-2 rounded-3 text-white" style="border-color: #f59e0b;">
                    <i data-lucide="credit-card" style="width: 16px; height: 16px; color: #f59e0b;"></i>
                    <span>Đăng ký Gói & Doanh thu</span>
                </a>
                <a href="/admin/documents" class="btn btn-outline-light fw-medium btn-sm px-3 d-flex align-items-center gap-2 rounded-3" style="border-color: rgba(255,255,255,0.4);">
                    <i data-lucide="folder-archive" style="width: 16px; height: 16px;"></i>
                    <span>Kho tài liệu hệ thống</span>
                </a>
                <a href="/admin/system" class="btn btn-outline-light fw-medium btn-sm px-3 d-flex align-items-center gap-2 rounded-3" style="border-color: rgba(255,255,255,0.4);">
                    <i data-lucide="server" style="width: 16px; height: 16px;"></i>
                    <span>Bảo trì bộ nhớ</span>
                </a>
            </div>
        </div>
        <div class="col-lg-4 d-none d-lg-flex justify-content-end">
            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-20 backdrop-blur" style="backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 bg-warning text-dark">
                        <i data-lucide="cpu" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold small text-white">PHP <?php echo PHP_VERSION; ?> &bull; MariaDB</div>
                        <div class="text-white-50" style="font-size: 0.75rem;">Múi giờ: <?php echo date_default_timezone_get(); ?> (UTC+7)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4 Thẻ Thống kê Toàn hệ thống -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #3b82f6, #06b6d4);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase">Người dùng</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #3b82f6, #06b6d4);">
                    <i data-lucide="users" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_users']); ?></h2>
                <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.72rem;"><?php echo $stats['total_admins']; ?> Admin</span>
            </div>
            <small class="text-muted">Tổng số tài khoản đã đăng ký</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #8b5cf6, #ec4899);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase">Tài liệu nguồn</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #ec4899);">
                    <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_docs']); ?></h2>
                <span class="badge bg-purple-subtle text-purple border" style="font-size: 0.72rem;"><?php echo format_bytes($stats['total_source_size']); ?></span>
            </div>
            <small class="text-muted">Tệp PDF lưu trong kho lưu trữ</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase">Bản xuất PDF</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_jobs']); ?></h2>
                <span class="badge bg-success-subtle text-success border" style="font-size: 0.72rem;"><?php echo $stats['completed_jobs']; ?> Thành công</span>
            </div>
            <small class="text-muted">Các tác vụ ghép, tách & editor</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase">Bộ nhớ Đĩa</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i data-lucide="hard-drive" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo format_bytes($storageDetails['total_size']); ?></h2>
                <span class="badge bg-warning-subtle text-warning border" style="font-size: 0.72rem;">Đang dùng</span>
            </div>
            <small class="text-muted">Sources + Outputs + Temp + Logs</small>
        </div>
    </div>
</div>

<!-- Banner Doanh thu & Gói Đăng ký -->
<div class="card card-custom p-4 p-md-5 mb-4 border-0 text-white position-relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #064e3b 0%, #047857 35%, #0f766e 70%, #0e7490 100%); border-radius: 20px;">
    <!-- Ambient Glow Orbs -->
    <div style="position: absolute; right: -30px; top: -30px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, transparent 70%); border-radius: 50%; filter: blur(35px); pointer-events: none;"></div>
    <div style="position: absolute; left: 35%; bottom: -40px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(6, 182, 212, 0.3) 0%, transparent 70%); border-radius: 50%; filter: blur(30px); pointer-events: none;"></div>

    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge text-white px-3 py-1 rounded-pill fw-bold" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(8px); font-size: 0.72rem; letter-spacing: 0.04em;">
                    <i data-lucide="sparkles" style="width: 12px; height: 12px; color: #fde047;" class="me-1"></i>
                    DOANH THU & GÓI CƯỚC &bull; THỜI GIAN THỰC
                </span>
                <span class="text-white small fw-medium" style="opacity: 0.85;">Hạn mốc 00:00 (UTC+7)</span>
            </div>
            <h3 class="fw-bold mb-2 text-white" style="letter-spacing: -0.02em;">
                Tổng doanh thu hệ thống: <span class="text-warning fw-extrabold"><?php echo number_format($subStats['total_revenue'] ?? 0); ?>đ</span>
            </h3>
            <p class="text-white small mb-0" style="max-width: 650px; opacity: 0.92; line-height: 1.6;">
                Đã ghi nhận thành công <strong class="text-white"><?php echo number_format($subStats['total_subscriptions'] ?? 0); ?> đơn đăng ký</strong> (các gói Plus, Pro, Premium) &bull; Đã cấp <strong class="text-warning"><?php echo number_format($subStats['total_tokens_granted'] ?? 0); ?> Tokens</strong> hoạt động theo mốc giờ chuẩn.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="/admin/subscriptions" class="btn btn-light text-dark fw-bold btn-sm px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2 hover-lift" style="font-size: 0.86rem;">
                <i data-lucide="credit-card" style="width: 16px; height: 16px; color: #047857;"></i>
                <span>Quản lý Đăng ký Gói</span>
                <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
            </a>
        </div>
    </div>
</div>

<!-- Row 2: Phân tích Tác vụ & Môi trường Máy chủ -->
<div class="row g-4 mb-4">
    <!-- Phân bố Tác vụ Xuất PDF -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 rounded-4 shadow-sm border border-light-subtle">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                    <div class="p-1 rounded-2 text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <i data-lucide="pie-chart" style="width: 16px; height: 16px;"></i>
                    </div>
                    <span>Phân bố Tác vụ Xuất PDF</span>
                </h6>
                <span class="badge bg-light text-secondary border rounded-pill px-2" style="font-size: 0.7rem;">
                    <?php echo $stats['total_jobs']; ?> tổng tác vụ
                </span>
            </div>

            <?php 
            $totalJobs = max(1, $stats['total_jobs']);
            $mergePct = round(($stats['merge_jobs'] / $totalJobs) * 100);
            $splitPct = round(($stats['split_jobs'] / $totalJobs) * 100);
            $editorPct = round(($stats['editor_jobs'] / $totalJobs) * 100);
            ?>

            <!-- Multi-colored Segmented Progress Bar -->
            <div class="progress mb-3 shadow-2xs" style="height: 12px; border-radius: 999px; background: rgba(226, 232, 240, 0.6); overflow: hidden;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $mergePct; ?>%; background: linear-gradient(90deg, #059669, #10b981);" title="Ghép PDF: <?php echo $mergePct; ?>%"></div>
                <div class="progress-bar" role="progressbar" style="width: <?php echo $splitPct; ?>%; background: linear-gradient(90deg, #ea580c, #f43f5e);" title="Tách PDF: <?php echo $splitPct; ?>%"></div>
                <div class="progress-bar" role="progressbar" style="width: <?php echo $editorPct; ?>%; background: linear-gradient(90deg, #7c3aed, #ec4899);" title="PDF Editor: <?php echo $editorPct; ?>%"></div>
            </div>

            <div class="row text-center g-2 pt-1 mb-3">
                <!-- Ghép PDF (Emerald) -->
                <div class="col-4">
                    <div class="p-3 rounded-3 h-100 transition-all hover-lift" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
                        <div class="d-flex align-items-center justify-content-center gap-1 text-muted small mb-1" style="font-size: 0.72rem;">
                            <i data-lucide="combine" style="width: 12px; height: 12px; color: #059669;"></i>
                            <span>Ghép PDF</span>
                        </div>
                        <div class="fw-bold fs-4 mb-0" style="color: #059669;"><?php echo $stats['merge_jobs']; ?></div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.2); color: #047857; font-size: 0.68rem;"><?php echo $mergePct; ?>%</span>
                    </div>
                </div>
                <!-- Tách PDF (Coral Sunset) -->
                <div class="col-4">
                    <div class="p-3 rounded-3 h-100 transition-all hover-lift" style="background: rgba(244, 63, 94, 0.08); border: 1px solid rgba(244, 63, 94, 0.25);">
                        <div class="d-flex align-items-center justify-content-center gap-1 text-muted small mb-1" style="font-size: 0.72rem;">
                            <i data-lucide="split" style="width: 12px; height: 12px; color: #ea580c;"></i>
                            <span>Tách PDF</span>
                        </div>
                        <div class="fw-bold fs-4 mb-0" style="color: #ea580c;"><?php echo $stats['split_jobs']; ?></div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(244, 63, 94, 0.2); color: #be123c; font-size: 0.68rem;"><?php echo $splitPct; ?>%</span>
                    </div>
                </div>
                <!-- PDF Editor (Violet Pink) -->
                <div class="col-4">
                    <div class="p-3 rounded-3 h-100 transition-all hover-lift" style="background: rgba(124, 58, 237, 0.08); border: 1px solid rgba(124, 58, 237, 0.25);">
                        <div class="d-flex align-items-center justify-content-center gap-1 text-muted small mb-1" style="font-size: 0.72rem;">
                            <i data-lucide="sparkles" style="width: 12px; height: 12px; color: #7c3aed;"></i>
                            <span>PDF Editor</span>
                        </div>
                        <div class="fw-bold fs-4 mb-0" style="color: #7c3aed;"><?php echo $stats['editor_jobs']; ?></div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(124, 58, 237, 0.2); color: #6d28d9; font-size: 0.68rem;"><?php echo $editorPct; ?>%</span>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                <div class="small d-flex align-items-center gap-2">
                    <span class="p-1 rounded-circle bg-success text-white d-inline-flex" style="width: 8px; height: 8px;"></span>
                    <span class="text-secondary fw-medium">Tỷ lệ thành công:</span> 
                    <span class="text-success fw-bold"><?php echo round(($stats['completed_jobs'] / $totalJobs) * 100, 1); ?>%</span>
                    <span class="text-muted small">(<?php echo $stats['completed_jobs']; ?> / <?php echo $stats['total_jobs']; ?>)</span>
                </div>
                <a href="/admin/exports" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                    <span>Chi tiết</span>
                    <i data-lucide="arrow-right" style="width: 12px; height: 12px;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Trạng thái Môi trường & Máy chủ -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 rounded-4 shadow-sm border border-light-subtle">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                    <div class="p-1 rounded-2 text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i data-lucide="server" style="width: 16px; height: 16px;"></i>
                    </div>
                    <span>Trạng thái Môi trường & Máy chủ</span>
                </h6>
                <div class="d-flex align-items-center gap-1 px-2 py-0 bg-success-subtle text-success border border-success-subtle rounded-pill small" style="font-size: 0.68rem; font-weight: 600;">
                    <span class="p-1 rounded-circle bg-success d-inline-block" style="width: 6px; height: 6px;"></span>
                    <span>Hoạt động tốt</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.84rem;">
                    <tbody>
                        <tr class="border-bottom border-light-subtle">
                            <td class="text-muted py-2 ps-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1 rounded-2" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                                        <i data-lucide="terminal" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span>Phiên bản PHP</span>
                                </div>
                            </td>
                            <td class="fw-bold text-dark text-end pe-1">
                                <span class="badge bg-light text-dark border font-monospace"><?php echo $serverEnv['php_version']; ?></span>
                                <small class="text-muted ms-1">(<?php echo $serverEnv['os']; ?>)</small>
                            </td>
                        </tr>
                        <tr class="border-bottom border-light-subtle">
                            <td class="text-muted py-2 ps-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1 rounded-2" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                                        <i data-lucide="database" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span>Cơ sở dữ liệu</span>
                                </div>
                            </td>
                            <td class="fw-bold text-dark text-end pe-1">
                                <span class="badge bg-light text-primary border font-monospace">MySQL / MariaDB</span>
                                <small class="text-muted ms-1"><?php echo htmlspecialchars($serverEnv['db_version']); ?></small>
                            </td>
                        </tr>
                        <tr class="border-bottom border-light-subtle">
                            <td class="text-muted py-2 ps-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1 rounded-2" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                                        <i data-lucide="hard-drive-download" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span>Giới hạn Tải lên</span>
                                </div>
                            </td>
                            <td class="fw-bold text-dark text-end pe-1 font-monospace" style="font-size: 0.8rem;">
                                Upload: <span class="text-success"><?php echo $serverEnv['upload_max_filesize']; ?></span> &bull; Post: <span class="text-success"><?php echo $serverEnv['post_max_size']; ?></span>
                            </td>
                        </tr>
                        <tr class="border-bottom border-light-subtle">
                            <td class="text-muted py-2 ps-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1 rounded-2" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                                        <i data-lucide="cpu" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span>Bộ nhớ & Timeout</span>
                                </div>
                            </td>
                            <td class="fw-bold text-dark text-end pe-1 font-monospace" style="font-size: 0.8rem;">
                                RAM: <span class="text-warning"><?php echo $serverEnv['memory_limit']; ?></span> &bull; Timeout: <?php echo $serverEnv['max_execution_time']; ?>s
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2 ps-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1 rounded-2" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                                        <i data-lucide="layers" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <span>QPDF Engine</span>
                                </div>
                            </td>
                            <td class="fw-bold text-end pe-1">
                                <?php if ($serverEnv['qpdf_available']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                        ✓ QPDF <?php echo htmlspecialchars($serverEnv['qpdf_version']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">FPDI Engine PHP</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Danh sách Tài liệu & Lần xuất gần nhất toàn hệ thống -->
<div class="row g-4">
    <!-- Tài liệu vừa tải lên -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100 rounded-4 shadow-sm border border-light-subtle">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                    <div class="p-1 rounded-2 text-white" style="background: linear-gradient(135deg, #0284c7, #38bdf8);">
                        <i data-lucide="folder" style="width: 16px; height: 16px;"></i>
                    </div>
                    <span>Tài liệu PDF vừa tải lên (Toàn hệ thống)</span>
                </h6>
                <a href="/admin/documents" class="text-primary text-decoration-none small fw-bold d-inline-flex align-items-center gap-1">
                    <span>Xem tất cả</span>
                    <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0" style="font-size: 0.84rem;">
                    <thead>
                        <tr class="text-muted border-bottom" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th class="ps-1 pb-2">Tên tài liệu</th>
                            <th class="pb-2">Người sở hữu</th>
                            <th class="pb-2">Dung lượng</th>
                            <th class="text-end pe-1 pb-2">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentDocs)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có tài liệu nào trong hệ thống.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentDocs as $d): ?>
                                <tr class="border-bottom-subtle hover-bg-subtle transition-all">
                                    <td class="py-2 ps-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-1 rounded-2 text-white shadow-2xs flex-shrink-0" style="background: linear-gradient(135deg, #4f46e5, #06b6d4);">
                                                <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($d['original_filename']); ?>">
                                                    <?php echo htmlspecialchars($d['original_filename']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.68rem;"><?php echo (int)$d['total_pages']; ?> trang</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 22px; height: 22px; font-size: 0.65rem; background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                                                <?php echo strtoupper(substr($d['user_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <span class="text-dark fw-medium text-truncate" style="max-width: 130px; font-size: 0.8rem;"><?php echo htmlspecialchars($d['user_name'] ?? 'ID #' . $d['user_id']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-2 text-secondary" style="font-size: 0.78rem;">
                                        <?php echo format_bytes((int)$d['file_size']); ?>
                                    </td>
                                    <td class="py-2 text-muted text-end pe-1" style="font-size: 0.75rem;">
                                        <?php echo date('H:i d/m', strtotime($d['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lượt xuất gần đây -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100 rounded-4 shadow-sm border border-light-subtle">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                    <div class="p-1 rounded-2 text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i data-lucide="history" style="width: 16px; height: 16px;"></i>
                    </div>
                    <span>Lượt xuất gần đây</span>
                </h6>
                <a href="/admin/exports" class="text-primary text-decoration-none small fw-bold d-inline-flex align-items-center gap-1">
                    <span>Xem tất cả</span>
                    <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                </a>
            </div>

            <div class="d-flex flex-column gap-2">
                <?php if (empty($recentExports)): ?>
                    <div class="text-center py-4 text-muted small">Chưa có lượt xuất nào.</div>
                <?php else: ?>
                    <?php foreach ($recentExports as $rx): ?>
                        <?php 
                            $jType = $rx['job_type'] ?? 'editor';
                            $tGrad = match($jType) {
                                'merge' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                                'split' => 'linear-gradient(135deg, #ea580c 0%, #f43f5e 100%)',
                                default => 'linear-gradient(135deg, #7c3aed 0%, #ec4899 100%)'
                            };
                            $tIcon = match($jType) {
                                'merge' => 'combine',
                                'split' => 'split',
                                default => 'sparkles'
                            };
                        ?>
                        <div class="p-2 px-3 rounded-3 bg-light-subtle border d-flex justify-content-between align-items-center transition-all hover-lift">
                            <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                <div class="p-1 rounded-2 text-white shadow-2xs flex-shrink-0" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: <?php echo $tGrad; ?>;">
                                    <i data-lucide="<?php echo $tIcon; ?>" style="width: 14px; height: 14px;"></i>
                                </div>
                                <div class="text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;" title="<?php echo htmlspecialchars($rx['output_name'] ?? 'Bản xuất #' . $rx['id']); ?>">
                                        <?php echo htmlspecialchars($rx['output_name'] ?? 'Bản xuất #' . $rx['id']); ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.68rem;">
                                        <span><?php echo htmlspecialchars($rx['user_name'] ?? 'Người dùng'); ?></span> &bull; 
                                        <span class="text-uppercase fw-semibold"><?php echo htmlspecialchars($rx['job_type']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <span class="badge rounded-pill <?php echo $rx['status'] === 'completed' ? 'text-success' : 'text-danger'; ?> px-2 py-1 small fw-bold" style="background: <?php echo $rx['status'] === 'completed' ? 'rgba(16, 185, 129, 0.12)' : 'rgba(244, 63, 94, 0.12)'; ?>; font-size: 0.70rem;">
                                <?php echo $rx['status'] === 'completed' ? '✓ Thành công' : '✕ Thất bại'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
