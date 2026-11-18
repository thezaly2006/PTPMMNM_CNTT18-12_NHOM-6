<?php ob_start(); ?>

<style>
    .pricing-card {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 2px solid rgba(226, 232, 240, 0.85);
        border-radius: 22px;
        transition: all 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
    }
    .pricing-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px -6px rgba(15, 23, 42, 0.1);
    }
    .pricing-card.tier-free:hover {
        border-color: #94a3b8;
    }
    .pricing-card.tier-plus:hover {
        border-color: #0284c7;
        box-shadow: 0 20px 40px -6px rgba(2, 132, 199, 0.18);
    }
    .pricing-card.tier-pro {
        border-color: #7c3aed;
        box-shadow: 0 10px 30px -4px rgba(124, 58, 237, 0.16);
    }
    .pricing-card.tier-pro:hover {
        border-color: #6d28d9;
        box-shadow: 0 22px 45px -6px rgba(124, 58, 237, 0.28);
    }
    .pricing-card.tier-premium {
        border-color: #f59e0b;
        background: linear-gradient(180deg, rgba(254, 243, 199, 0.3) 0%, rgba(255, 255, 255, 0.95) 100%);
    }
    .pricing-card.tier-premium:hover {
        border-color: #d97706;
        box-shadow: 0 22px 45px -6px rgba(245, 158, 11, 0.26);
    }
    .pricing-badge-hot {
        background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        padding: 6px 18px;
        border-radius: 0 0 14px 14px;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.35);
        z-index: 3;
    }
    .cycle-pill-btn {
        padding: 9px 24px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.88rem;
        border: none;
        background: transparent;
        color: #64748b;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .cycle-pill-btn:hover {
        color: #0f172a;
    }
    .cycle-pill-btn.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    }
    .qr-preview-img {
        transition: transform 0.2s ease;
    }
    .qr-preview-img:hover {
        transform: scale(1.02);
    }
</style>

<!-- Banner thông tin tài khoản hiện tại & Số dư Token -->
<div class="card card-aurora p-4 p-md-5 mb-4 border-0 text-white position-relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #3730a3 0%, #4f46e5 40%, #7c3aed 75%, #db2777 100%);">
    <!-- Ambient Glow Orbs -->
    <div style="position: absolute; right: -40px; top: -40px; width: 260px; height: 260px; background: radial-gradient(circle, rgba(245, 158, 11, 0.35) 0%, transparent 70%); border-radius: 50%; filter: blur(35px); pointer-events: none;"></div>
    <div style="position: absolute; left: 40%; bottom: -50px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(6, 182, 212, 0.3) 0%, transparent 70%); border-radius: 50%; filter: blur(35px); pointer-events: none;"></div>

    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge text-white px-3 py-1 rounded-pill fw-bold" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(8px); font-size: 0.76rem;">
                    <i data-lucide="sparkles" style="width: 13px; height: 13px; color: #fde047;" class="me-1"></i>
                    Không gian nạp Token & Nâng cấp Gói
                </span>
                <?php if (auth_check()): ?>
                    <?php 
                        $tier = $user['plan_tier'] ?? 'free';
                        $tierBadge = match($tier) {
                            'plus' => 'bg-info text-white',
                            'pro' => 'bg-warning text-dark',
                            'premium' => 'bg-danger text-white',
                            default => 'bg-secondary text-white',
                        };
                    ?>
                    <span class="badge <?php echo $tierBadge; ?> px-2 py-1 rounded-pill fw-bold shadow-2xs" style="font-size: 0.72rem;">
                        GÓI <?php echo strtoupper($tier); ?>
                    </span>
                <?php endif; ?>
            </div>

            <h2 class="fw-bold mb-2 text-white" style="letter-spacing: -0.02em;">
                <?php if (auth_check()): ?>
                    Số dư hiện tại: <span class="text-warning fw-extrabold" id="heroUserTokens"><?php echo (is_admin() || has_unlimited_tokens($user)) ? 'Vô hạn (∞)' : number_format((int)($user['tokens'] ?? 0)); ?></span> Tokens
                    <?php if (has_unlimited_tokens($user) && !empty($user['plan_expires_at'])): ?>
                        <span class="badge bg-white text-dark ms-2 fw-semibold" style="font-size: 0.72rem;">Hạn dùng: 00:00 ngày <?php echo date('d/m/Y', strtotime($user['plan_expires_at'])); ?> (UTC+7)</span>
                    <?php endif; ?>
                <?php else: ?>
                    Đăng nhập để nhận ngay <span class="text-warning fw-extrabold">50 Tokens Khởi đầu</span>!
                <?php endif; ?>
            </h2>
            <p class="text-white small mb-0" style="max-width: 680px; opacity: 0.94; line-height: 1.6;">
                Tài khoản Miễn phí có <strong>50 Tokens</strong> (tiêu hao 5 tokens/lượt xuất). Khi đăng ký bất kỳ gói nào (<strong>Plus, Pro, Premium</strong>), bạn được cấp quyền <strong class="text-warning">VÔ HẠN TOKENS (∞)</strong> trong suốt thời hạn gói, tính trọn vẹn đến mốc <strong>00:00:00 (UTC+7)</strong>!
            </p>
        </div>

        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <?php if (!auth_check()): ?>
                <button type="button" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm hover-lift" onclick="window.showRequireLoginModal ? window.showRequireLoginModal() : window.location.href='/login'">
                    <i data-lucide="log-in" style="width: 16px; height: 16px;" class="me-1"></i>
                    Đăng nhập để xem Token
                </button>
            <?php else: ?>
                <div class="p-3 rounded-4 d-inline-block text-start shadow-sm" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.28); backdrop-filter: blur(12px);">
                    <div class="small text-white-50">Tài khoản thanh toán:</div>
                    <div class="fw-bold text-white"><?php echo htmlspecialchars($user['name'] ?? 'Bạn'); ?></div>
                    <div class="text-white-50" style="font-size: 0.75rem;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bộ chuyển đổi chu kỳ: Theo Tuần | Theo Tháng | Theo Năm -->
