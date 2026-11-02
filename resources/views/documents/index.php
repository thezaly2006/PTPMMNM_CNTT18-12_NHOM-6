<?php ob_start(); ?>

<!-- Header & Quick Actions -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Tài liệu của tôi</h4>
        <p class="text-muted small mb-0">Quản lý và tổ chức các tệp PDF gốc được lưu trữ trong không gian làm việc.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i>
            <span>Tải tệp PDF lên</span>
        </button>
    </div>
</div>

<!-- Bộ lọc tìm kiếm & Sắp xếp -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="/documents" class="row g-2 align-items-center">
        <div class="col-md-7 col-lg-8">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm kiếm theo tên tài liệu..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>Cũ nhất trước</option>
                <option value="name_asc" <?php echo ($sort ?? '') === 'name_asc' ? 'selected' : ''; ?>>Tên A &rarr; Z</option>
                <option value="name_desc" <?php echo ($sort ?? '') === 'name_desc' ? 'selected' : ''; ?>>Tên Z &rarr; A</option>
                <option value="size_desc" <?php echo ($sort ?? '') === 'size_desc' ? 'selected' : ''; ?>>Dung lượng lớn nhất</option>
                <option value="pages_desc" <?php echo ($sort ?? '') === 'pages_desc' ? 'selected' : ''; ?>>Nhiều trang nhất</option>
            </select>
        </div>
        <div class="col-md-2 col-lg-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Lọc</button>
            <?php if (!empty($search)): ?>
                <a href="/documents" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Danh sách Tài liệu -->
<div class="card card-custom">
    <?php if (empty($documents)): ?>
        <div class="text-center py-5">
            <i data-lucide="folder-search" style="width: 48px; height: 48px; color: #94a3b8;" class="mb-3"></i>
            <?php if (!empty($search)): ?>
                <h6 class="fw-semibold text-dark">Không tìm thấy tài liệu phù hợp</h6>
                <p class="text-muted small mb-3">Không có tệp PDF nào khớp với từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>".</p>
                <a href="/documents" class="btn btn-outline-secondary btn-sm">Xem tất cả tài liệu</a>
            <?php else: ?>
                <h6 class="fw-semibold text-dark">Chưa có tài liệu PDF nào</h6>
                <p class="text-muted small mb-3">Bắt đầu bằng cách kéo thả hoặc chọn tệp PDF tải lên.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;" class="me-1"></i>
                    <span>Tải tệp ngay</span>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th scope="col" style="font-weight: 500; width: 40px;">#</th>
                        <th scope="col" style="font-weight: 500;">Tên tệp</th>
                        <th scope="col" style="font-weight: 500;">Số trang</th>
                        <th scope="col" style="font-weight: 500;">Kích thước</th>
                        <th scope="col" style="font-weight: 500;">Ngày nạp</th>
                        <th scope="col" style="font-weight: 500;" class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $index => $doc): ?>
                        <tr>
                            <td class="text-muted small"><?php echo (($currentPage ?? 1) - 1) * ($perPage ?? 10) + $index + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 text-white shadow-sm flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);">
                                        <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 320px;" title="<?php echo htmlspecialchars($doc['original_filename']); ?>">
                                            <?php echo htmlspecialchars($doc['original_filename']); ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Mã: <code><?php echo htmlspecialchars($doc['stored_filename']); ?></code></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i data-lucide="book-open" style="width: 12px; height: 12px;" class="me-1"></i>
                                    <?php echo (int)$doc['total_pages']; ?> trang
                                </span>
                            </td>
                            <td class="text-muted small"><?php echo format_bytes((int)$doc['file_size']); ?></td>
                            <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($doc['created_at'])); ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" title="Xem trước tài liệu PDF (Review)" onclick="reviewPdf('/documents/view?id=<?php echo $doc['id']; ?>', '<?php echo htmlspecialchars(addslashes($doc['original_filename'])); ?>', '/documents/download?id=<?php echo $doc['id']; ?>')">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-lg-inline ms-1">Xem trước</span>
                                    </button>
                                    <a href="/editor?doc_id=<?php echo $doc['id']; ?>" class="btn btn-outline-secondary" title="Mở trong Editor">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-md-inline ms-1">Chỉnh sửa</span>
                                    </a>
                                    <a href="/documents/download?id=<?php echo $doc['id']; ?>" class="btn btn-outline-secondary" title="Tải về máy">
                                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Xóa tài liệu" onclick="confirmDelete(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars(addslashes($doc['original_filename'])); ?>')">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php 
        $queryParams = [];
        if (!empty($search)) $queryParams['q'] = $search;
        if (!empty($sort)) $queryParams['sort'] = $sort;
        if (!empty($perPage) && $perPage !== 10) $queryParams['per_page'] = $perPage;
        echo render_pagination($currentPage ?? 1, $totalPages ?? 1, $totalItems ?? count($documents), $perPage ?? 10, '/documents', $queryParams); 
        ?>
    <?php endif; ?>
</div>

<!-- Modal Xác nhận Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="p-3 bg-danger-subtle text-danger rounded-circle d-inline-flex mb-3">
                    <i data-lucide="alert-triangle" style="width: 28px; height: 28px;"></i>
                </div>
                <h6 class="fw-bold mb-2">Xác nhận xóa tài liệu?</h6>
                <p class="text-muted small mb-4" id="deleteDocName"></p>
                <form action="/documents/delete" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" id="deleteDocId">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-sm btn-danger">Xác nhận xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteDocId').value = id;
        document.getElementById('deleteDocName').textContent = 'Tệp "' + name + '" sẽ bị xóa vĩnh viễn khỏi hệ thống.';
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
