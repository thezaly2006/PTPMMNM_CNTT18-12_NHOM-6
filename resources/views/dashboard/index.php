<?php ob_start(); ?>

<!-- Hero Welcome Banner -->
<div class="p-4 p-md-5 rounded-4 mb-4 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);">
    <!-- Decorative background blobs -->
    <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: rgba(255, 255, 255, 0.12); border-radius: 50%; filter: blur(30px); pointer-events: none;"></div>
    <div style="position: absolute; right: 120px; bottom: -50px; width: 180px; height: 180px; background: rgba(253, 224, 71, 0.15); border-radius: 50%; filter: blur(25px); pointer-events: none;"></div>

    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <span class="badge bg-white text-primary px-3 py-1 mb-2 fw-semibold rounded-pill shadow-sm" style="font-size: 0.78rem;">
                <i data-lucide="sparkles" style="width: 13px; height: 13px;" class="me-1"></i> Không gian làm việc PDF 2.0
            </span>
            <h2 class="fw-bold mb-2">Xin chào, <?php echo htmlspecialchars(auth_user()['name'] ?? 'Bạn'); ?>! 👋</h2>
            <p class="mb-3 text-white-50" style="font-size: 0.95rem; max-width: 620px;">
                Bộ công cụ trực quan hỗ trợ sắp xếp, xoay khổ A4 dọc, ghép và tách tài liệu PDF với tính năng đóng băng bản chụp bất biến (Snapshot) đạt chuẩn nghiệm thu.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-light text-primary fw-semibold btn-sm px-3 d-flex align-items-center gap-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i>
                    <span>Tải nhiều tệp PDF</span>
                </button>
                <a href="/editor" class="btn btn-outline-light fw-medium btn-sm px-3 d-flex align-items-center gap-2 rounded-3" style="border-color: rgba(255,255,255,0.4);">
                    <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                    <span>Mở PDF Editor</span>
                </a>
            </div>
        </div>
        <div class="col-lg-4 d-none d-lg-flex justify-content-end">
            <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-20 backdrop-blur" style="backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 bg-white text-primary">
                        <i data-lucide="shield-check" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">Toàn vẹn & An toàn</div>
                        <div class="text-white-50" style="font-size: 0.75rem;">Không bao giờ làm mất trang gốc</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3 Thẻ thống kê thời gian thực sinh động -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #6366f1, #3b82f6);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Tài liệu của tôi</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #6366f1, #3b82f6);">
                    <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_documents']); ?></h2>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">Đang quản lý</span>
            </div>
            <small class="text-muted">Tổng số tài liệu nguồn PDF trong kho lưu trữ</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #10b981, #06b6d4);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Phiên chỉnh sửa</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #06b6d4);">
                    <i data-lucide="edit-3" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_plans']); ?></h2>
                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">✓ Tự động lưu</span>
            </div>
            <small class="text-muted">Các kế hoạch kéo thả và xoay trang PDF</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #a855f7, #ec4899);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Lần xuất thành công</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #a855f7, #ec4899);">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_exports']); ?></h2>
                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.72rem;">Snapshot bất biến</span>
            </div>
            <small class="text-muted">Tệp PDF thành phẩm đã được đối chiếu an toàn</small>
        </div>
    </div>
</div>

<!-- Khối tác vụ nhanh hiện đại -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="zap" style="width: 18px; height: 18px; color: #f59e0b;"></i>
            <span>Công cụ thao tác nhanh</span>
        </h6>
        <span class="text-muted small">Chọn chức năng cần thực hiện</span>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-white h-100 shadow-sm" style="transition: all 0.2s ease;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <i data-lucide="upload-cloud" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Tải PDF lên</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Nạp nhiều tệp nguồn cùng lúc</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#uploadModal">Thực hiện</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-white h-100 shadow-sm" style="transition: all 0.2s ease;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i data-lucide="combine" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Ghép PDF</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Hợp nhất trang theo thứ tự tùy ý</div>
                    </div>
                </div>
                <a href="/merge" class="btn btn-sm btn-outline-success px-3 rounded-pill">Bắt đầu</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-white h-100 shadow-sm" style="transition: all 0.2s ease;">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i data-lucide="split" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small">Tách PDF</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Trích xuất trang thành các tệp riêng</div>
                    </div>
                </div>
                <a href="/split" class="btn btn-sm btn-outline-warning px-3 rounded-pill">Bắt đầu</a>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách tài liệu gần đây & Lần xuất gần đây -->
