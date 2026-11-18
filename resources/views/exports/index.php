<?php ob_start(); ?>

<!-- Header Hero -->
<div class="card card-aurora p-4 mb-4 position-relative overflow-hidden" style="border-left: 5px solid #7c3aed !important;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 rounded-pill bg-purple-subtle text-primary border border-purple-subtle" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                <i data-lucide="history" style="width: 14px; height: 14px; color: #7c3aed;"></i>
                <span class="fw-bold" style="font-size: 0.76rem;">Nhật Ký Bản Xuất &bull; Bản chụp Snapshot bất biến SHA-256</span>
            </div>
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Lịch sử xuất tài liệu PDF</h3>
            <p class="text-muted small mb-0">Theo dõi toàn bộ các phiên xuất thành phẩm từ ghép, tách hoặc PDF Editor, đối chiếu nguồn trang và tải lại tức thì.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if (($totalItems ?? 0) > 10 || ($perPage ?? 10) !== 10): ?>
                <div class="d-flex align-items-center gap-1 text-muted small me-2">
                    <span>Hiển thị:</span>
                    <select class="form-select form-select-sm py-1 rounded-3" style="width: auto; font-size: 0.8rem;" onchange="
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
            <a href="/editor" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 rounded-pill px-3 shadow-2xs hover-lift" style="border-color: #7c3aed; color: #7c3aed;">
                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                <span>Mở Editor</span>
            </a>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm, Tác vụ, Trạng thái & Sắp xếp -->
<div class="card card-aurora p-3 mb-4">
    <form method="GET" action="/exports" class="row g-2 align-items-center">
        <!-- Ô tìm kiếm từ khóa hoặc mã ID -->
        <div class="col-md-4 col-lg-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                    <i data-lucide="search" style="width: 16px; height: 16px; color: #7c3aed;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 py-2 rounded-end-3" placeholder="Tìm theo tên tệp xuất hoặc mã #ID..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="font-size: 0.85rem;">
            </div>
        </div>

        <!-- Lọc Loại tác vụ -->
        <div class="col-6 col-md-2 col-lg-2">
            <select name="type" class="form-select form-select-sm py-2 rounded-3" onchange="this.form.submit()" style="font-size: 0.85rem; font-weight: 500;">
                <option value="">Tất cả tác vụ</option>
                <option value="merge" <?php echo ($type ?? '') === 'merge' ? 'selected' : ''; ?>>📗 Ghép PDF</option>
                <option value="split" <?php echo ($type ?? '') === 'split' ? 'selected' : ''; ?>>📙 Tách PDF</option>
                <option value="editor" <?php echo ($type ?? '') === 'editor' ? 'selected' : ''; ?>>📕 PDF Editor</option>
            </select>
        </div>

        <!-- Lọc Trạng thái -->
        <div class="col-6 col-md-2 col-lg-2">
            <select name="status" class="form-select form-select-sm py-2 rounded-3" onchange="this.form.submit()" style="font-size: 0.85rem; font-weight: 500;">
                <option value="">Tất cả trạng thái</option>
                <option value="completed" <?php echo ($status ?? '') === 'completed' ? 'selected' : ''; ?>>✓ Thành công</option>
                <option value="failed" <?php echo ($status ?? '') === 'failed' ? 'selected' : ''; ?>>✕ Thất bại</option>
            </select>
        </div>

        <!-- Sắp xếp -->
        <div class="col-6 col-md-2 col-lg-2">
            <select name="sort" class="form-select form-select-sm py-2 rounded-3" onchange="this.form.submit()" style="font-size: 0.85rem; font-weight: 500;">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>⚡ Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>🕒 Cũ nhất trước</option>
                <option value="size_desc" <?php echo ($sort ?? '') === 'size_desc' ? 'selected' : ''; ?>>💾 Dung lượng lớn</option>
                <option value="pages_desc" <?php echo ($sort ?? '') === 'pages_desc' ? 'selected' : ''; ?>>📄 Nhiều trang nhất</option>
                <option value="name_asc" <?php echo ($sort ?? '') === 'name_asc' ? 'selected' : ''; ?>>🔤 Tên tệp A &rarr; Z</option>
            </select>
        </div>

        <!-- Nút tìm kiếm & Xóa lọc -->
        <div class="col-6 col-md-2 col-lg-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1 rounded-3 fw-bold" style="background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%); border: none;">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                <span>Lọc</span>
            </button>
            <?php if (!empty($search) || !empty($type) || !empty($status) || ($sort ?? '') !== 'created_at_desc'): ?>
                <a href="/exports" class="btn btn-sm btn-outline-secondary rounded-3" title="Xóa toàn bộ bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
        <input type="hidden" name="per_page" value="<?php echo htmlspecialchars((string)($perPage ?? 10)); ?>">
    </form>
