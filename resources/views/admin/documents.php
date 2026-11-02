<?php ob_start(); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Kho Tài liệu Toàn hệ thống</h4>
        <p class="text-muted small mb-0">Theo dõi, kiểm tra và quản lý toàn bộ các tệp PDF nguồn được lưu trữ trong dự án.</p>
    </div>
</div>

<!-- Bộ lọc tìm kiếm & Lọc theo người sở hữu -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="/admin/documents" class="row g-2 align-items-center">
        <div class="col-md-5 col-lg-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm theo tên tệp hoặc mã hash..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-3">
            <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả người sở hữu</option>
                <?php foreach ($allUsers as $usr): ?>
                    <option value="<?php echo $usr['id']; ?>" <?php echo ($filterUserId ?? 0) === (int)$usr['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usr['name']); ?> (<?php echo htmlspecialchars($usr['email']); ?>)
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

        <div class="col-12 col-md-2 col-lg-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Lọc</button>
            <?php if (!empty($search) || !empty($filterUserId) || ($sort ?? '') !== 'created_at_desc'): ?>
                <a href="/admin/documents" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
        <input type="hidden" name="per_page" value="<?php echo htmlspecialchars((string)($perPage ?? 10)); ?>">
    </form>
</div>

<!-- Bảng Danh sách Tài liệu -->
<div class="card card-custom">
    <?php if (empty($documents)): ?>
        <div class="text-center py-5">
            <i data-lucide="folder-x" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-3"></i>
            <h6 class="fw-semibold text-dark">Không tìm thấy tài liệu phù hợp</h6>
            <p class="text-muted small mb-3">Vui lòng thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.</p>
            <a href="/admin/documents" class="btn btn-outline-secondary btn-sm">Xóa bộ lọc</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 60px;">ID</th>
                        <th>Tên tài liệu gốc</th>
                        <th>Người sở hữu</th>
                        <th>Số trang</th>
                        <th>Dung lượng</th>
                        <th>Ngày tải lên</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td class="text-muted small"><strong>#<?php echo $doc['id']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-3 text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                                        <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="<?php echo htmlspecialchars($doc['original_filename']); ?>">
                                            <?php echo htmlspecialchars($doc['original_filename']); ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;"><code><?php echo htmlspecialchars($doc['stored_filename']); ?></code></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small text-dark"><?php echo htmlspecialchars($doc['user_name'] ?? 'Không rõ'); ?></div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($doc['user_email'] ?? ''); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border px-2 py-1"><?php echo (int)$doc['total_pages']; ?> trang</span></td>
                            <td class="text-muted small"><?php echo format_bytes((int)$doc['file_size']); ?></td>
                            <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($doc['created_at'])); ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" title="Xem trước tài liệu PDF (Review)" onclick="reviewPdf('/documents/view?id=<?php echo $doc['id']; ?>', '<?php echo htmlspecialchars(addslashes($doc['original_filename'])); ?>', '/documents/download?id=<?php echo $doc['id']; ?>')">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-lg-inline ms-1">Xem trước</span>
                                    </button>
                                    <a href="/documents/download?id=<?php echo $doc['id']; ?>" class="btn btn-outline-secondary" title="Tải file về máy">
                                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Xóa tài liệu này" onclick="openDeleteDocModal(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars($doc['original_filename'], ENT_QUOTES); ?>')">
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
        $paginationParams = array_filter([
            'q' => $search ?? '',
            'user_id' => !empty($filterUserId) ? $filterUserId : '',
            'sort' => ($sort ?? '') !== 'created_at_desc' ? $sort : '',
            'per_page' => ($perPage ?? 10) !== 10 ? $perPage : '',
        ]);
        echo render_pagination(
            $currentPage ?? 1, 
            $totalPages ?? 1, 
            $totalItems ?? count($documents), 
            $perPage ?? 10, 
            '/admin/documents', 
            $paginationParams
        ); 
        ?>
    <?php endif; ?>
</div>

<!-- Modal Xác nhận Xóa Tài liệu -->
<div class="modal fade" id="deleteDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="/admin/documents/delete" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="document_id" id="deleteDocId">
                <div class="modal-header border-bottom bg-danger-subtle text-danger">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                        <span>Xác nhận xóa tài liệu nguồn</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Bạn có chắc chắn muốn xóa tệp <strong id="deleteDocName"></strong> khỏi máy chủ không?</p>
                    <p class="text-muted small mb-0">Tệp vật lý trong <code>storage/sources/</code> sẽ bị xóa vĩnh viễn.</p>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">Xóa tài liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteDocModal(id, name) {
        document.getElementById('deleteDocId').value = id;
        document.getElementById('deleteDocName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteDocModal')).show();
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