<div class="row g-4">
    <!-- Tài liệu vừa tải lên -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i data-lucide="clock" style="width: 17px; height: 17px; color: #0284c7;"></i>
                    <span>Tài liệu gần đây</span>
                </h6>
                <a href="/documents" class="text-primary text-decoration-none small fw-semibold d-flex align-items-center gap-1">
                    <span>Xem tất cả</span>
                    <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                </a>
            </div>

            <?php if (empty($recentDocuments)): ?>
                <div class="text-center py-5">
                    <i data-lucide="folder-open" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-2"></i>
                    <p class="text-muted small mb-3">Chưa có tài liệu nào trong không gian làm việc của bạn.</p>
                    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i data-lucide="upload" style="width: 14px; height: 14px;" class="me-1"></i>
                        <span>Tải tài liệu đầu tiên</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th scope="col" style="font-weight: 600;">Tên tài liệu</th>
                                <th scope="col" style="font-weight: 600;">Số trang</th>
                                <th scope="col" style="font-weight: 600;">Dung lượng</th>
                                <th scope="col" style="font-weight: 600;" class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDocuments as $doc): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-1 rounded bg-indigo-50" style="background: #eef2ff;">
                                                <i data-lucide="file-text" style="width: 17px; height: 17px; color: #6366f1;"></i>
                                            </div>
                                            <span class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="<?php echo htmlspecialchars($doc['original_filename']); ?>">
                                                <?php echo htmlspecialchars($doc['original_filename']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><?php echo (int)$doc['total_pages']; ?> trang</span></td>
                                    <td class="text-muted small"><?php echo format_bytes((int)$doc['file_size']); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/editor?doc_id=<?php echo $doc['id']; ?>" class="btn btn-outline-primary" title="Mở trong Editor">
                                                <i data-lucide="edit-2" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <a href="/documents/download?id=<?php echo $doc['id']; ?>" class="btn btn-outline-secondary" title="Tải về máy">
                                                <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lần xuất gần đây -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i data-lucide="history" style="width: 17px; height: 17px; color: #e11d48;"></i>
                    <span>Bản xuất gần đây</span>
                </h6>
                <a href="/exports" class="text-primary text-decoration-none small fw-semibold d-flex align-items-center gap-1">
                    <span>Xem lịch sử</span>
                    <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                </a>
            </div>

            <?php if (empty($recentExports)): ?>
                <div class="text-center py-5">
                    <i data-lucide="clock" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-2"></i>
                    <p class="text-muted small mb-0">Chưa có bản xuất nào được tạo.</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentExports as $job): ?>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <div class="fw-bold text-dark small text-truncate" style="max-width: 210px;">
                                    <?php echo htmlspecialchars($job['output_name'] ?? 'Tệp kết quả #' . $job['id']); ?>
                                </div>
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    <span><?php echo date('H:i d/m/Y', strtotime($job['created_at'])); ?></span> &bull; 
                                    <span class="badge bg-light text-secondary border text-uppercase" style="font-size: 0.65rem;"><?php echo htmlspecialchars($job['job_type']); ?></span>
                                </div>
                            </div>
                            <div>
                                <?php if ($job['status'] === 'completed'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i data-lucide="check" style="width: 12px; height: 12px;" class="me-1"></i> Thành công
                                    </span>
                                <?php elseif ($job['status'] === 'failed'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i data-lucide="alert-circle" style="width: 12px; height: 12px;" class="me-1"></i> Thất bại
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <span class="spinner-border spinner-border-sm me-1" style="width: 9px; height: 9px;"></span> Đang xử lý
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
