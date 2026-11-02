<?php ob_start(); ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/exports" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Bản Chụp Bất Biến (Snapshot) #<?php echo $job['id']; ?></h4>
            <small class="text-muted">Đối chiếu chi tiết nguồn gốc từng trang trong tệp kết quả xuất</small>
        </div>
    </div>
    <div>
        <?php if ($job['status'] === 'completed' && !empty($job['download_token'])): ?>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="reviewPdf('/exports/download?token=<?php echo $job['download_token']; ?>&inline=1', '<?php echo htmlspecialchars(addslashes($job['output_name'] ?? 'PDF_Workspace_Export.pdf')); ?>', '/exports/download?token=<?php echo $job['download_token']; ?>')">
                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                    <span>Xem trước tệp (Review)</span>
                </button>
                <a href="/exports/download?token=<?php echo $job['download_token']; ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" download="<?php echo htmlspecialchars($job['output_name'] ?? 'PDF_Workspace_Export.pdf'); ?>">
                    <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                    <span>Tải tệp thành phẩm</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Thẻ thông tin tổng quát bản xuất -->
<div class="card card-custom p-4 mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <span class="text-muted small">Người thực hiện</span>
            <div class="fw-bold text-dark mt-1"><?php echo htmlspecialchars($job['user_name'] ?? 'ID #' . $job['user_id']); ?></div>
            <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($job['user_email'] ?? ''); ?></div>
        </div>
        <div class="col-md-3">
            <span class="text-muted small">Tên tệp kết quả</span>
            <div class="fw-bold text-dark mt-1 text-truncate" title="<?php echo htmlspecialchars($job['output_name'] ?? ''); ?>">
                <?php echo htmlspecialchars($job['output_name'] ?? 'Tệp xuất #' . $job['id']); ?>
            </div>
            <span class="badge bg-light text-secondary border text-uppercase" style="font-size: 0.65rem;"><?php echo htmlspecialchars($job['job_type']); ?></span>
        </div>
        <div class="col-md-3">
            <span class="text-muted small">Thông số tệp</span>
            <div class="fw-bold text-dark mt-1">
                <?php echo !empty($job['output_pages']) ? (int)$job['output_pages'] . ' trang' : '-'; ?> &bull; 
                <?php echo !empty($job['file_size']) ? format_bytes((int)$job['file_size']) : '-'; ?>
            </div>
            <div class="text-muted" style="font-size: 0.75rem;">Thời gian: <?php echo date('d/m/Y H:i:s', strtotime($job['created_at'])); ?></div>
        </div>
        <div class="col-md-3">
            <span class="text-muted small">Trạng thái xử lý</span>
            <div class="mt-1">
                <?php if ($job['status'] === 'completed'): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i data-lucide="check" style="width: 12px; height: 12px;" class="me-1"></i> Thành công
                    </span>
                <?php elseif ($job['status'] === 'failed'): ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        <i data-lucide="alert-circle" style="width: 12px; height: 12px;" class="me-1"></i> Thất bại
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Đang xử lý</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($job['error_message'])): ?>
        <div class="alert alert-danger mt-3 mb-0 small">
            <strong>Thông báo lỗi:</strong> <?php echo htmlspecialchars($job['error_message']); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Bảng đối chiếu Snapshot -->
<div class="card card-custom">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="layers" style="width: 16px; height: 16px; color: #6366f1;"></i>
            <span>Bảng Snapshot Đối Chiếu Trang Nguồn (Immutable)</span>
        </h6>
        <span class="badge bg-primary-subtle text-primary border">Tổng cộng <?php echo count($snapshotPages); ?> trang</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th style="width: 80px;">Trang đích</th>
                    <th>Tên tệp nguồn tại lúc xuất (Snapshot)</th>
                    <th>Tài liệu nguồn hiện tại</th>
                    <th>Trang nguồn gốc</th>
                    <th>Góc xoay</th>
                    <th>Chuẩn A4 dọc</th>
                    <th>Thời điểm đóng băng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($snapshotPages as $p): ?>
                    <tr>
                        <td class="fw-bold text-primary">Trang <?php echo $p['output_order']; ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?php echo htmlspecialchars($p['source_filename_snapshot']); ?></div>
                            <span class="text-muted" style="font-size: 0.70rem;">Tài liệu gốc ID: #<?php echo $p['document_id']; ?></span>
                        </td>
                        <td>
                            <?php if (!empty($p['current_doc_name'])): ?>
                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['current_doc_name']); ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border">Đã bị xóa khỏi kho (Vẫn toàn vẹn trong Snapshot)</span>
                            <?php endif; ?>
                        </td>
                        <td class="small fw-semibold">Trang <?php echo $p['source_page_number']; ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo (int)$p['rotation_degrees']; ?>&deg;</span></td>
                        <td>
                            <?php if (!empty($p['fit_portrait'])): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">✓ Khổ dọc A4</span>
                            <?php else: ?>
                                <span class="text-muted small">Mặc định</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?php echo date('d/m/Y H:i:s', strtotime($p['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