<div class="text-center mb-5">
    <h3 class="fw-bold text-dark mb-2" style="letter-spacing: -0.02em;">Chọn gói dịch vụ phù hợp với bạn</h3>
    <p class="text-muted small mb-4">Linh hoạt đăng ký theo Tuần, Tháng hoặc Năm để tiết kiệm chi phí tối đa</p>

    <div class="p-1 rounded-pill border d-inline-flex align-items-center shadow-sm" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
        <button type="button" class="cycle-pill-btn <?php echo ($selectedCycle === 'weekly') ? 'active' : ''; ?>" data-cycle="weekly" onclick="selectPricingCycle('weekly')">
            Theo Tuần
        </button>
        <button type="button" class="cycle-pill-btn <?php echo ($selectedCycle === 'monthly') ? 'active' : ''; ?>" data-cycle="monthly" onclick="selectPricingCycle('monthly')">
            Theo Tháng <span class="badge bg-white text-primary ms-1" style="font-size: 0.65rem; border-radius: 99px;">Phổ biến</span>
        </button>
        <button type="button" class="cycle-pill-btn <?php echo ($selectedCycle === 'yearly') ? 'active' : ''; ?>" data-cycle="yearly" onclick="selectPricingCycle('yearly')">
            Theo Năm <span class="badge bg-danger text-white rounded-pill ms-1" style="font-size: 0.65rem;">-28% Giảm</span>
        </button>
    </div>
</div>

