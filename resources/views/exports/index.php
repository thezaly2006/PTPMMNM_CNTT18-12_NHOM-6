<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Lịch sử xuất tài liệu PDF</h4>
        <p class="text-muted small mb-0">Theo dõi toàn bộ các lần xuất PDF, tải lại file kết quả và kiểm tra bản chụp bất biến (Snapshot).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (($totalItems ?? 0) > 10 || ($perPage ?? 10) !== 10): ?>
            <div class="d-flex align-items-center gap-1 text-muted small me-2">
                <span>Hiển thị:</span>
                <select class="form-select form-select-sm py-1" style="width: auto; font-size: 0.8rem;" onchange="
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', '1');
                    url.searchParams.set('per_page', this.value);
                    window.location.href = url.toString();
                ">
                    <option value="10" <?php echo ($perPage ?? 10) === 10 ? 'selected' : ''; ?>>10 / trang</option>
                    <option value="20" <?php echo ($perPage ?? 10) === 20 ? 'selected' : ''; ?>>20 / trang</option>
                    <option value="50" <?php echo ($perPage ?? 10) === 50 ? 'selected' : ''; ?>>50 / trang</option>
                </select>
            </div>
        <?php endif; ?>
        <a href="/editor" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
            <span>Mở Editor</span>
        </a>
    </div>
</div>

<!-- Bộ lọc tìm kiếm, Tác vụ, Trạng thái & Sắp xếp -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="/exports" class="row g-2 align-items-center">
        <!-- Ô tìm kiếm từ khóa hoặc mã ID -->
        <div class="col-md-4 col-lg-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm theo tên tệp xuất hoặc mã #ID..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>
        </div>

        <!-- Lọc Loại tác vụ -->
        <div class="col-6 col-md-2 col-lg-2">
            <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả tác vụ</option>
                <option value="merge" <?php echo ($type ?? '') === 'merge' ? 'selected' : ''; ?>>Ghép PDF</option>
                <option value="split" <?php echo ($type ?? '') === 'split' ? 'selected' : ''; ?>>Tách PDF</option>
                <option value="editor" <?php echo ($type ?? '') === 'editor' ? 'selected' : ''; ?>>PDF Editor</option>
            </select>
        </div>

        <!-- Lọc Trạng thái -->
        <div class="col-6 col-md-2 col-lg-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="completed" <?php echo ($status ?? '') === 'completed' ? 'selected' : ''; ?>>Thành công</option>
                <option value="failed" <?php echo ($status ?? '') === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
            </select>
        </div>

        <!-- Sắp xếp -->
        <div class="col-6 col-md-2 col-lg-2">
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>Cũ nhất trước</option>
                <option value="size_desc" <?php echo ($sort ?? '') === 'size_desc' ? 'selected' : ''; ?>>Dung lượng lớn nhất</option>
                <option value="pages_desc" <?php echo ($sort ?? '') === 'pages_desc' ? 'selected' : ''; ?>>Nhiều trang nhất</option>
                <option value="name_asc" <?php echo ($sort ?? '') === 'name_asc' ? 'selected' : ''; ?>>Tên tệp A &rarr; Z</option>
            </select>
        </div>

        <!-- Nút tìm kiếm & Xóa lọc -->
        <div class="col-6 col-md-2 col-lg-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                <span>Tìm & Lọc</span>
            </button>
            <?php if (!empty($search) || !empty($type) || !empty($status) || ($sort ?? '') !== 'created_at_desc'): ?>
                <a href="/exports" class="btn btn-sm btn-outline-secondary" title="Xóa toàn bộ bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
        <input type="hidden" name="per_page" value="<?php echo htmlspecialchars((string)($perPage ?? 10)); ?>">
    </form>
</div>

<div class="card card-custom">
    <?php if (empty($exports)): ?>
        <?php if (!empty($search) || !empty($type) || !empty($status)): ?>
            <div class="text-center py-5">
                <i data-lucide="search-x" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-3"></i>
                <h6 class="fw-semibold text-dark">Không tìm thấy bản xuất phù hợp</h6>
                <p class="text-muted small mb-3">
                    Không có kết quả nào khớp với 
                    <?php if (!empty($search)): ?>từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
                    <?php if (!empty($type)): ?><span class="badge bg-light text-secondary border ms-1">Tác vụ: <?php echo htmlspecialchars($type); ?></span><?php endif; ?>
                    <?php if (!empty($status)): ?><span class="badge bg-light text-secondary border ms-1">Trạng thái: <?php echo htmlspecialchars($status); ?></span><?php endif; ?>.
                </p>
                <a href="/exports" class="btn btn-outline-secondary btn-sm">Xóa bộ lọc</a>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i data-lucide="history" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-3"></i>
                <h6 class="fw-semibold text-dark">Chưa có lịch sử xuất file</h6>
                <p class="text-muted small mb-3">Các tài liệu sau khi bạn ghép, tách hoặc biên tập từ PDF Editor sẽ xuất hiện tại đây.</p>
                <a href="/merge" class="btn btn-primary btn-sm">Bắt đầu ghép PDF</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th scope="col" style="font-weight: 500; width: 60px;">Mã</th>
                        <th scope="col" style="font-weight: 500;">Tệp kết quả</th>
                        <th scope="col" style="font-weight: 500;">Loại tác vụ</th>
                        <th scope="col" style="font-weight: 500;">Số trang</th>
                        <th scope="col" style="font-weight: 500;">Dung lượng</th>
                        <th scope="col" style="font-weight: 500;">Thời gian tạo</th>
                        <th scope="col" style="font-weight: 500;">Trạng thái</th>
                        <th scope="col" style="font-weight: 500;" class="text-end">Thao tác</th>
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
                                        <span class="spinner-border spinner-border-sm me-1" style="width: 10px; height: 10px;"></span> Đang xử lý
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="/exports/detail?id=<?php echo $job['id']; ?>" class="btn btn-outline-secondary" title="Xem chi tiết đối chiếu nguồn trang (Snapshot)">
                                        <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-md-inline ms-1">Đối chiếu</span>
                                    </a>
                                    <?php if ($job['status'] === 'completed' && !empty($job['download_token'])): ?>
                                        <button type="button" class="btn btn-outline-primary" title="Xem trước nội dung tệp PDF (Review)" onclick="reviewPdf('/exports/download?token=<?php echo $job['download_token']; ?>&inline=1', '<?php echo htmlspecialchars(addslashes($job['output_name'] ?? 'PDF_Workspace_Export.pdf')); ?>', '/exports/download?token=<?php echo $job['download_token']; ?>')">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                            <span class="d-none d-xl-inline ms-1">Xem trước</span>
                                        </button>
                                        <a href="/exports/download?token=<?php echo $job['download_token']; ?>" class="btn btn-primary" title="Tải xuống tệp" download="<?php echo htmlspecialchars($job['output_name'] ?? 'PDF_Workspace_Export.pdf'); ?>">
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
            'sort' => ($sort ?? '') !== 'created_at_desc' ? $sort : '',
            'per_page' => ($perPage ?? 10) !== 10 ? $perPage : '',
        ]);
        echo render_pagination(
            $currentPage ?? 1, 
            $totalPages ?? 1, 
            $totalItems ?? count($exports), 
            $perPage ?? 10, 
            '/exports', 
            $paginationParams
        ); 
        ?>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>