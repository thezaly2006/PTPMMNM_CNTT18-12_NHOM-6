<?php ob_start(); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Bảo trì Hệ thống & Bộ nhớ Đệm</h4>
        <p class="text-muted small mb-0">Theo dõi phân bổ dung lượng ổ đĩa, dọn dẹp các tệp tạm thời và kiểm thử nghiệm thu hệ thống.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/tests" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 rounded-3">
            <i data-lucide="play" style="width: 14px; height: 14px;"></i>
            <span>Chạy 5 Bài Kiểm Thử Nghiệm Thu</span>
        </a>
    </div>
</div>

<!-- 4 Thẻ Phân Bổ Dung Lượng -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold">Tài liệu Nguồn</span>
                <i data-lucide="folder" style="width: 18px; height: 18px; color: #6366f1;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?php echo format_bytes($storageDetails['sources'] ?? 0); ?></h4>
            <code class="text-muted small">storage/sources/</code>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold">Tệp Xuất Kết Quả</span>
                <i data-lucide="file-check" style="width: 18px; height: 18px; color: #10b981;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?php echo format_bytes($storageDetails['outputs'] ?? 0); ?></h4>
            <code class="text-muted small">storage/outputs/</code>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold">Tệp Đệm Tạm Thời</span>
                <i data-lucide="archive" style="width: 18px; height: 18px; color: #f59e0b;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?php echo format_bytes($storageDetails['temp'] ?? 0); ?></h4>
            <code class="text-muted small">storage/temp/</code>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold">Nhật Ký (Logs)</span>
                <i data-lucide="scroll-text" style="width: 18px; height: 18px; color: #e11d48;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?php echo format_bytes($storageDetails['logs'] ?? 0); ?></h4>
            <code class="text-muted small">storage/logs/</code>
        </div>
    </div>
</div>

<!-- Khối Thao tác Dọn dẹp Bộ nhớ tạm -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 rounded-3 bg-warning-subtle text-warning-emphasis">
                    <i data-lucide="trash-2" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Dọn dẹp Tệp Tạm Thời (Temporary Cleaner)</h6>
                    <small class="text-muted">Giải phóng dung lượng các tệp phát sinh trong quá trình ghép hoặc tách PDF</small>
                </div>
            </div>

            <p class="small text-muted mb-4">
                Hiện có <strong><?php echo count($tempFiles); ?></strong> tệp rác trong thư mục <code>storage/temp/</code> với tổng dung lượng là <strong><?php echo format_bytes($storageDetails['temp'] ?? 0); ?></strong>. Việc dọn dẹp sẽ không ảnh hưởng tới tài liệu nguồn hay các file thành phẩm đã xuất.
            </p>

            <form action="/admin/system/clean-temp" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn dọn sạch toàn bộ các tệp tạm thời không?');">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-warning text-dark fw-semibold d-flex align-items-center gap-2" <?php echo empty($tempFiles) ? 'disabled' : ''; ?>>
                    <i data-lucide="broom" style="width: 16px; height: 16px;"></i>
                    <span>Dọn sạch tệp tạm ngay</span>
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 rounded-3 bg-primary-subtle text-primary">
                    <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Đánh giá Nghiệm thu Đề tài 12</h6>
                    <small class="text-muted">Chạy bộ kiểm thử tự động 5 tiêu chí then chốt</small>
                </div>
            </div>

            <ul class="list-unstyled small text-muted mb-4 ps-1">
                <li class="mb-2 d-flex align-items-center gap-2">
                    <i data-lucide="check" style="width: 14px; height: 14px; color: #10b981;"></i>
                    <span><strong>Bài 1:</strong> Ghép trang nhiều tài liệu &amp; kiểm tra số trang</span>
                </li>
                <li class="mb-2 d-flex align-items-center gap-2">
                    <i data-lucide="check" style="width: 14px; height: 14px; color: #10b981;"></i>
                    <span><strong>Bài 2:</strong> Xoay trang 90/180/270 độ và loại bỏ trang</span>
                </li>
                <li class="mb-2 d-flex align-items-center gap-2">
                    <i data-lucide="check" style="width: 14px; height: 14px; color: #10b981;"></i>
                    <span><strong>Bài 3:</strong> Tách PDF thành nhiều nhóm &amp; từ chối dải trang sai</span>
                </li>
                <li class="mb-2 d-flex align-items-center gap-2">
                    <i data-lucide="check" style="width: 14px; height: 14px; color: #10b981;"></i>
                    <span><strong>Bài 4:</strong> An toàn trước tệp hỏng, rỗng và giả mạo</span>
                </li>
                <li class="d-flex align-items-center gap-2">
                    <i data-lucide="check" style="width: 14px; height: 14px; color: #10b981;"></i>
                    <span><strong>Bài 5:</strong> Phân quyền đa người dùng &amp; Tính bất biến của Snapshot</span>
                </li>
            </ul>

            <a href="/tests" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                <i data-lucide="play" style="width: 16px; height: 16px;"></i>
                <span>Chạy kiểm thử nghiệm thu</span>
            </a>
        </div>
    </div>
</div>

<!-- Danh sách Tệp Tạm Đang Tồn Tại -->
<div class="card card-custom">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="files" style="width: 16px; height: 16px; color: #64748b;"></i>
            <span>Danh sách tệp tạm hiện có trong <code>storage/temp/</code></span>
        </h6>
        <span class="badge bg-light text-secondary border"><?php echo count($tempFiles); ?> tệp</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>Tên tệp tạm</th>
                    <th>Dung lượng</th>
                    <th>Thời gian tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tempFiles)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i data-lucide="check-circle" style="width: 24px; height: 24px; color: #10b981;" class="mb-1 d-block mx-auto"></i>
                            Thư mục tạm đang hoàn toàn sạch sẽ, không có tệp rác.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tempFiles as $tf): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($tf['name']); ?></code></td>
                            <td><?php echo format_bytes((int)$tf['size']); ?></td>
                            <td class="text-muted"><?php echo date('d/m/Y H:i:s', $tf['mtime']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
