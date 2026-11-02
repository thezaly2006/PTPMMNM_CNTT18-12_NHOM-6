<?php ob_start(); ?>

<div style="max-width: 1000px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="/exports" class="btn btn-sm btn-light border text-muted">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            </a>
            <div>
                <h4 class="fw-bold text-dark mb-0">Chi tiết bản xuất #<?php echo (int)$job['id']; ?></h4>
                <small class="text-muted">Bản chụp bất biến (Immutable Snapshot) của lần xuất tài liệu</small>
            </div>
        </div>

        <?php if ($job['status'] === 'completed' && !empty($job['download_token'])): ?>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="reviewPdf('/exports/download?token=<?php echo $job['download_token']; ?>&inline=1', '<?php echo htmlspecialchars(addslashes($job['output_name'] ?? 'PDF_Workspace_Export.pdf')); ?>', '/exports/download?token=<?php echo $job['download_token']; ?>')">
                    <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                    <span>Xem trước file kết quả</span>
                </button>
                <a href="/exports/download?token=<?php echo $job['download_token']; ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" download="<?php echo htmlspecialchars($job['output_name'] ?? 'PDF_Workspace_Export.pdf'); ?>">
                    <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                    <span>Tải tệp kết quả</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Thông tin tổng quát về bản xuất -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-3">
                <small class="text-muted fw-semibold">TÊN TỆP XUẤT</small>
                <div class="fw-bold text-dark text-truncate mt-1" title="<?php echo htmlspecialchars($job['output_name'] ?? ''); ?>">
                    <?php echo htmlspecialchars($job['output_name'] ?? 'Chưa xác định'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-3">
                <small class="text-muted fw-semibold">TRẠNG THÁI</small>
                <div class="mt-1">
                    <?php if ($job['status'] === 'completed'): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Hoàn thành</span>
                    <?php elseif ($job['status'] === 'failed'): ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Thất bại</span>
                    <?php else: ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Đang xử lý</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-3">
                <small class="text-muted fw-semibold">TỔNG SỐ TRANG</small>
                <div class="fw-bold text-dark mt-1">
                    <?php echo !empty($job['output_pages']) ? (int)$job['output_pages'] . ' trang' : count($snapshotPages) . ' trang'; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card card-custom p-3">
                <small class="text-muted fw-semibold">THỜI GIAN ĐÓNG BĂNG</small>
                <div class="fw-bold text-dark mt-1">
                    <?php echo date('H:i:s d/m/Y', strtotime($job['created_at'])); ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($job['status'] === 'failed' && !empty($job['error_message'])): ?>
        <div class="alert alert-danger mb-4 small">
            <strong>Thông báo lỗi từ hệ thống:</strong> <?php echo htmlspecialchars($job['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Bảng đối chiếu nguồn trang (Snapshot Table) -->
    <div class="card card-custom">
        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">Bảng đối chiếu trang đầu ra và nguồn gốc</h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Bản chụp bất biến (Immutable)</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th scope="col" style="font-weight: 500; width: 120px;">Thứ tự đầu ra</th>
                        <th scope="col" style="font-weight: 500;">Tài liệu nguồn (Snapshot)</th>
                        <th scope="col" style="font-weight: 500;">Số trang gốc</th>
                        <th scope="col" style="font-weight: 500;">Góc xoay</th>
                        <th scope="col" style="font-weight: 500;" class="text-end">Trạng thái xử lý</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($snapshotPages)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">Không có dữ liệu trang cho bản xuất này.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($snapshotPages as $p): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary px-2 py-1">Trang #<?php echo (int)$p['output_order']; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="file-text" style="width: 16px; height: 16px; color: #6366f1;"></i>
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($p['source_filename_snapshot']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">Trang <?php echo (int)$p['source_page_number']; ?></span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <i data-lucide="rotate-cw" style="width: 12px; height: 12px;" class="me-1"></i>
                                        <?php echo (int)$p['rotation_degrees']; ?>°
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <?php if (!empty($p['document_id'])): ?>
                                            <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1" title="Phóng to xem trang gốc này (Ảnh 2)" onclick="openPageZoomModal({ docId: <?php echo (int)$p['document_id']; ?>, pageNum: <?php echo (int)$p['source_page_number']; ?>, title: '<?php echo htmlspecialchars(addslashes($p['source_filename_snapshot'])); ?>', rotation: <?php echo (int)$p['rotation_degrees']; ?> })">
                                                <i data-lucide="maximize-2" style="width: 12px; height: 12px;"></i>
                                                <span style="font-size: 0.7rem;">Xem trang gốc</span>
                                            </button>
                                        <?php endif; ?>
                                        <span class="text-success small fw-medium">
                                            <i data-lucide="check" style="width: 14px; height: 14px;" class="me-1"></i> Khớp chính xác
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php echo render_pagination($currentPage ?? 1, $totalPages ?? 1, $totalItems ?? count($snapshotPages), $perPage ?? 15, '/exports/detail', ['id' => $job['id']]); ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
