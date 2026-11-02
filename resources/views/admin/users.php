<?php ob_start(); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Quản lý Người dùng & Phân quyền</h4>
        <p class="text-muted small mb-0">Xem danh sách sinh viên/người dùng, điều chỉnh quyền hạn và quản trị tài khoản hệ thống.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i data-lucide="user-plus" style="width: 15px; height: 15px;"></i>
            <span>Thêm tài khoản mới</span>
        </button>
    </div>
</div>

<!-- Bộ lọc tìm kiếm & Vai trò -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="/admin/users" class="row g-2 align-items-center">
        <div class="col-md-5 col-lg-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Tìm theo tên hoặc địa chỉ email..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-3">
            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả vai trò</option>
                <option value="admin" <?php echo ($role ?? '') === 'admin' ? 'selected' : ''; ?>>Quản trị viên (Admin)</option>
                <option value="user" <?php echo ($role ?? '') === 'user' ? 'selected' : ''; ?>>Người dùng (User)</option>
            </select>
        </div>

        <div class="col-6 col-md-2 col-lg-2">
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>Cũ nhất trước</option>
                <option value="docs_desc" <?php echo ($sort ?? '') === 'docs_desc' ? 'selected' : ''; ?>>Nhiều tài liệu nhất</option>
                <option value="exports_desc" <?php echo ($sort ?? '') === 'exports_desc' ? 'selected' : ''; ?>>Nhiều bản xuất nhất</option>
                <option value="name_asc" <?php echo ($sort ?? '') === 'name_asc' ? 'selected' : ''; ?>>Tên A &rarr; Z</option>
            </select>
        </div>

        <div class="col-12 col-md-2 col-lg-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Lọc</button>
            <?php if (!empty($search) || !empty($role) || ($sort ?? '') !== 'created_at_desc'): ?>
                <a href="/admin/users" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                    <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </a>
            <?php endif; ?>
        </div>
        <input type="hidden" name="per_page" value="<?php echo htmlspecialchars((string)($perPage ?? 10)); ?>">
    </form>
</div>

