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

<!-- Row 2: Phân tích Tác vụ & Môi trường Máy chủ -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i data-lucide="pie-chart" style="width: 18px; height: 18px; color: #6366f1;"></i>
                <span>Phân bố Tác vụ Xuất PDF</span>
            </h6>

            <?php 
            $totalJobs = max(1, $stats['total_jobs']);
            $mergePct = round(($stats['merge_jobs'] / $totalJobs) * 100);
            $splitPct = round(($stats['split_jobs'] / $totalJobs) * 100);
            $editorPct = round(($stats['editor_jobs'] / $totalJobs) * 100);
            ?>

            <div class="progress mb-3" style="height: 12px; border-radius: 8px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $mergePct; ?>%" title="Ghép PDF: <?php echo $mergePct; ?>%"></div>
                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $splitPct; ?>%" title="Tách PDF: <?php echo $splitPct; ?>%"></div>
                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $editorPct; ?>%" title="PDF Editor: <?php echo $editorPct; ?>%"></div>
            </div>

            <div class="row text-center g-2 pt-2">
                <div class="col-4">
                    <div class="p-2 border rounded-3 bg-light">
                        <div class="small text-muted">Ghép PDF</div>
                        <div class="fw-bold text-success fs-5"><?php echo $stats['merge_jobs']; ?></div>
                        <span class="badge bg-success-subtle text-success small"><?php echo $mergePct; ?>%</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded-3 bg-light">
                        <div class="small text-muted">Tách PDF</div>
                        <div class="fw-bold text-warning fs-5"><?php echo $stats['split_jobs']; ?></div>
                        <span class="badge bg-warning-subtle text-warning small"><?php echo $splitPct; ?>%</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 border rounded-3 bg-light">
                        <div class="small text-muted">PDF Editor</div>
                        <div class="fw-bold text-primary fs-5"><?php echo $stats['editor_jobs']; ?></div>
                        <span class="badge bg-primary-subtle text-primary small"><?php echo $editorPct; ?>%</span>
                    </div>
                </div>
            </div>

            <div class="mt-3 p-3 bg-light-subtle rounded-3 border d-flex justify-content-between align-items-center">
                <div class="small">
                    <span class="fw-bold text-dark">Tỷ lệ thành công:</span> 
                    <span class="text-success fw-bold"><?php echo round(($stats['completed_jobs'] / $totalJobs) * 100, 1); ?>%</span>
                    <span class="text-muted ms-2">(<?php echo $stats['completed_jobs']; ?> / <?php echo $stats['total_jobs']; ?>)</span>
                </div>
                <a href="/admin/exports" class="btn btn-xs btn-outline-primary small" style="font-size: 0.75rem;">Xem chi tiết &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i data-lucide="server" style="width: 18px; height: 18px; color: #f59e0b;"></i>
                <span>Trạng thái Môi trường & Máy chủ</span>
            </h6>

            <div class="table-responsive">
                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.85rem;">
                    <tbody>
                        <tr class="border-bottom">
                            <td class="text-muted py-2"><i data-lucide="terminal" style="width: 14px; height: 14px;" class="me-2 text-primary"></i>Phiên bản PHP</td>
                            <td class="fw-bold text-dark text-end"><?php echo $serverEnv['php_version']; ?> (<?php echo $serverEnv['os']; ?>)</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted py-2"><i data-lucide="database" style="width: 14px; height: 14px;" class="me-2 text-info"></i>Cơ sở dữ liệu</td>
                            <td class="fw-bold text-dark text-end">MariaDB / MySQL <?php echo htmlspecialchars($serverEnv['db_version']); ?></td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted py-2"><i data-lucide="hard-drive-download" style="width: 14px; height: 14px;" class="me-2 text-success"></i>Giới hạn Tải lên</td>
                            <td class="fw-bold text-dark text-end">Upload: <?php echo $serverEnv['upload_max_filesize']; ?> &bull; Post: <?php echo $serverEnv['post_max_size']; ?></td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted py-2"><i data-lucide="cpu" style="width: 14px; height: 14px;" class="me-2 text-warning"></i>Bộ nhớ PHP</td>
                            <td class="fw-bold text-dark text-end">RAM: <?php echo $serverEnv['memory_limit']; ?> &bull; Timeout: <?php echo $serverEnv['max_execution_time']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2"><i data-lucide="layers" style="width: 14px; height: 14px;" class="me-2 text-purple"></i>Linux QPDF Binary</td>
                            <td class="fw-bold text-end">
                                <?php if ($serverEnv['qpdf_available']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">✓ Sẵn sàng (<?php echo htmlspecialchars($serverEnv['qpdf_version']); ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border">FPDI PHP Engine (Mặc định)</span>
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
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="file-text" style="width: 17px; height: 17px; color: #0284c7;"></i>
                    <span>Tài liệu PDF vừa tải lên (Toàn hệ thống)</span>
                </h6>
                <a href="/admin/documents" class="text-primary text-decoration-none small fw-semibold">Xem tất cả &rarr;</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>Tên tài liệu</th>
                            <th>Người sở hữu</th>
                            <th>Dung lượng</th>
                            <th class="text-end">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentDocs)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có tài liệu nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentDocs as $d): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="<?php echo htmlspecialchars($d['original_filename']); ?>">
                                            <?php echo htmlspecialchars($d['original_filename']); ?>
                                        </div>
                                        <span class="text-muted" style="font-size: 0.72rem;"><?php echo (int)$d['total_pages']; ?> trang</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($d['user_name'] ?? 'ID #' . $d['user_id']); ?></span>
                                    </td>
                                    <td class="text-muted"><?php echo format_bytes((int)$d['file_size']); ?></td>
                                    <td class="text-muted text-end"><?php echo date('H:i d/m', strtotime($d['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="history" style="width: 17px; height: 17px; color: #10b981;"></i>
                    <span>Lượt xuất gần đây</span>
                </h6>
                <a href="/admin/exports" class="text-primary text-decoration-none small fw-semibold">Xem tất cả &rarr;</a>
            </div>

            <div class="list-group list-group-flush">
                <?php if (empty($recentExports)): ?>
                    <div class="text-center py-4 text-muted small">Chưa có lượt xuất nào.</div>
                <?php else: ?>
                    <?php foreach ($recentExports as $rx): ?>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <div class="fw-semibold text-dark small text-truncate" style="max-width: 190px;">
                                    <?php echo htmlspecialchars($rx['output_name'] ?? 'Bản xuất #' . $rx['id']); ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    <span><?php echo htmlspecialchars($rx['user_name'] ?? 'Người dùng'); ?></span> &bull; 
                                    <span class="badge bg-light text-secondary border text-uppercase" style="font-size: 0.62rem;"><?php echo htmlspecialchars($rx['job_type']); ?></span>
                                </div>
                            </div>
                            <span class="badge <?php echo $rx['status'] === 'completed' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle'; ?> border px-2 py-1 small">
                                <?php echo $rx['status'] === 'completed' ? 'Thành công' : 'Thất bại'; ?>
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
