<?php ob_start(); ?>

<!-- Header & Quick Actions -->
<div class="card card-aurora p-4 mb-4 position-relative overflow-hidden" style="border-left: 5px solid #0284c7 !important;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 rounded-pill bg-info-subtle text-info border border-info-subtle">
                <i data-lucide="folder-check" style="width: 14px; height: 14px; color: #0284c7;"></i>
                <span class="fw-bold" style="font-size: 0.76rem;">Kho Lưu Trữ PDF Nguồn &bull; Lưu trữ an toàn & mã hóa SHA-256</span>
            </div>
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Tài liệu của tôi</h3>
            <p class="text-muted small mb-0">Quản lý, tìm kiếm và phân loại các tệp PDF gốc sẵn sàng đưa vào các tác vụ ghép, tách hoặc biên tập.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-2 rounded-pill px-4 shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#uploadModal" style="background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%); border: none;">
                <i data-lucide="upload-cloud" style="width: 15px; height: 15px;"></i>
                <span class="fw-bold">Tải tệp PDF lên</span>
            </button>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm & Sắp xếp -->
<div class="card card-aurora p-3 mb-4">
    <form method="GET" action="/documents" class="row g-2 align-items-center">
        <div class="col-md-7 col-lg-8">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                    <i data-lucide="search" style="width: 16px; height: 16px; color: #0284c7;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 py-2 rounded-end-3" placeholder="Tìm kiếm theo tên tài liệu..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="font-size: 0.85rem;">
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <select name="sort" class="form-select form-select-sm py-2 rounded-3" onchange="this.form.submit()" style="font-size: 0.85rem; font-weight: 500;">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>⚡ Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>🕒 Cũ nhất trước</option>
                <option value="name_asc" <?php echo ($sort ?? '') === 'name_asc' ? 'selected' : ''; ?>>🔤 Tên A &rarr; Z</option>
                <option value="name_desc" <?php echo ($sort ?? '') === 'name_desc' ? 'selected' : ''; ?>>🔤 Tên Z &rarr; A</option>
                <option value="size_desc" <?php echo ($sort ?? '') === 'size_desc' ? 'selected' : ''; ?>>💾 Dung lượng lớn nhất</option>
                <option value="pages_desc" <?php echo ($sort ?? '') === 'pages_desc' ? 'selected' : ''; ?>>📄 Nhiều trang nhất</option>
            </select>
        </div>
        <div class="col-md-2 col-lg-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-bold" style="border-color: #0284c7; color: #0284c7;">Lọc</button>
            <?php if (!empty($search)): ?>
                <a href="/documents" class="btn btn-sm btn-outline-secondary rounded-3" title="Xóa bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Danh sách Tài liệu -->
<div class="card card-aurora p-4">
    <?php if (empty($documents)): ?>
        <div class="text-center py-5">
            <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                <i data-lucide="folder-search" style="width: 48px; height: 48px; color: #94a3b8;"></i>
            </div>
            <?php if (!empty($search)): ?>
                <h6 class="fw-bold text-dark mb-1">Không tìm thấy tài liệu phù hợp</h6>
                <p class="text-muted small mb-3">Không có tệp PDF nào khớp với từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>".</p>
                <a href="/documents" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Xem tất cả tài liệu</a>
            <?php else: ?>
                <h6 class="fw-bold text-dark mb-1">Chưa có tài liệu PDF nào trong kho</h6>
                <p class="text-muted small mb-3">Bắt đầu bằng cách kéo thả hoặc tải lên các tệp PDF đầu tiên của bạn.</p>
                <button class="btn btn-primary btn-sm rounded-pill px-4 hover-lift shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal" style="background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%); border: none;">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;" class="me-1"></i>
                    <span>Tải tệp lên ngay</span>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small" style="font-size: 0.78rem;">
                        <th scope="col" style="font-weight: 600; width: 45px;">#</th>
                        <th scope="col" style="font-weight: 600;">Tên tài liệu</th>
                        <th scope="col" style="font-weight: 600;">Quy mô</th>
                        <th scope="col" style="font-weight: 600;">Dung lượng</th>
                        <th scope="col" style="font-weight: 600;">Ngày nạp</th>
                        <th scope="col" style="font-weight: 600;" class="text-end">Tác vụ nhanh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $index => $doc): ?>
                        <tr>
                            <td class="text-muted small fw-semibold"><?php echo (($currentPage ?? 1) - 1) * ($perPage ?? 10) + $index + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 text-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);">
                                        <i data-lucide="file-text" style="width: 19px; height: 19px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 320px;" title="<?php echo htmlspecialchars($doc['original_filename']); ?>">
                                            <?php echo htmlspecialchars($doc['original_filename']); ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.73rem;">Mã lưu trữ: <code class="text-primary"><?php echo htmlspecialchars(substr($doc['stored_filename'], 0, 16)); ?>...</code></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.74rem;">
                                    <i data-lucide="book-open" style="width: 12px; height: 12px;" class="me-1"></i>
                                    <?php echo (int)$doc['total_pages']; ?> trang
                                </span>
                            </td>
                            <td class="text-muted small fw-medium" style="font-size: 0.78rem;"><?php echo format_bytes((int)$doc['file_size']); ?></td>
                            <td class="text-muted small" style="font-size: 0.78rem;"><?php echo date('d/m/Y H:i', strtotime($doc['created_at'])); ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" title="Xem trước tài liệu PDF (Review)" onclick="reviewPdf('/documents/view?id=<?php echo $doc['id']; ?>', '<?php echo htmlspecialchars(addslashes($doc['original_filename'])); ?>', '/documents/download?id=<?php echo $doc['id']; ?>')">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-lg-inline ms-1">Xem trước</span>
                                    </button>
                                    <a href="/split?doc_id=<?php echo $doc['id']; ?>" class="btn btn-outline-danger" title="Tách tài liệu này">
                                        <i data-lucide="split" style="width: 14px; height: 14px;"></i>
                                        <span class="d-none d-xl-inline ms-1">Tách</span>
                                    </a>
                                    <a href="/editor?doc_id=<?php echo $doc['id']; ?>" class="btn btn-outline-secondary" title="Mở trong Editor">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
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
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-body p-4 text-center">
                <div class="p-3 bg-danger-subtle text-danger rounded-circle d-inline-flex mb-3">
                    <i data-lucide="alert-triangle" style="width: 32px; height: 32px; color: #ef4444;"></i>
                </div>
                <h6 class="fw-bold mb-2 text-dark">Xác nhận xóa tài liệu?</h6>
                <p class="text-muted small mb-4" id="deleteDocName"></p>
                <form action="/documents/delete" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" id="deleteDocId">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold">Xác nhận xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteDocId').value = id;
        document.getElementById('deleteDocName').textContent = 'Tệp "' + name + '" sẽ bị xóa vĩnh viễn khỏi không gian lưu trữ.';
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
