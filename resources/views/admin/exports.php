<?php ob_start(); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Giám sát Xuất & Bản chụp Bất biến (Snapshot)</h4>
        <p class="text-muted small mb-0">Theo dõi toàn bộ các lần xuất PDF trên toàn hệ thống, kiểm tra tính bất biến của Snapshot và tải tệp kết quả.</p>
    </div>
</div>

<!-- Bộ lọc tìm kiếm & Lọc trạng thái / loại tác vụ -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="/admin/exports" class="row g-2 align-items-center">
        <div class="col-md-3 col-lg-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm theo tên file hoặc #ID..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>
        </div>

        <div class="col-6 col-md-2 col-lg-2">
            <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả tác vụ</option>
                <option value="merge" <?php echo ($type ?? '') === 'merge' ? 'selected' : ''; ?>>Ghép PDF</option>
                <option value="split" <?php echo ($type ?? '') === 'split' ? 'selected' : ''; ?>>Tách PDF</option>
                <option value="editor" <?php echo ($type ?? '') === 'editor' ? 'selected' : ''; ?>>PDF Editor</option>
            </select>
        </div>

        <div class="col-6 col-md-2 col-lg-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="completed" <?php echo ($status ?? '') === 'completed' ? 'selected' : ''; ?>>Thành công</option>
                <option value="failed" <?php echo ($status ?? '') === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
            </select>
        </div>

        <div class="col-6 col-md-2 col-lg-2">
            <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả người dùng</option>
                <?php foreach ($allUsers as $usr): ?>
                    <option value="<?php echo $usr['id']; ?>" <?php echo ($filterUserId ?? 0) === (int)$usr['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usr['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-2 col-lg-2">
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>Cũ nhất trước</option>
                <option value="size_desc" <?php echo ($sort ?? '') === 'size_desc' ? 'selected' : ''; ?>>Dung lượng lớn nhất</option>
                <option value="pages_desc" <?php echo ($sort ?? '') === 'pages_desc' ? 'selected' : ''; ?>>Nhiều trang nhất</option>
                <option value="name_asc" <?php echo ($sort ?? '') === 'name_asc' ? 'selected' : ''; ?>>Tên A &rarr; Z</option>
            </select>
        </div>

        <div class="col-12 col-md-1 col-lg-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary w-100">Lọc</button>
            <?php if (!empty($search) || !empty($type) || !empty($status) || !empty($filterUserId) || ($sort ?? '') !== 'created_at_desc'): ?>
                <a href="/admin/exports" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
        <input type="hidden" name="per_page" value="<?php echo htmlspecialchars((string)($perPage ?? 10)); ?>">
    </form>
</div>

<!-- Bảng Lịch sử & Giám sát Snapshot -->
<div class="card card-custom">
    <?php if (empty($exports)): ?>
        <div class="text-center py-5">
            <i data-lucide="history" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-3"></i>
            <h6 class="fw-semibold text-dark">Không tìm thấy bản xuất phù hợp</h6>
            <p class="text-muted small mb-3">Vui lòng thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.</p>
            <a href="/admin/exports" class="btn btn-outline-secondary btn-sm">Xóa bộ lọc</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 60px;">Mã</th>
                        <th>Tệp kết quả</th>
                        <th>Người thực hiện</th>
                        <th>Loại tác vụ</th>
                        <th>Số trang</th>
                        <th>Dung lượng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exports as $job): ?>
                        <tr>
                            <td class="text-muted small"><strong>#<?php echo $job['id']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 text-white shadow-sm flex-shrink-0" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: <?php echo $job['status'] === 'completed' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)'; ?>;">
                                        <i data-lucide="<?php echo $job['status'] === 'completed' ? 'file-check' : 'file-x'; ?>" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                            <?php echo htmlspecialchars($job['output_name'] ?? 'Tệp xuất #' . $job['id']); ?>
                                        </div>
                                        <?php if (!empty($job['stored_filename'])): ?>
                                            <div class="text-muted" style="font-size: 0.72rem;"><code><?php echo htmlspecialchars($job['stored_filename']); ?></code></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small text-dark"><?php echo htmlspecialchars($job['user_name'] ?? 'ID #' . $job['user_id']); ?></div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($job['user_email'] ?? ''); ?></div>
                            </td>
                            <td>
                                <?php
                                $badgeStyle = match($job['job_type']) {
                                    'editor' => 'background: rgba(168, 85, 247, 0.12); color: #7c3aed; border-color: rgba(168, 85, 247, 0.3);',
                                    'merge' => 'background: rgba(16, 185, 129, 0.12); color: #059669; border-color: rgba(16, 185, 129, 0.3);',
                                    'split' => 'background: rgba(245, 158, 11, 0.12); color: #d97706; border-color: rgba(245, 158, 11, 0.3);',
                                    default => 'background: #f1f5f9; color: #475569; border-color: #e2e8f0;'
                                };
                                ?>
                                <span class="badge border text-uppercase" style="font-size: 0.7rem; <?php echo $badgeStyle; ?>">
                                    <?php echo htmlspecialchars($job['job_type']); ?>
                                </span>
                            </td>
                            <td class="small fw-semibold"><?php echo !empty($job['output_pages']) ? (int)$job['output_pages'] . ' trang' : '-'; ?></td>
                            <td class="text-muted small"><?php echo !empty($job['file_size']) ? format_bytes((int)$job['file_size']) : '-'; ?></td>
                            <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($job['created_at'])); ?></td>
                            <td>
                                <?php if ($job['status'] === 'completed'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i data-lucide="check" style="width: 12px; height: 12px;" class="me-1"></i> Thành công
                                    </span>
                                <?php elseif ($job['status'] === 'failed'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" title="<?php echo htmlspecialchars($job['error_message'] ?? 'Lỗi'); ?>">
                                        <i data-lucide="alert-circle" style="width: 12px; height: 12px;" class="me-1"></i> Thất bại
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        Đang xử lý
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/exports/detail?id=<?php echo $job['id']; ?>" class="btn btn-outline-secondary" title="Xem bảng đối chiếu Snapshot">
                                        <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-md-inline ms-1">Đối chiếu</span>
                                    </a>
                                    <?php if ($job['status'] === 'completed' && !empty($job['download_token'])): ?>
                                        <button type="button" class="btn btn-outline-primary" title="Xem trước tài liệu PDF (Review)" onclick="reviewPdf('/exports/download?token=<?php echo $job['download_token']; ?>&inline=1', '<?php echo htmlspecialchars(addslashes($job['output_name'] ?? 'Tài liệu')); ?>', '/exports/download?token=<?php echo $job['download_token']; ?>')">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                            <span class="d-none d-xl-inline ms-1">Xem trước</span>
                                        </button>
                                        <a href="/exports/download?token=<?php echo $job['download_token']; ?>" class="btn btn-primary" title="Tải xuống tệp thành phẩm" download="<?php echo htmlspecialchars($job['output_name'] ?? 'PDF_Workspace_Export.pdf'); ?>">
                                            <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php 
        $paginationParams = array_filter([
            'q' => $search ?? '',
            'type' => $type ?? '',
            'status' => $status ?? '',
            'user_id' => !empty($filterUserId) ? $filterUserId : '',
            'sort' => ($sort ?? '') !== 'created_at_desc' ? $sort : '',
            'per_page' => ($perPage ?? 10) !== 10 ? $perPage : '',
        ]);
        echo render_pagination(
            $currentPage ?? 1, 
            $totalPages ?? 1, 
            $totalItems ?? count($exports), 
            $perPage ?? 10, 
            '/admin/exports', 
            $paginationParams
        ); 
        ?>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