</div>

<div class="card card-aurora p-4">
    <?php if (empty($exports)): ?>
        <?php if (!empty($search) || !empty($type) || !empty($status)): ?>
            <div class="text-center py-5">
                <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                    <i data-lucide="search-x" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Không tìm thấy bản xuất phù hợp</h6>
                <p class="text-muted small mb-3">
                    Không có kết quả nào khớp với 
                    <?php if (!empty($search)): ?>từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
                    <?php if (!empty($type)): ?><span class="badge bg-light text-secondary border ms-1">Tác vụ: <?php echo htmlspecialchars($type); ?></span><?php endif; ?>
                    <?php if (!empty($status)): ?><span class="badge bg-light text-secondary border ms-1">Trạng thái: <?php echo htmlspecialchars($status); ?></span><?php endif; ?>.
                </p>
                <a href="/exports" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Xóa bộ lọc</a>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                    <i data-lucide="history" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Chưa có lịch sử xuất file</h6>
                <p class="text-muted small mb-3">Các tài liệu sau khi bạn ghép, tách hoặc biên tập từ PDF Editor sẽ xuất hiện tại đây.</p>
                <a href="/merge" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm hover-lift">Bắt đầu ghép PDF ngay</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small" style="font-size: 0.78rem;">
                        <th scope="col" style="font-weight: 600; width: 65px;">Mã</th>
                        <th scope="col" style="font-weight: 600;">Tệp kết quả</th>
                        <th scope="col" style="font-weight: 600;">Loại tác vụ</th>
                        <th scope="col" style="font-weight: 600;">Số trang</th>
                        <th scope="col" style="font-weight: 600;">Dung lượng</th>
                        <th scope="col" style="font-weight: 600;">Thời gian tạo</th>
                        <th scope="col" style="font-weight: 600;">Trạng thái</th>
                        <th scope="col" style="font-weight: 600;" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exports as $job): ?>
                        <tr>
                            <td class="text-muted small"><strong>#<?php echo $job['id']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 text-white shadow-sm flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: <?php echo $job['status'] === 'completed' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)'; ?>;">
                                        <i data-lucide="<?php echo $job['status'] === 'completed' ? 'file-check' : 'file-x'; ?>" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 260px;" title="<?php echo htmlspecialchars($job['output_name'] ?? 'Tệp xuất #' . $job['id']); ?>">
                                            <?php echo htmlspecialchars($job['output_name'] ?? 'Tệp xuất #' . $job['id']); ?>
                                        </div>
                                        <?php if (!empty($job['stored_filename'])): ?>
                                            <div class="text-muted" style="font-size: 0.73rem;"><code><?php echo htmlspecialchars(substr($job['stored_filename'], 0, 16)); ?>...</code></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $badgeStyle = match($job['job_type']) {
                                    'editor' => 'background: rgba(168, 85, 247, 0.12); color: #7c3aed; border-color: rgba(168, 85, 247, 0.3);',
                                    'merge' => 'background: rgba(16, 185, 129, 0.12); color: #059669; border-color: rgba(16, 185, 129, 0.3);',
                                    'split' => 'background: rgba(244, 63, 94, 0.12); color: #e11d48; border-color: rgba(244, 63, 94, 0.3);',
                                    default => 'background: #f1f5f9; color: #475569; border-color: #e2e8f0;'
                                };
                                ?>
                                <span class="badge border text-uppercase" style="font-size: 0.7rem; <?php echo $badgeStyle; ?>">
                                    <?php echo htmlspecialchars($job['job_type']); ?>
                                </span>
                            </td>
                            <td class="small fw-semibold"><?php echo !empty($job['output_pages']) ? (int)$job['output_pages'] . ' trang' : '-'; ?></td>
                            <td class="text-muted small fw-medium"><?php echo !empty($job['file_size']) ? format_bytes((int)$job['file_size']) : '-'; ?></td>
                            <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($job['created_at'])); ?></td>
                            <td>
                                <?php if ($job['status'] === 'completed'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.74rem;">
                                        <i data-lucide="check" style="width: 12px; height: 12px;" class="me-1"></i> Thành công
                                    </span>
                                <?php elseif ($job['status'] === 'failed'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" title="<?php echo htmlspecialchars($job['error_message'] ?? 'Lỗi'); ?>" style="font-size: 0.74rem;">
                                        <i data-lucide="alert-circle" style="width: 12px; height: 12px;" class="me-1"></i> Thất bại
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size: 0.74rem;">
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