<!-- 4 Cột Gói Cước Đa Màu Sắc Bento -->
<div class="row g-4 mb-5">
    <!-- 1. Gói Miễn Phí (Free - Slate) -->
    <div class="col-lg-3 col-md-6">
        <div class="pricing-card tier-free p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="fw-bold text-dark mb-0">Miễn phí</h4>
                    <span class="badge bg-light text-secondary border px-2 py-1">Cơ bản</span>
                </div>
                <p class="text-muted small mb-3">Dành cho người mới làm quen với thao tác xử lý văn bản PDF.</p>

                <div class="mb-4">
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="h2 fw-bold text-dark mb-0">0đ</span>
                        <span class="text-muted small">/ vĩnh viễn</span>
                    </div>
                    <div class="text-secondary fw-semibold small mt-1">
                        <i data-lucide="coins" style="width: 14px; height: 14px; color: #64748b;"></i> 50 Tokens khởi đầu
                    </div>
                </div>

                <ul class="list-unstyled small mb-4 text-secondary">
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Ghép và Tách tệp PDF cơ bản</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Dung lượng tệp tối đa 10MB</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Snapshot bảo vệ 7 ngày</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-muted">
                        <i data-lucide="minus" style="width: 16px; height: 16px; color: #cbd5e1;"></i>
                        <span>Hàng đợi xử lý thông thường</span>
                    </li>
                </ul>
            </div>

            <div>
                <?php if (auth_check() && ($user['plan_tier'] ?? 'free') === 'free'): ?>
                    <button class="btn btn-outline-secondary w-100 rounded-pill btn-sm py-2" disabled>Gói hiện tại của bạn</button>
                <?php else: ?>
                    <button class="btn btn-light border w-100 rounded-pill btn-sm py-2" disabled>Mặc định</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. Gói Plus (Sky Blue) -->
    <div class="col-lg-3 col-md-6">
        <div class="pricing-card tier-plus p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="fw-bold text-dark mb-0">Gói Plus</h4>
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-bold">Cá nhân</span>
                </div>
                <p class="text-muted small mb-3">Tăng cường Token để xử lý bài tập, đồ án học tập thường xuyên.</p>

                <div class="mb-4">
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="h2 fw-bold text-info mb-0 tier-price" id="price_plus">79.000đ</span>
                        <span class="text-muted small tier-cycle-label" id="cycle_plus">/ tháng</span>
                    </div>
                    <div class="fw-bold small mt-1" style="color: #0284c7;">
                        <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i> <span id="tokens_plus">Vô hạn Tokens (∞)</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.7rem;">Tính theo mốc 00:00 (UTC+7)</div>
                </div>

                <ul class="list-unstyled small mb-4 text-secondary">
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span><strong>Vô hạn Tokens (∞)</strong> suốt thời hạn gói</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Dung lượng tệp lên tới <strong>25MB</strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Xem trước & Phóng to trang sắc nét</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Snapshot bảo vệ 30 ngày</span>
                    </li>
                </ul>
            </div>

            <div>
                <button type="button" class="btn btn-outline-primary w-100 rounded-pill fw-bold btn-sm py-2 hover-lift" onclick="openCheckout('plus')" style="border-color: #0284c7; color: #0284c7;">
                    Nâng cấp Gói Plus
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Gói Pro (Violet/Pink - HOT Phổ Biến Nhất) -->
    <div class="col-lg-3 col-md-6">
        <div class="pricing-card tier-pro p-4 h-100 d-flex flex-column justify-content-between">
            <div class="pricing-badge-hot">
                PHỔ BIẾN NHẤT ★
            </div>

            <div>
                <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
                    <h4 class="fw-bold text-dark mb-0">Gói Pro</h4>
                    <span class="badge text-white px-2 py-1" style="background: linear-gradient(135deg, #7c3aed, #ec4899);">Chuyên nghiệp</span>
                </div>
                <p class="text-muted small mb-3">Tối ưu cho người làm văn phòng, sinh viên đồ án & giảng viên.</p>

                <div class="mb-4">
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="h2 fw-bold text-primary mb-0 tier-price" id="price_pro" style="color: #7c3aed !important;">169.000đ</span>
                        <span class="text-muted small tier-cycle-label" id="cycle_pro">/ tháng</span>
                    </div>
                    <div class="fw-bold small mt-1" style="color: #7c3aed;">
                        <i data-lucide="zap" style="width: 14px; height: 14px; color: #f59e0b;"></i> <span id="tokens_pro">Vô hạn Tokens (∞)</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.7rem;">Tính theo mốc 00:00 (UTC+7)</div>
                </div>

                <ul class="list-unstyled small mb-4 text-secondary">
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span><strong>Vô hạn Tokens (∞)</strong> tốc độ cao</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Dung lượng tải lên <strong>50MB</strong>/tệp</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Ép khổ A4 Dọc căn giữa chuẩn</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Ưu tiên hàng đợi xử lý cực nhanh</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Lưu trữ Snapshot bất biến 1 năm</span>
                    </li>
                </ul>
            </div>

            <div>
                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow hover-lift" onclick="openCheckout('pro')" style="background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%); border: none;">
                    Nâng cấp Pro ngay &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Gói Premium (Royal Gold - VIP) -->
    <div class="col-lg-3 col-md-6">
        <div class="pricing-card tier-premium p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="fw-bold text-dark mb-0">Premium</h4>
                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">VIP DOANH NGHIỆP</span>
                </div>
                <p class="text-muted small mb-3">Toàn quyền khai thác tài nguyên không giới hạn cùng hỗ trợ ưu tiên 24/7.</p>

                <div class="mb-4">
                    <div class="d-flex align-items-baseline gap-1">
                        <span class="h2 fw-bold text-dark mb-0 tier-price" id="price_premium">299.000đ</span>
                        <span class="text-muted small tier-cycle-label" id="cycle_premium">/ tháng</span>
                    </div>
                    <div class="text-danger fw-bold small mt-1">
                        <i data-lucide="crown" style="width: 14px; height: 14px; color: #f59e0b;"></i> <span id="tokens_premium">Vô hạn Tokens (∞)</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.7rem;">Tính theo mốc 00:00 (UTC+7)</div>
                </div>

                <ul class="list-unstyled small mb-4 text-secondary">
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span><strong>Vô hạn Tokens (∞)</strong> không giới hạn</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Dung lượng tải lên tối đa <strong>100MB</strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Lưu trữ Snapshot vĩnh viễn</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Hỗ trợ kỹ thuật chuyên gia 24/7</span>
                    </li>
                </ul>
            </div>

            <div>
                <button type="button" class="btn btn-warning text-dark w-100 rounded-pill fw-bold btn-sm py-2 shadow-sm hover-lift" onclick="openCheckout('premium')" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); border: none;">
                    Đăng ký Premium VIP
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lịch sử giao dịch Token gần đây (nếu đã đăng nhập) -->
<?php if (auth_check() && !empty($transactions)): ?>
    <div class="card card-aurora p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i data-lucide="history" style="width: 18px; height: 18px; color: #6366f1;"></i>
            <span>Lịch sử biến động Token gần đây</span>
        </h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size: 0.76rem;">
                        <th style="font-weight: 600;">Thời gian</th>
                        <th style="font-weight: 600;">Hành động</th>
                        <th style="font-weight: 600;">Nội dung chi tiết</th>
                        <th style="font-weight: 600;">Biến động</th>
                        <th style="font-weight: 600;">Số dư sau giao dịch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td class="text-muted"><?php echo date('d/m/Y H:i', strtotime($txn['created_at'])); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <?php echo strtoupper($txn['action']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($txn['description'] ?? ''); ?></td>
                            <td>
                                <?php if ($txn['amount'] > 0): ?>
                                    <span class="text-success fw-bold">+<?php echo number_format($txn['amount']); ?> Tokens</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold"><?php echo number_format($txn['amount']); ?> Tokens</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark"><?php echo number_format($txn['balance_after']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Thanh Toán Mua Gói & Nạp Token (Checkout Modal) -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalTitle" aria-hidden="true" style="backdrop-filter: blur(12px);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; overflow: hidden; background: #ffffff;">
            <!-- Modal Header -->
            <div class="p-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 bg-white text-primary shadow-sm">
                        <i data-lucide="credit-card" style="width: 24px; height: 24px; color: #4f46e5;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white" id="checkoutModalTitle">Xác nhận Đăng ký & Thanh toán Gói</h5>
                        <p class="text-white-50 small mb-0">Kích hoạt tài nguyên tức thì & nạp Token vào tài khoản</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Cột trái: Tóm tắt đơn hàng -->
                    <div class="col-md-5">
                        <div class="p-3 rounded-4 bg-light border h-100">
                            <h6 class="fw-bold text-dark mb-3">Tóm tắt đơn hàng</h6>

                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Gói cước:</span>
                                <span class="fw-bold text-primary text-uppercase" id="checkoutTierName">GÓI PRO</span>
                            </div>

                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Chu kỳ:</span>
                                <span class="fw-semibold text-dark" id="checkoutCycleName">Theo Tháng</span>
                            </div>

                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Token nhận được:</span>
                                <span class="fw-bold text-success" id="checkoutTokensGranted">+2.000 Tokens</span>
                            </div>

                            <div class="d-flex justify-content-between small mb-3">
                                <span class="text-muted">Hạn sử dụng:</span>
                                <span class="fw-medium text-dark" id="checkoutExpiryLabel">+30 ngày</span>
                            </div>

                            <hr class="my-2">

                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <span class="fw-bold text-dark">Tổng thanh toán:</span>
                                <span class="h4 fw-extrabold text-danger mb-0" id="checkoutTotalPrice">169.000đ</span>
                            </div>

                            <div class="alert alert-success border-0 small py-2 px-3 mb-0" style="font-size: 0.75rem;">
                                <i data-lucide="shield-check" style="width: 14px; height: 14px;" class="me-1"></i>
                                Gói sẽ được kích hoạt ngay lập tức sau khi xác nhận thanh toán thành công.
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Phương thức thanh toán -->
                    <div class="col-md-7">
                        <h6 class="fw-bold text-dark mb-3">Chọn phương thức thanh toán</h6>

                        <!-- Tabs phương thức -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary active flex-grow-1 p-2 pm-btn rounded-3" data-pm="vietqr" onclick="selectPaymentMethod('vietqr')">
                                <i data-lucide="qr-code" style="width: 16px; height: 16px;" class="mb-1 d-block mx-auto"></i>
                                <span>VietQR (VPBank)</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 p-2 pm-btn rounded-3" data-pm="momo" onclick="selectPaymentMethod('momo')">
                                <i data-lucide="smartphone" style="width: 16px; height: 16px;" class="mb-1 d-block mx-auto"></i>
                                <span>Ví MoMo</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 p-2 pm-btn rounded-3" data-pm="zalopay" onclick="selectPaymentMethod('zalopay')">
                                <i data-lucide="wallet" style="width: 16px; height: 16px;" class="mb-1 d-block mx-auto"></i>
                                <span>ZaloPay & Đa Bank</span>
                            </button>
                        </div>

                        <!-- 1. Panel VietQR (VPBank) -->
                        <div id="pm_panel_vietqr" class="pm-panel text-center p-3 border rounded-3 bg-light mb-3">
                            <div class="fw-bold text-dark small mb-1">Quét mã VietQR VPBank Napas 247</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Mở app bất kỳ ngân hàng nào (MB, VCB, Techcombank, VPBank...) để quét</div>

                            <div class="my-2 position-relative d-inline-block">
                                <div class="p-2 bg-white rounded-3 border shadow-xs d-inline-block">
                                    <img src="/assets/qr/qr_vpbank.png" alt="Mã VietQR VPBank MAI TRONG THE" class="img-fluid rounded-2 qr-preview-img" style="max-height: 240px; cursor: zoom-in;" onclick="zoomQrImage('/assets/qr/qr_vpbank.png', 'Mã VietQR VPBank - MAI TRONG THE')" title="Bấm để phóng to mã QR">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.68rem;">🔍 Bấm vào mã QR để phóng to toàn màn hình</div>
                            </div>

                            <div class="small text-start bg-white p-3 rounded-2 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Ngân hàng:</span>
                                    <strong class="text-dark">VPBank (Việt Nam Thịnh Vượng)</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Chủ tài khoản:</span>
                                    <strong class="text-dark">MAI TRONG THE</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Số tài khoản:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-primary font-monospace fs-6">0392269573</strong>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" onclick="copyToClipboard('0392269573', 'Đã sao chép số tài khoản VPBank!')" title="Sao chép số tài khoản">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Số tiền cần chuyển:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-danger fw-bold fs-6" id="qrAmountDisplay">169.000đ</strong>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="copyCurrentPrice()" title="Sao chép số tiền">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Nội dung chuyển khoản:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-danger font-monospace" id="transferSyntax">PDF PRO <?php echo auth_id() ?? 'GUEST'; ?></strong>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="copyTransferSyntax()" title="Sao chép nội dung">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Panel Ví MoMo -->
                        <div id="pm_panel_momo" class="pm-panel text-center p-3 border rounded-3 bg-light mb-3" style="display: none;">
                            <div class="fw-bold text-dark small mb-1">Quét mã Ví Điện Tử MoMo</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Mở ứng dụng Ví MoMo hoặc app ngân hàng hỗ trợ Napas 247 để quét</div>

                            <div class="my-2 position-relative d-inline-block">
                                <div class="p-2 bg-white rounded-3 border shadow-xs d-inline-block">
                                    <img src="/assets/qr/qr_momo.png" alt="Mã QR MoMo MAI TRONG THE" class="img-fluid rounded-2 qr-preview-img" style="max-height: 240px; cursor: zoom-in;" onclick="zoomQrImage('/assets/qr/qr_momo.png', 'Mã Ví MoMo - MAI TRONG THE')" title="Bấm để phóng to mã QR">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.68rem;">🔍 Bấm vào mã QR để phóng to toàn màn hình</div>
                            </div>

                            <div class="small text-start bg-white p-3 rounded-2 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Ví điện tử:</span>
                                    <strong class="text-danger">MoMo (Napas 247)</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Chủ ví:</span>
                                    <strong class="text-dark">MAI TRONG THE</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Số điện thoại / Ví:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-primary font-monospace fs-6">0392269573</strong>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" onclick="copyToClipboard('0392269573', 'Đã sao chép số điện thoại ví MoMo!')" title="Sao chép số điện thoại">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Lời nhắn chuyển tiền:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-danger font-monospace" id="transferSyntaxMomo">PDF PRO <?php echo auth_id() ?? 'GUEST'; ?></strong>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="copyTransferSyntax()" title="Sao chép lời nhắn">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Panel ZaloPay & 50+ Ngân Hàng -->
                        <div id="pm_panel_zalopay" class="pm-panel text-center p-3 border rounded-3 bg-light mb-3" style="display: none;">
                            <div class="fw-bold text-dark small mb-1">Mã QR Đa Năng — Nhận tiền từ mọi ứng dụng</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Hỗ trợ ZaloPay, Vietcombank, BIDV, MBBank, Techcombank và 50+ app ngân hàng</div>

                            <div class="my-2 position-relative d-inline-block">
                                <div class="p-2 bg-white rounded-3 border shadow-xs d-inline-block">
                                    <img src="/assets/qr/qr_zalopay.png" alt="Mã QR ZaloPay & 50+ Bank MAI TRONG THE" class="img-fluid rounded-2 qr-preview-img" style="max-height: 240px; cursor: zoom-in;" onclick="zoomQrImage('/assets/qr/qr_zalopay.png', 'Mã QR Đa Năng - MAI TRONG THE')" title="Bấm để phóng to mã QR">
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.68rem;">🔍 Bấm vào mã QR để phóng to toàn màn hình</div>
                            </div>

                            <div class="small text-start bg-white p-3 rounded-2 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Hình thức QR:</span>
                                    <strong class="text-primary">ZaloPay / QR Đa Năng (50+ Ngân hàng)</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Chủ tài khoản:</span>
                                    <strong class="text-dark">MAI TRONG THE</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Nội dung chuyển khoản:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-danger font-monospace" id="transferSyntaxZalo">PDF PRO <?php echo auth_id() ?? 'GUEST'; ?></strong>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="copyTransferSyntax()" title="Sao chép nội dung">
                                            <i data-lucide="copy" style="width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="checkoutAlert" style="display: none;" class="alert small mb-3"></div>

                        <!-- Nút hoàn tất kích hoạt -->
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm rounded-pill hover-lift" id="btnConfirmSubscribe" onclick="executeSubscribe()" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                            <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i>
                            <span>Xác nhận Thanh toán & Kích hoạt gói ngay</span>
                        </button>
                        <p class="text-center text-muted small mt-2 mb-0" style="font-size: 0.7rem;">
                            Bằng cách bấm xác nhận, bạn đồng ý với Điều khoản dịch vụ và chính sách bảo hành gói của PDF Workspace.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chúc Mừng Thành Công -->
<div class="modal fade" id="subscriptionSuccessModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(12px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center p-4" style="border-radius: 22px;">
            <div class="p-3 rounded-circle bg-success-subtle text-success d-inline-flex mx-auto mb-3" style="width: 64px; height: 64px; align-items: center; justify-content: center;">
                <i data-lucide="check-circle" style="width: 36px; height: 36px;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Thanh toán & Kích hoạt thành công! 🎉</h4>
            <p class="text-muted small mb-3" id="successModalMsg">
                Gói cước đã được áp dụng và Tokens đã được cộng vào tài khoản của bạn.
            </p>
            <div class="p-3 bg-light rounded-3 border mb-4 text-start small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Mã giao dịch:</span>
                    <strong class="font-monospace text-primary" id="successModalTxn">INV-PRO-XXXX</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Số dư Token mới:</span>
                    <strong class="text-success" id="successModalNewTokens">2.050 Tokens</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Hạn gói:</span>
                    <strong class="text-dark" id="successModalExpiry">--</strong>
                </div>
            </div>
            <div class="d-grid gap-2">
                <a href="/dashboard" class="btn btn-primary fw-bold py-2 rounded-pill shadow-sm hover-lift">
                    Về Trang Chủ Tổng Quan
                </a>
                <button type="button" class="btn btn-light border py-2 rounded-pill" data-bs-dismiss="modal">
                    Ở lại trang Bảng giá
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Phóng to Mã QR Cực Nét -->
<div class="modal fade" id="qrZoomModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(12px);">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg p-3 text-center" style="border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <span class="fw-bold small text-dark" id="qrZoomTitle">Mã QR Thanh Toán</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="p-2 bg-white rounded-3 border shadow-xs mb-2">
                <img id="qrZoomImg" src="" alt="Mã QR phóng to" class="img-fluid rounded-2" style="width: 100%; height: auto;">
            </div>
            <div class="text-muted small" style="font-size: 0.72rem;">
                Mở ứng dụng ngân hàng hoặc ví điện tử để quét trực tiếp mã này.
            </div>
        </div>
    </div>
</div>

<script>
    let activeCycle = '<?php echo $selectedCycle; ?>';
    let checkoutTier = '<?php echo $selectedTier; ?>';
    let currentPaymentMethod = 'vietqr';

    const pricingData = {
        plus: {
            name: 'Gói Plus',
            tokens: { weekly: 500, monthly: 500, yearly: 6000 },
            price: { weekly: 29000, monthly: 79000, yearly: 699000 },
            label: { weekly: '29.000đ', monthly: '79.000đ', yearly: '699.000đ' }
        },
        pro: {
            name: 'Gói Pro',
            tokens: { weekly: 2000, monthly: 2000, yearly: 24000 },
            price: { weekly: 59000, monthly: 169000, yearly: 1490000 },
            label: { weekly: '59.000đ', monthly: '169.000đ', yearly: '1.490.000đ' }
        },
        premium: {
            name: 'Gói Premium',
            tokens: { weekly: 10000, monthly: 10000, yearly: 120000 },
            price: { weekly: 99000, monthly: 299000, yearly: 2590000 },
            label: { weekly: '99.000đ', monthly: '299.000đ', yearly: '2.590.000đ' }
        }
    };

    function selectPricingCycle(cycle) {
        activeCycle = cycle;
        document.querySelectorAll('.cycle-pill-btn').forEach(btn => {
            if (btn.dataset.cycle === cycle) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Cập nhật giá trên các thẻ
        ['plus', 'pro', 'premium'].forEach(tier => {
            const data = pricingData[tier];
            const priceEl = document.getElementById(`price_${tier}`);
            const cycleEl = document.getElementById(`cycle_${tier}`);
            const tokensEl = document.getElementById(`tokens_${tier}`);

            if (priceEl) priceEl.textContent = data.label[cycle];
            if (cycleEl) cycleEl.textContent = cycle === 'weekly' ? '/ tuần' : (cycle === 'yearly' ? '/ năm' : '/ tháng');
            if (tokensEl) {
                tokensEl.textContent = 'Vô hạn Tokens (∞)';
            }
        });

        // Cập nhật URL mà không cần tải lại toàn trang
        if (window.history && window.history.replaceState) {
            const newUrl = window.location.pathname + '?cycle=' + cycle;
            window.history.replaceState(null, '', newUrl);
        }
    }

    function openCheckout(tier) {
        if (!window.IS_LOGGED_IN) {
            if (typeof window.showRequireLoginModal === 'function') {
                window.showRequireLoginModal(() => {
                    openCheckout(tier);
                });
            } else {
                window.location.href = '/login';
            }
            return;
        }

        checkoutTier = tier;
        const data = pricingData[tier];
        const cycleText = activeCycle === 'weekly' ? 'Theo Tuần' : (activeCycle === 'yearly' ? 'Theo Năm' : 'Theo Tháng');
        const expiryText = activeCycle === 'weekly' ? '1 Tuần (Hạn 00:00 UTC+7)' : (activeCycle === 'yearly' ? '1 Năm (Hạn 00:00 UTC+7)' : '1 Tháng (Hạn 00:00 UTC+7)');
        const tokensText = 'Vô hạn Tokens (∞)';

        document.getElementById('checkoutTierName').textContent = data.name;
        document.getElementById('checkoutCycleName').textContent = cycleText;
        document.getElementById('checkoutTokensGranted').textContent = tokensText;
        document.getElementById('checkoutExpiryLabel').textContent = expiryText;
        document.getElementById('checkoutTotalPrice').textContent = data.label[activeCycle];

        const userId = <?php echo auth_id() ?? 0; ?>;
        const syntax = `PDF ${tier.toUpperCase()} ${userId}`;
        document.getElementById('transferSyntax').textContent = syntax;
        if (document.getElementById('transferSyntaxMomo')) document.getElementById('transferSyntaxMomo').textContent = syntax;
        if (document.getElementById('transferSyntaxZalo')) document.getElementById('transferSyntaxZalo').textContent = syntax;
        if (document.getElementById('qrAmountDisplay')) document.getElementById('qrAmountDisplay').textContent = data.label[activeCycle];

        const alertEl = document.getElementById('checkoutAlert');
        if (alertEl) alertEl.style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        modal.show();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function selectPaymentMethod(pm) {
        currentPaymentMethod = pm;
        document.querySelectorAll('.pm-btn').forEach(btn => {
            if (btn.dataset.pm === pm) {
                btn.className = 'btn btn-sm btn-outline-primary active flex-grow-1 p-2 pm-btn rounded-3';
            } else {
                btn.className = 'btn btn-sm btn-outline-secondary flex-grow-1 p-2 pm-btn rounded-3';
            }
        });

        document.querySelectorAll('.pm-panel').forEach(p => p.style.display = 'none');
        const activePanel = document.getElementById(`pm_panel_${pm}`);
        if (activePanel) activePanel.style.display = 'block';
    }

    function copyToClipboard(text, msg) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                showToastNotification(msg || 'Đã sao chép vào bộ nhớ tạm!');
            }).catch(() => {
                fallbackCopy(text, msg);
            });
        } else {
            fallbackCopy(text, msg);
        }
    }

    function fallbackCopy(text, msg) {
        const input = document.createElement('textarea');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToastNotification(msg || 'Đã sao chép vào bộ nhớ tạm!');
    }

    function copyTransferSyntax() {
        const el = document.getElementById('transferSyntax');
        if (el) copyToClipboard(el.textContent.trim(), 'Đã sao chép cú pháp chuyển khoản!');
    }

    function copyCurrentPrice() {
        const el = document.getElementById('qrAmountDisplay');
        if (el) copyToClipboard(el.textContent.trim(), 'Đã sao chép số tiền cần chuyển!');
    }

    function zoomQrImage(src, title) {
        document.getElementById('qrZoomImg').src = src;
        document.getElementById('qrZoomTitle').textContent = title || 'Mã QR Thanh Toán';
        new bootstrap.Modal(document.getElementById('qrZoomModal')).show();
    }

    function showToastNotification(message) {
        let toast = document.getElementById('qrCopyToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'qrCopyToast';
            toast.style.cssText = 'position: fixed; bottom: 30px; right: 30px; background: #0f172a; color: #fff; padding: 10px 18px; border-radius: 12px; font-size: 0.82rem; font-weight: 500; z-index: 999999; box-shadow: 0 10px 25px rgba(0,0,0,0.25); display: flex; align-items: center; gap: 8px; opacity: 0; transition: all 0.25s ease; pointer-events: none;';
            toast.innerHTML = '<span>📋</span> <span id="qrToastText"></span>';
            document.body.appendChild(toast);
        }
        document.getElementById('qrToastText').textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
        }, 2200);
    }

    async function executeSubscribe() {
        const btn = document.getElementById('btnConfirmSubscribe');
        const alertEl = document.getElementById('checkoutAlert');

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý giao dịch...`;
        alertEl.style.display = 'none';

        try {
            const res = await fetch('/api/pricing/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                },
                body: JSON.stringify({
                    plan_tier: checkoutTier,
                    billing_cycle: activeCycle,
                    payment_method: currentPaymentMethod,
                    csrf_token: '<?php echo csrf_token(); ?>'
                })
            });

            const data = await res.json();
            if (data.success) {
                const checkoutModalEl = document.getElementById('checkoutModal');
                const checkoutModalInst = bootstrap.Modal.getInstance(checkoutModalEl);
                if (checkoutModalInst) checkoutModalInst.hide();

                const heroTokens = document.getElementById('heroUserTokens');
                if (heroTokens) {
                    heroTokens.textContent = 'Vô hạn (∞)';
                }

                if (typeof window.updateUserTokensBadge === 'function') {
                    window.updateUserTokensBadge('unlimited', checkoutTier);
                }

                document.getElementById('successModalMsg').textContent = data.message;
                document.getElementById('successModalTxn').textContent = data.data.transaction_code;
                document.getElementById('successModalNewTokens').textContent = 'Vô hạn Tokens (∞)';
                document.getElementById('successModalExpiry').textContent = (data.data.expires_at ? `00:00 ngày ${data.data.expires_at} (UTC+7)` : '--');

                const successModal = new bootstrap.Modal(document.getElementById('subscriptionSuccessModal'));
                successModal.show();
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                alertEl.className = 'alert alert-danger small mb-3 rounded-3';
                alertEl.textContent = data.message || 'Thanh toán thất bại.';
                alertEl.style.display = 'block';
            }
        } catch (e) {
            alertEl.className = 'alert alert-danger small mb-3 rounded-3';
            alertEl.textContent = 'Lỗi kết nối máy chủ: ' + e.message;
            alertEl.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="sparkles" style="width: 16px; height: 16px;"></i><span>Xác nhận Thanh toán & Kích hoạt gói ngay</span>`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    selectPricingCycle(activeCycle);

    <?php if (!empty($selectedTier) && in_array($selectedTier, ['plus', 'pro', 'premium'], true)): ?>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            openCheckout('<?php echo $selectedTier; ?>');
        }, 250);
    });
    <?php endif; ?>
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