<!-- Bảng Danh sách Người dùng -->
<div class="card card-custom">
    <?php if (empty($users)): ?>
        <div class="text-center py-5">
            <i data-lucide="user-x" style="width: 44px; height: 44px; color: #94a3b8;" class="mb-3"></i>
            <h6 class="fw-semibold text-dark">Không tìm thấy người dùng phù hợp</h6>
            <p class="text-muted small mb-3">Vui lòng thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.</p>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm">Xóa bộ lọc</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 60px;">ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Tài liệu</th>
                        <th>Bản xuất</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="text-muted small"><strong>#<?php echo $u['id']; ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 34px; height: 34px; font-size: 0.82rem; background: <?php echo ($u['role'] ?? '') === 'admin' ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #6366f1, #8b5cf6)'; ?>;">
                                        <?php echo strtoupper(substr($u['name'] ?? 'U', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($u['name']); ?></div>
                                        <?php if ((int)$u['id'] === auth_id()): ?>
                                            <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.65rem;">Chính bạn</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <?php if (($u['role'] ?? '') === 'admin'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i data-lucide="shield" style="width: 12px; height: 12px;" class="me-1"></i> Quản trị viên
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        <i data-lucide="user" style="width: 12px; height: 12px;" class="me-1"></i> Người dùng
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/documents?user_id=<?php echo $u['id']; ?>" class="badge bg-light text-primary border text-decoration-none">
                                    <?php echo (int)($u['total_docs'] ?? 0); ?> tệp
                                </a>
                            </td>
                            <td>
                                <a href="/admin/exports?user_id=<?php echo $u['id']; ?>" class="badge bg-light text-success border text-decoration-none">
                                    <?php echo (int)($u['total_exports'] ?? 0); ?> lần
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($u['is_active'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i data-lucide="more-horizontal" style="width: 15px; height: 15px;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <!-- Đổi vai trò -->
                                        <li>
                                            <form action="/admin/users/role" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <input type="hidden" name="role" value="<?php echo ($u['role'] ?? '') === 'admin' ? 'user' : 'admin'; ?>">
                                                <button type="submit" class="dropdown-item small d-flex align-items-center gap-2" <?php echo (int)$u['id'] === auth_id() ? 'disabled' : ''; ?>>
                                                    <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i>
                                                    <span>Chuyển thành <?php echo ($u['role'] ?? '') === 'admin' ? 'Người dùng' : 'Quản trị viên'; ?></span>
                                                </button>
                                            </form>
                                        </li>

                                        <!-- Khóa / Mở khóa -->
                                        <li>
                                            <form action="/admin/users/toggle-status" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <button type="submit" class="dropdown-item small d-flex align-items-center gap-2" <?php echo (int)$u['id'] === auth_id() ? 'disabled' : ''; ?>>
                                                    <i data-lucide="<?php echo !empty($u['is_active']) ? 'lock' : 'unlock'; ?>" style="width: 14px; height: 14px;"></i>
                                                    <span><?php echo !empty($u['is_active']) ? 'Khóa tài khoản' : 'Mở khóa tài khoản'; ?></span>
                                                </button>
                                            </form>
                                        </li>

                                        <!-- Đổi mật khẩu -->
                                        <li>
                                            <button type="button" class="dropdown-item small d-flex align-items-center gap-2" onclick="openResetPasswordModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['name'], ENT_QUOTES); ?>')">
                                                <i data-lucide="key" style="width: 14px; height: 14px;"></i>
                                                <span>Đặt lại mật khẩu</span>
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <!-- Xóa tài khoản -->
                                        <li>
                                            <button type="button" class="dropdown-item small text-danger d-flex align-items-center gap-2" <?php echo (int)$u['id'] === auth_id() ? 'disabled' : ''; ?> onclick="openDeleteUserModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['name'], ENT_QUOTES); ?>')">
                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                <span>Xóa người dùng</span>
                                            </button>
                                        </li>
                                    </ul>
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
            'role' => $role ?? '',
            'sort' => ($sort ?? '') !== 'created_at_desc' ? $sort : '',
            'per_page' => ($perPage ?? 10) !== 10 ? $perPage : '',
        ]);
        echo render_pagination(
            $currentPage ?? 1, 
            $totalPages ?? 1, 
            $totalItems ?? count($users), 
            $perPage ?? 10, 
            '/admin/users', 
            $paginationParams
        ); 
        ?>
    <?php endif; ?>
</div>

<!-- Modal Thêm Người dùng Mới -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="/admin/users/create" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i data-lucide="user-plus" style="width: 18px; height: 18px; color: #6366f1;"></i>
                        <span>Thêm Tài khoản Mới</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Họ và tên</label>
                        <input type="text" name="name" class="form-control" required placeholder="Nguyễn Văn A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Mật khẩu khởi tạo</label>
                        <input type="password" name="password" class="form-control" minlength="6" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-dark">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="user">Người dùng (User)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Đặt lại Mật khẩu -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="/admin/users/reset-password" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="user_id" id="resetPasswordUserId">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i data-lucide="key" style="width: 18px; height: 18px; color: #f59e0b;"></i>
                        <span>Đặt lại Mật khẩu cho: <span id="resetPasswordUserName" class="text-primary"></span></span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required placeholder="Nhập mật khẩu mới">
                    </div>
                    <small class="text-muted">Người dùng sẽ sử dụng mật khẩu mới này để đăng nhập ngay sau khi bạn lưu.</small>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning text-dark fw-semibold btn-sm px-3">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xác nhận Xóa Người dùng -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="/admin/users/delete" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="user_id" id="deleteUserId">
                <div class="modal-header border-bottom bg-danger-subtle text-danger">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                        <span>Xác nhận xóa tài khoản</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Bạn có chắc chắn muốn xóa tài khoản của <strong id="deleteUserName"></strong> không?</p>
                    <div class="alert alert-danger small mb-0 py-2">
                        <strong>Lưu ý:</strong> Hành động này sẽ tự động xóa sạch toàn bộ tài liệu nguồn và các phiên xuất PDF liên quan của người dùng này!
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">Xác nhận xóa vĩnh viễn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openResetPasswordModal(id, name) {
        document.getElementById('resetPasswordUserId').value = id;
        document.getElementById('resetPasswordUserName').textContent = name;
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }

    function openDeleteUserModal(id, name) {
        document.getElementById('deleteUserId').value = id;
        document.getElementById('deleteUserName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
