<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i data-lucide="credit-card" style="width: 24px; height: 24px; color: #f59e0b;"></i>
            <span>Quản lý Đăng ký Gói & Doanh thu</span>
        </h4>
        <p class="text-muted small mb-0">Theo dõi thông tin khách hàng mua gói, thời gian đăng ký, phương thức thanh toán và doanh thu hệ thống.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#createSubscriptionModal">
            <i data-lucide="plus-circle" style="width: 15px; height: 15px;"></i>
            <span>Tạo đơn / Cấp gói thủ công</span>
        </button>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show small mb-4 shadow-xs" role="alert">
        <i data-lucide="check-circle" style="width: 16px; height: 16px;" class="me-1"></i>
        <?php echo htmlspecialchars($success); ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show small mb-4 shadow-xs" role="alert">
        <i data-lucide="alert-circle" style="width: 16px; height: 16px;" class="me-1"></i>
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- 4 Thẻ thống kê tài chính & gói đăng ký -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #10b981, #059669);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Tổng doanh thu</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i data-lucide="dollar-sign" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-success"><?php echo number_format($stats['total_revenue']); ?>đ</h3>
            </div>
            <small class="text-muted">Doanh thu từ các đơn hàng hoàn tất</small>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Tổng lượt đăng ký</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <i data-lucide="shopping-bag" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_subscriptions']); ?></h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">Đơn hàng</span>
            </div>
            <small class="text-muted">Plus: <?php echo $stats['by_tier']['plus']['count']; ?> | Pro: <?php echo $stats['by_tier']['pro']['count']; ?> | VIP: <?php echo $stats['by_tier']['premium']['count']; ?></small>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Gói đang hoạt động</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i data-lucide="user-check" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['active_subscriptions']); ?></h3>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 0.72rem;">Còn hiệu lực</span>
            </div>
            <small class="text-muted">Khách hàng đang sở hữu gói hoạt động</small>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-4 h-100 position-relative overflow-hidden">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #ec4899, #f43f5e);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Tổng Token đã cấp</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">
                    <i data-lucide="coins" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_tokens_granted']); ?></h3>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.72rem;">Tokens</span>
            </div>
            <small class="text-muted">Tổng lượng token nạp qua giao dịch</small>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm & Danh sách đơn đăng ký -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="receipt" style="width: 18px; height: 18px; color: #6366f1;"></i>
            <span>Danh sách Đăng ký Gói Dịch Vụ</span>
            <span class="badge bg-light text-secondary border ms-1"><?php echo number_format($totalItems ?? 0); ?> đơn</span>
        </h6>
        <div class="d-flex gap-2">
            <a href="/admin/subscriptions" class="btn btn-xs btn-outline-secondary">Xóa bộ lọc</a>
        </div>
    </div>

    <form method="GET" action="/admin/subscriptions" class="row g-2 mb-4">
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i data-lucide="search" style="width: 14px; height: 14px;"></i></span>
                <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Mã đơn, Tên, Email khách...">
            </div>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-sm" name="tier">
                <option value="">-- Tất cả Gói --</option>
                <option value="plus" <?php echo ($tier ?? '') === 'plus' ? 'selected' : ''; ?>>Gói Plus (500 Tok)</option>
                <option value="pro" <?php echo ($tier ?? '') === 'pro' ? 'selected' : ''; ?>>Gói Pro (2.000 Tok)</option>
                <option value="premium" <?php echo ($tier ?? '') === 'premium' ? 'selected' : ''; ?>>Gói Premium (∞ Vô hạn)</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-sm" name="cycle">
                <option value="">-- Tất cả Chu kỳ --</option>
                <option value="weekly" <?php echo ($cycle ?? '') === 'weekly' ? 'selected' : ''; ?>>Theo Tuần</option>
                <option value="monthly" <?php echo ($cycle ?? '') === 'monthly' ? 'selected' : ''; ?>>Theo Tháng</option>
                <option value="yearly" <?php echo ($cycle ?? '') === 'yearly' ? 'selected' : ''; ?>>Theo Năm</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-sm" name="status">
                <option value="">-- Trạng thái --</option>
                <option value="completed" <?php echo ($status ?? '') === 'completed' ? 'selected' : ''; ?>>Hoàn tất</option>
                <option value="pending" <?php echo ($status ?? '') === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                <option value="cancelled" <?php echo ($status ?? '') === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-sm" name="sort">
                <option value="created_at_desc" <?php echo ($sort ?? '') === 'created_at_desc' ? 'selected' : ''; ?>>Mới nhất trước</option>
                <option value="created_at_asc" <?php echo ($sort ?? '') === 'created_at_asc' ? 'selected' : ''; ?>>Cũ nhất trước</option>
                <option value="price_desc" <?php echo ($sort ?? '') === 'price_desc' ? 'selected' : ''; ?>>Giá cao nhất</option>
                <option value="tokens_desc" <?php echo ($sort ?? '') === 'tokens_desc' ? 'selected' : ''; ?>>Token nhiều nhất</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-sm btn-dark w-100">Lọc</button>
        </div>
    </form>

    <?php if (empty($subscriptions)): ?>
        <div class="text-center py-5 border rounded-3 bg-light">
            <i data-lucide="inbox" style="width: 42px; height: 42px; color: #94a3b8;" class="mb-2"></i>
            <h6 class="text-secondary fw-semibold">Chưa có bản ghi đăng ký gói nào phù hợp</h6>
            <p class="text-muted small mb-0">Khi khách hàng đặt mua gói trên trang /pricing hoặc Admin cấp gói, thông tin sẽ xuất hiện tại đây.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 130px;">Mã Đơn Hàng</th>
                        <th>Khách hàng</th>
                        <th>Gói cước</th>
                        <th>Chu kỳ</th>
                        <th>Số tiền</th>
                        <th>Tokens cấp</th>
                        <th>Phương thức</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscriptions as $sub): ?>
                        <tr>
                            <td>
                                <strong class="font-monospace text-primary small"><?php echo htmlspecialchars($sub['transaction_code']); ?></strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-2xs" style="width: 32px; height: 32px; font-size: 0.8rem; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                        <?php echo strtoupper(substr($sub['user_name'] ?? 'U', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($sub['user_name']); ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($sub['user_email']); ?> (ID: #<?php echo $sub['user_id']; ?>)</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $tierColors = [
                                        'plus' => 'bg-info-subtle text-info border border-info-subtle',
                                        'pro' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'premium' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    ];
                                    $badge = $tierColors[strtolower($sub['plan_tier'])] ?? 'bg-light text-dark border';
                                ?>
                                <span class="badge <?php echo $badge; ?> text-uppercase px-2 py-1" style="font-size: 0.72rem;">
                                    <?php echo htmlspecialchars($sub['plan_tier']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $cycleLabels = [
                                        'weekly' => '<span class="badge bg-light text-dark border">Tuần</span>',
                                        'monthly' => '<span class="badge bg-primary text-white">Tháng</span>',
                                        'yearly' => '<span class="badge bg-danger text-white">Năm</span>',
                                    ];
                                    echo $cycleLabels[strtolower($sub['billing_cycle'])] ?? htmlspecialchars($sub['billing_cycle']);
                                ?>
                            </td>
                            <td>
                                <strong class="text-dark small"><?php echo number_format((int)$sub['price']); ?>đ</strong>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                    +<?php echo number_format((int)$sub['tokens_granted']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $pmIcons = [
                                        'vietqr' => '<span class="badge bg-primary-subtle text-primary border"><i data-lucide="qr-code" style="width: 12px; height: 12px;" class="me-1"></i>VietQR VPBank</span>',
                                        'momo' => '<span class="badge bg-danger-subtle text-danger border"><i data-lucide="smartphone" style="width: 12px; height: 12px;" class="me-1"></i>Ví MoMo</span>',
                                        'zalopay' => '<span class="badge bg-info-subtle text-info border"><i data-lucide="wallet" style="width: 12px; height: 12px;" class="me-1"></i>ZaloPay/Đa Bank</span>',
                                        'card' => '<span class="badge bg-secondary-subtle text-secondary border"><i data-lucide="credit-card" style="width: 12px; height: 12px;" class="me-1"></i>Thẻ QT</span>',
                                        'manual_admin' => '<span class="badge bg-warning-subtle text-dark border"><i data-lucide="shield" style="width: 12px; height: 12px;" class="me-1"></i>Admin cấp</span>',
                                    ];
                                    echo $pmIcons[$sub['payment_method']] ?? '<span class="badge bg-light text-dark border">' . htmlspecialchars($sub['payment_method']) . '</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($sub['status'] === 'completed'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i data-lucide="check" style="width: 11px; height: 11px;"></i> Hoàn tất
                                    </span>
                                <?php elseif ($sub['status'] === 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                        <i data-lucide="clock" style="width: 11px; height: 11px;"></i> Chờ duyệt
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i data-lucide="x" style="width: 11px; height: 11px;"></i> Đã hủy
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small text-dark fw-medium"><?php echo date('d/m/Y H:i', strtotime($sub['created_at'])); ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    Hạn: <?php echo $sub['expires_at'] ? date('d/m/Y', strtotime($sub['expires_at'])) : 'Vĩnh viễn'; ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border py-1 px-2" type="button" data-bs-toggle="dropdown">
                                        <i data-lucide="more-horizontal" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <button type="button" class="dropdown-item small d-flex align-items-center gap-2" onclick='openViewSubscriptionModal(<?php echo json_encode($sub, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                                <span>Xem chi tiết đơn</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="/admin/subscriptions/status" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="dropdown-item small text-success d-flex align-items-center gap-2" <?php echo $sub['status'] === 'completed' ? 'disabled' : ''; ?>>
                                                    <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                                                    <span>Đánh dấu Hoàn tất</span>
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="/admin/subscriptions/status" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item small text-danger d-flex align-items-center gap-2" <?php echo $sub['status'] === 'cancelled' ? 'disabled' : ''; ?>>
                                                    <i data-lucide="x-circle" style="width: 14px; height: 14px;"></i>
                                                    <span>Hủy giao dịch</span>
                                                </button>
                                            </form>
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
            'tier' => $tier ?? '',
            'status' => $status ?? '',
            'cycle' => $cycle ?? '',
            'sort' => ($sort ?? '') !== 'created_at_desc' ? $sort : '',
            'per_page' => ($perPage ?? 10) !== 10 ? $perPage : '',
        ]);
        echo render_pagination(
            $currentPage ?? 1, 
            $totalPages ?? 1, 
            $totalItems ?? count($subscriptions), 
            $perPage ?? 10, 
            '/admin/subscriptions', 
            $paginationParams
        ); 
        ?>
    <?php endif; ?>
</div>

<!-- Khối Lịch sử biến động Token toàn hệ thống gần đây -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="coins" style="width: 18px; height: 18px; color: #ea580c;"></i>
            <span>Nhật ký Dòng tiền Token toàn hệ thống (Gần đây)</span>
        </h6>
        <span class="text-muted small">Cập nhật thời gian thực</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr class="text-muted">
                    <th>Thời gian</th>
                    <th>Người dùng</th>
                    <th>Hành động</th>
                    <th>Nội dung chi tiết</th>
                    <th>Biến động</th>
                    <th>Số dư sau GD</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tokenTransactions)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-3 text-muted">Chưa có giao dịch biến động token nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tokenTransactions as $tx): ?>
                        <tr>
                            <td class="text-muted"><?php echo date('d/m/Y H:i:s', strtotime($tx['created_at'])); ?></td>
                            <td>
                                <strong class="text-dark"><?php echo htmlspecialchars($tx['user_name']); ?></strong>
                                <span class="text-muted" style="font-size: 0.72rem;">(<?php echo htmlspecialchars($tx['user_email']); ?>)</span>
                            </td>
                            <td>
                                <?php if ($tx['action'] === 'purchase'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Nạp gói</span>
                                <?php elseif ($tx['action'] === 'export' || $tx['action'] === 'merge' || $tx['action'] === 'split'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Xuất PDF</span>
                                <?php elseif ($tx['action'] === 'admin_adjust'): ?>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Admin cấp</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($tx['action']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($tx['description'] ?? ''); ?></td>
                            <td>
                                <?php if ((int)$tx['amount'] > 0): ?>
                                    <strong class="text-success">+<?php echo number_format((int)$tx['amount']); ?></strong>
                                <?php else: ?>
                                    <strong class="text-danger"><?php echo number_format((int)$tx['amount']); ?></strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold"><?php echo number_format((int)$tx['balance_after']); ?> Tokens</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tạo Đơn Gói / Cấp Gói Thủ Công -->
<div class="modal fade" id="createSubscriptionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="/admin/subscriptions/create" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header border-bottom bg-primary-subtle text-primary">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                        <span>Tạo Đơn Hàng / Cấp Gói Cho Khách</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Chọn Khách Hàng</label>
                        <select class="form-select form-select-sm" name="user_id" required>
                            <option value="">-- Chọn một người dùng --</option>
                            <?php foreach ($allUsers as $u): ?>
                                <option value="<?php echo $u['id']; ?>">
                                    #<?php echo $u['id']; ?> - <?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['email']); ?>) [Gói: <?php echo strtoupper($u['plan_tier'] ?? 'free'); ?> - <?php echo number_format((int)($u['tokens'] ?? 0)); ?> tok]
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Gói Cước Cấp</label>
                            <select class="form-select form-select-sm" name="plan_tier" id="manualPlanTier" onchange="updateManualDefaults()">
                                <option value="plus">Gói PLUS (500 Tokens)</option>
                                <option value="pro" selected>Gói PRO (2.000 Tokens)</option>
                                <option value="premium">Gói PREMIUM (∞ Vô hạn)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Chu Kỳ Đăng Ký</label>
                            <select class="form-select form-select-sm" name="billing_cycle" id="manualCycle" onchange="updateManualDefaults()">
                                <option value="weekly">Theo Tuần (7 ngày)</option>
                                <option value="monthly" selected>Theo Tháng (30 ngày)</option>
                                <option value="yearly">Theo Năm (365 ngày)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Số Tiền Thu (VNĐ)</label>
                            <input type="number" class="form-control form-control-sm" name="price" id="manualPrice" value="169000" step="1000">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Số Token Cộng Thêm</label>
                            <input type="number" class="form-control form-control-sm" name="tokens_granted" id="manualTokens" value="2000" step="50">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Phương Thức Thanh Toán Ghi Nhận</label>
                        <select class="form-select form-select-sm" name="payment_method">
                            <option value="vietqr">VietQR (VPBank Chuyển khoản)</option>
                            <option value="momo">Ví MoMo</option>
                            <option value="zalopay">ZaloPay / QR Đa Ngân Hàng</option>
                            <option value="card">Thẻ Quốc Tế</option>
                            <option value="manual_admin" selected>Quản trị viên cấp tặng / Khuyến mãi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Kích hoạt & Ghi nhận đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xem Chi Tiết Đơn Hàng -->
<div class="modal fade" id="viewSubscriptionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="file-text" style="width: 18px; height: 18px; color: #6366f1;"></i>
                    <span>Chi Tiết Đơn Hàng <span id="vSubCode" class="text-primary font-monospace"></span></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 small">
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Khách hàng:</span>
                        <strong class="text-dark" id="vSubUser"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Email:</span>
                        <span class="text-dark" id="vSubEmail"></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Số dư Token hiện tại:</span>
                        <strong class="text-success" id="vSubUserTokens"></strong>
                    </div>
                </div>

                <div class="list-group list-group-flush border rounded-3 mb-3">
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Gói cước:</span>
                        <strong class="text-primary text-uppercase" id="vSubTier"></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Chu kỳ:</span>
                        <span class="text-dark fw-medium" id="vSubCycle"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Số tiền thanh toán:</span>
                        <strong class="text-danger fs-6" id="vSubPrice"></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Tokens cấp vào tài khoản:</span>
                        <strong class="text-success" id="vSubTokens"></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Kênh thanh toán:</span>
                        <span class="text-dark" id="vSubMethod"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Trạng thái:</span>
                        <span id="vSubStatus"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Thời gian đặt mua:</span>
                        <span class="text-dark" id="vSubCreated"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Hạn gói cước:</span>
                        <span class="text-dark" id="vSubExpires"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    const manualPrices = {
        plus: { weekly: { price: 29000, tok: 500 }, monthly: { price: 79000, tok: 500 }, yearly: { price: 699000, tok: 6000 } },
        pro: { weekly: { price: 59000, tok: 2000 }, monthly: { price: 169000, tok: 2000 }, yearly: { price: 1490000, tok: 24000 } },
        premium: { weekly: { price: 99000, tok: 10000 }, monthly: { price: 299000, tok: 10000 }, yearly: { price: 2590000, tok: 120000 } },
    };

    function updateManualDefaults() {
        const tier = document.getElementById('manualPlanTier').value;
        const cycle = document.getElementById('manualCycle').value;
        const cfg = manualPrices[tier] && manualPrices[tier][cycle] ? manualPrices[tier][cycle] : { price: 0, tok: 0 };
        document.getElementById('manualPrice').value = cfg.price;
        document.getElementById('manualTokens').value = cfg.tok;
    }

    function openViewSubscriptionModal(sub) {
        document.getElementById('vSubCode').textContent = sub.transaction_code;
        document.getElementById('vSubUser').textContent = sub.user_name;
        document.getElementById('vSubEmail').textContent = sub.user_email;
        document.getElementById('vSubUserTokens').textContent = Number(sub.user_current_tokens || 0).toLocaleString() + ' Tokens';
        document.getElementById('vSubTier').textContent = (sub.plan_tier || '').toUpperCase();
        document.getElementById('vSubCycle').textContent = sub.billing_cycle === 'weekly' ? 'Theo Tuần' : (sub.billing_cycle === 'yearly' ? 'Theo Năm' : 'Theo Tháng');
        document.getElementById('vSubPrice').textContent = Number(sub.price || 0).toLocaleString() + 'đ';
        document.getElementById('vSubTokens').textContent = '+' + Number(sub.tokens_granted || 0).toLocaleString() + ' Tokens';
        document.getElementById('vSubMethod').textContent = sub.payment_method;
        document.getElementById('vSubStatus').innerHTML = sub.status === 'completed' ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Hoàn tất</span>' : '<span class="badge bg-warning-subtle text-dark border">Chờ xử lý</span>';
        document.getElementById('vSubCreated').textContent = sub.created_at;
        document.getElementById('vSubExpires').textContent = sub.expires_at || 'Không thời hạn';

        new bootstrap.Modal(document.getElementById('viewSubscriptionModal')).show();
        lucide.createIcons();
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/admin.php';
?>
