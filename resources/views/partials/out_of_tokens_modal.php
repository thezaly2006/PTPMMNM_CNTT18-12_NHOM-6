<!-- Modal Đề xuất nâng cấp khi hết Token -->
<div class="modal fade" id="outOfTokensModal" tabindex="-1" aria-labelledby="outOfTokensModalTitle" aria-hidden="true" style="backdrop-filter: blur(8px);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 24px; overflow: hidden; background: #ffffff;">
            <!-- Header có gradient cảnh báo & kim cương nổi bật -->
            <div class="p-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #e11d48 100%);">
                <div style="position: absolute; right: -20px; top: -20px; width: 140px; height: 140px; background: rgba(255,255,255,0.15); border-radius: 50%; filter: blur(20px);"></div>
                <div class="d-flex justify-content-between align-items-start position-relative" style="z-index: 2;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-4 bg-white text-warning shadow-sm d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i data-lucide="coins" style="width: 28px; height: 28px; color: #ea580c;"></i>
                        </div>
                        <div>
                            <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 mb-1 rounded-pill" style="font-size: 0.72rem;">
                                ⚠️ Cần bổ sung Token
                            </span>
                            <h4 class="modal-title fw-bold text-white mb-0" id="outOfTokensModalTitle">
                                Bạn đã hết Token xử lý!
                            </h4>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <p class="mb-0 mt-3 text-white-50 small" id="outOfTokensMessage">
                    Số dư Token trong tài khoản của bạn hiện không đủ để hoàn tất xuất file này. Hãy nâng cấp gói để tiếp tục không gián đoạn!
                </p>
            </div>

            <div class="modal-body p-4 pt-3">
                <!-- Bộ chọn chu kỳ thanh toán nhanh -->
                <div class="d-flex justify-content-center my-3">
                    <div class="p-1 bg-light rounded-pill border d-inline-flex align-items-center" id="modalCycleToggle">
                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 btn-modal-cycle" data-cycle="weekly" onclick="switchModalCycle('weekly')">Theo Tuần</button>
                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 btn-modal-cycle active bg-white shadow-2xs fw-bold text-primary" data-cycle="monthly" onclick="switchModalCycle('monthly')">Theo Tháng</button>
                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 btn-modal-cycle position-relative" data-cycle="yearly" onclick="switchModalCycle('yearly')">
                            Theo Năm
                            <span class="badge bg-danger rounded-pill" style="font-size: 0.6rem; margin-left: 2px;">-28%</span>
                        </button>
                    </div>
                </div>

                <!-- 3 Gói cước đề xuất -->
                <div class="row g-3 mb-3">
                    <!-- Gói Plus -->
                    <div class="col-md-4">
                        <div class="card h-100 p-3 border rounded-4 text-center hover-shadow transition-all" style="border-color: #cbd5e1; cursor: pointer;" onclick="goToPricing(event, 'plus')">
                            <div class="fw-bold text-dark mb-1">Gói Plus</div>
                            <div class="text-primary fw-bold mb-1">Vô hạn Tokens (∞)</div>
                            <div class="text-muted small mb-2" style="font-size: 0.68rem;">Hạn đến 00:00 (UTC+7)</div>
                            <div class="h4 fw-bold text-dark mb-2" id="modalPricePlus">79.000đ / tháng</div>
                            <p class="text-muted small mb-3" style="font-size: 0.75rem;">Phù hợp cá nhân xử lý tài liệu định kỳ.</p>
                            <a href="/pricing?tier=plus&cycle=monthly" class="btn btn-outline-primary btn-sm rounded-3 w-100 fw-semibold modal-tier-link" data-tier="plus" onclick="goToPricing(event, 'plus')">
                                Chọn gói Plus &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Gói Pro (Phổ biến) -->
                    <div class="col-md-4">
                        <div class="card h-100 p-3 border-2 border-primary rounded-4 text-center position-relative shadow-sm" style="background: linear-gradient(180deg, rgba(99, 102, 241, 0.04) 0%, rgba(255,255,255,1) 100%); cursor: pointer;" onclick="goToPricing(event, 'pro')">
                            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-gradient text-white shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); font-size: 0.68rem; padding: 4px 10px;">
                                Khuyên dùng ★
                            </span>
                            <div class="fw-bold text-dark mb-1 mt-1">Gói Pro</div>
                            <div class="text-primary fw-bold mb-1">Vô hạn Tokens (∞)</div>
                            <div class="text-muted small mb-2" style="font-size: 0.68rem;">Hạn đến 00:00 (UTC+7)</div>
                            <div class="h4 fw-bold text-primary mb-2" id="modalPricePro">169.000đ / tháng</div>
                            <p class="text-muted small mb-3" style="font-size: 0.75rem;">Đầy đủ A4 Dọc, tối ưu độ nét & ưu tiên xuất nhanh.</p>
                            <a href="/pricing?tier=pro&cycle=monthly" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold shadow-sm modal-tier-link" data-tier="pro" onclick="goToPricing(event, 'pro')">
                                Nâng cấp Pro ngay &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Gói Premium -->
                    <div class="col-md-4">
                        <div class="card h-100 p-3 border rounded-4 text-center hover-shadow transition-all" style="border-color: #cbd5e1; cursor: pointer;" onclick="goToPricing(event, 'premium')">
                            <div class="fw-bold text-dark mb-1">Gói Premium</div>
                            <div class="text-danger fw-bold mb-1">Vô hạn Tokens (∞)</div>
                            <div class="text-muted small mb-2" style="font-size: 0.68rem;">Hạn đến 00:00 (UTC+7)</div>
                            <div class="h4 fw-bold text-dark mb-2" id="modalPricePremium">299.000đ / tháng</div>
                            <p class="text-muted small mb-3" style="font-size: 0.75rem;">Toàn quyền tài nguyên, bảo mật cao cấp & Snapshot vĩnh viễn.</p>
                            <a href="/pricing?tier=premium&cycle=monthly" class="btn btn-outline-danger btn-sm rounded-3 w-100 fw-semibold modal-tier-link" data-tier="premium" onclick="goToPricing(event, 'premium')">
                                Chọn Premium &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light border small text-muted d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mb-0">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="shield-check" style="width: 16px; height: 16px; color: #10b981;"></i>
                        <span>Thanh toán tự động kích hoạt tức thì qua VietQR, MoMo hoặc Thẻ.</span>
                    </div>
                    <a href="/pricing" class="fw-bold text-primary text-decoration-none small d-flex align-items-center gap-1">
                        <span>Xem chi tiết bảng giá</span>
                        <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentModalCycle = 'monthly';
    const modalPricingMap = {
        weekly: { plus: '29.000đ / tuần', pro: '59.000đ / tuần', premium: '99.000đ / tuần' },
        monthly: { plus: '79.000đ / tháng', pro: '169.000đ / tháng', premium: '299.000đ / tháng' },
        yearly: { plus: '699.000đ / năm', pro: '1.490.000đ / năm', premium: '2.590.000đ / năm' }
    };

    function switchModalCycle(cycle) {
        currentModalCycle = cycle;
        document.querySelectorAll('.btn-modal-cycle').forEach(b => {
            if (b.dataset.cycle === cycle) {
                b.className = 'btn btn-sm rounded-pill px-3 py-1 btn-modal-cycle active bg-white shadow-2xs fw-bold text-primary';
            } else {
                b.className = 'btn btn-sm rounded-pill px-3 py-1 btn-modal-cycle text-muted';
            }
        });

        const p = modalPricingMap[cycle];
        document.getElementById('modalPricePlus').textContent = p.plus;
        document.getElementById('modalPricePro').textContent = p.pro;
        document.getElementById('modalPricePremium').textContent = p.premium;

        document.querySelectorAll('.modal-tier-link').forEach(a => {
            const tier = a.dataset.tier;
            a.href = `/pricing?tier=${tier}&cycle=${cycle}`;
        });
    }

    function goToPricing(e, tier) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const cycle = currentModalCycle || 'monthly';
        const modalEl = document.getElementById('outOfTokensModal');
        if (modalEl) {
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) inst.hide();
        }
        window.location.href = `/pricing?tier=${tier}&cycle=${cycle}`;
    }

    window.showOutOfTokensModal = function(info = {}) {
        const msgEl = document.getElementById('outOfTokensMessage');
        if (msgEl && info.message) {
            msgEl.textContent = info.message;
        }

        const modalEl = document.getElementById('outOfTokensModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            if (window.lucide && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        } else {
            window.location.href = '/pricing';
        }
    };
</script>
