<?php ob_start(); ?>

<!-- Aurora Hero Welcome Banner -->
<div class="p-4 p-md-5 rounded-4 mb-4 text-white position-relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #3730a3 0%, #4f46e5 35%, #7c3aed 70%, #db2777 100%); border: 1px solid rgba(255, 255, 255, 0.2);">
    <!-- Ambient Aurora Glow Orbs -->
    <div style="position: absolute; right: -40px; top: -40px; width: 280px; height: 280px; background: radial-gradient(circle, rgba(6, 182, 212, 0.4) 0%, transparent 70%); border-radius: 50%; filter: blur(35px); pointer-events: none;"></div>
    <div style="position: absolute; right: 180px; bottom: -50px; width: 240px; height: 240px; background: radial-gradient(circle, rgba(245, 158, 11, 0.35) 0%, transparent 70%); border-radius: 50%; filter: blur(40px); pointer-events: none;"></div>
    <div style="position: absolute; left: 30%; top: -60px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(236, 72, 153, 0.3) 0%, transparent 70%); border-radius: 50%; filter: blur(40px); pointer-events: none;"></div>

    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(12px);">
                <i data-lucide="sparkles" style="width: 14px; height: 14px; color: #fde047;"></i>
                <span class="fw-semibold text-white" style="font-size: 0.78rem; letter-spacing: 0.02em;">PDF Workspace 2.0 &bull; Đóng băng Snapshot Bất biến</span>
            </div>
            
            <h1 class="fw-bold mb-2 display-6 text-white" style="font-size: 2rem; letter-spacing: -0.02em;">
                Xin chào, <?php echo auth_check() ? htmlspecialchars(auth_user()['name'] ?? 'Bạn') : 'Khách trải nghiệm'; ?>! 👋
            </h1>
            <p class="mb-4 text-white" style="font-size: 0.96rem; max-width: 650px; line-height: 1.6; opacity: 0.92;">
                Không gian làm việc xử lý tài liệu PDF đa năng hiện đại: ghép nhiều tệp tốc độ cao, tách trang đa luồng, xoay chuẩn A4 và tự động đối chiếu băm SHA-256 an toàn tuyệt đối.
            </p>
            <div class="d-flex flex-wrap gap-2 pt-1">
                <button class="btn btn-light text-primary fw-bold btn-sm px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm rounded-pill hover-lift" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px; color: #4f46e5;"></i>
                    <span>Tải tệp PDF lên</span>
                </button>
                <a href="/merge" class="btn btn-outline-light fw-semibold btn-sm px-3 py-2 d-inline-flex align-items-center gap-2 rounded-pill hover-lift" style="border-color: rgba(255,255,255,0.6); backdrop-filter: blur(10px); background: rgba(255,255,255,0.08);">
                    <i data-lucide="combine" style="width: 16px; height: 16px; color: #34d399;"></i>
                    <span>Ghép nhiều PDF</span>
                </a>
                <a href="/split" class="btn btn-outline-light fw-semibold btn-sm px-3 py-2 d-inline-flex align-items-center gap-2 rounded-pill hover-lift" style="border-color: rgba(255,255,255,0.6); backdrop-filter: blur(10px); background: rgba(255,255,255,0.08);">
                    <i data-lucide="split" style="width: 16px; height: 16px; color: #fb7185;"></i>
                    <span>Tách trang PDF</span>
                </a>
                <a href="/editor" class="btn btn-outline-light fw-semibold btn-sm px-3 py-2 d-inline-flex align-items-center gap-2 rounded-pill hover-lift" style="border-color: rgba(255,255,255,0.6); backdrop-filter: blur(10px); background: rgba(255,255,255,0.08);">
                    <i data-lucide="edit-3" style="width: 16px; height: 16px; color: #f472b6;"></i>
                    <span>Mở PDF Editor</span>
                </a>
            </div>
        </div>
        <div class="col-lg-4 d-none d-lg-flex justify-content-end">
            <div class="p-3 rounded-4 shadow-sm" style="background: rgba(255, 255, 255, 0.16); border: 1px solid rgba(255, 255, 255, 0.28); backdrop-filter: blur(16px); max-width: 300px;">
                <div class="d-flex align-items-start gap-3">
                    <div class="p-2 rounded-3 bg-white text-primary shadow-sm flex-shrink-0">
                        <i data-lucide="shield-check" style="width: 28px; height: 28px; color: #4f46e5;"></i>
                    </div>
                    <div>
                        <div class="fw-bold small text-white">Bảo toàn 100% Gốc</div>
                        <div class="mt-1" style="font-size: 0.76rem; line-height: 1.45; color: rgba(255, 255, 255, 0.9);">
                            Mọi thao tác xoay, ghép, tách đều tạo snapshot bản sao độc lập, hoàn toàn không làm biến đổi tài liệu gốc.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bento 4 Thẻ Chỉ số Thống kê Metric Đa Sắc Màu -->
<div class="row g-3 mb-4">
    <!-- Thẻ 1: Tài liệu nguồn (Azure/Cyan) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-aurora p-4 h-100 position-relative overflow-hidden hover-lift">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0284c7, #06b6d4);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.06em; font-size: 0.72rem;">Tài liệu nguồn</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);">
                    <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_documents']); ?></h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;">Đang lưu trữ</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted" style="font-size: 0.76rem;">Tổng tệp PDF gốc</small>
                <a href="/documents" class="text-primary small text-decoration-none fw-bold" style="font-size: 0.76rem;">Quản lý &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Thẻ 2: Phiên chỉnh sửa (Emerald/Teal) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-aurora p-4 h-100 position-relative overflow-hidden hover-lift">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #059669, #10b981);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.06em; font-size: 0.72rem;">Kế hoạch chỉnh sửa</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <i data-lucide="edit-3" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_plans']); ?></h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">✓ Tự động lưu</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted" style="font-size: 0.76rem;">Kéo thả & xoay trang</small>
                <a href="/editor" class="text-success small text-decoration-none fw-bold" style="font-size: 0.76rem;">Mở Editor &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Thẻ 3: Bản xuất thành phẩm (Violet/Fuchsia) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-aurora p-4 h-100 position-relative overflow-hidden hover-lift">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #7c3aed, #ec4899);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.06em; font-size: 0.72rem;">Bản xuất thành công</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark"><?php echo number_format($stats['total_exports']); ?></h3>
                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">Bất biến Snapshot</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted" style="font-size: 0.76rem;">Tệp PDF đã tạo ra</small>
                <a href="/exports" class="text-info small text-decoration-none fw-bold" style="font-size: 0.76rem;">Lịch sử &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Thẻ 4: Token & Gói dịch vụ (Gold/Amber) -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-aurora p-4 h-100 position-relative overflow-hidden hover-lift" style="background: linear-gradient(180deg, rgba(254, 243, 199, 0.4) 0%, rgba(255, 255, 255, 0.95) 100%); border-color: rgba(245, 158, 11, 0.35);">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #d97706, #f59e0b, #ef4444);"></div>
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.06em; font-size: 0.72rem;">Ví Token & Gói</span>
                <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                    <i data-lucide="coins" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-1">
                <h3 class="fw-bold mb-0 text-dark">
                    <?php 
                        if (!auth_check()) {
                            echo '50';
                        } elseif (has_unlimited_tokens()) {
                            echo '∞ Vô hạn';
                        } else {
                            echo number_format((int)(auth_user()['tokens'] ?? 0));
                        }
                    ?>
                </h3>
                <span class="badge bg-warning text-dark fw-bold border border-warning" style="font-size: 0.7rem;">
                    <?php 
                        if (!auth_check()) {
                            echo 'KHÁCH';
                        } elseif (is_admin()) {
                            echo 'ADMIN';
                        } else {
                            echo 'GÓI ' . strtoupper(auth_user()['plan_tier'] ?? 'FREE');
                        }
                    ?>
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <small class="text-muted" style="font-size: 0.74rem;">
                    <?php 
                        if (auth_check() && has_unlimited_tokens() && !empty(auth_user()['plan_expires_at'])) {
                            echo 'Hạn: 00:00 ' . date('d/m/Y', strtotime(auth_user()['plan_expires_at'])) . ' (UTC+7)';
                        } elseif (auth_check() && is_admin()) {
                            echo 'Vô hạn Token vĩnh viễn';
                        } else {
                            echo '5 token / lần xuất';
                        }
                    ?>
                </small>
                <a href="/pricing" class="btn btn-xs btn-warning text-dark fw-bold d-inline-flex align-items-center gap-1 rounded-pill px-2 py-1 shadow-2xs" style="font-size: 0.72rem;">
                    <span>Nâng cấp</span>
                    <i data-lucide="arrow-right" style="width: 11px; height: 11px;"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bento 4 Thao Tác Nhanh Đa Sắc Màu (Vibrant Feature Quick Actions) -->
<div class="card card-aurora p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <div class="p-1 rounded-2 text-white" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
                    <i data-lucide="zap" style="width: 16px; height: 16px;"></i>
                </div>
                <span>Bộ công cụ chuyên sâu 1 chạm</span>
            </h5>
            <small class="text-muted">Chọn tác vụ cần xử lý ngay với trải nghiệm mượt mà, tối ưu hóa cho từng nhu cầu</small>
        </div>
        <span class="badge bg-indigo-50 text-primary border border-indigo-200 px-3 py-1 rounded-pill small d-none d-sm-inline-block fw-semibold" style="background: #eef2ff;">Chuẩn nghiệp vụ 100%</span>
    </div>
    
    <div class="row g-3">
        <!-- Tile 1: Tải PDF lên (Indigo-Cyan) -->
        <div class="col-md-3">
            <div class="p-3 rounded-4 bg-white h-100 shadow-sm d-flex flex-column justify-content-between border hover-lift" style="border-color: rgba(79, 70, 229, 0.2) !important;">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="p-3 rounded-3 text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #4f46e5, #06b6d4);">
                        <i data-lucide="upload-cloud" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">Tải PDF lên kho</div>
                        <div class="text-muted mt-1" style="font-size: 0.76rem; line-height: 1.4;">Nạp nhiều tệp nguồn đa luồng cùng lúc nhanh chóng</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold py-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                    <span>Tải tệp lên</span>
                </button>
            </div>
        </div>

        <!-- Tile 2: Ghép PDF (Emerald-Teal) -->
        <div class="col-md-3">
            <div class="p-3 rounded-4 bg-white h-100 shadow-sm d-flex flex-column justify-content-between border hover-lift" style="border-color: rgba(16, 185, 129, 0.25) !important;">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="p-3 rounded-3 text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #059669, #10b981);">
                        <i data-lucide="combine" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">Ghép nhiều PDF</div>
                        <div class="text-muted mt-1" style="font-size: 0.76rem; line-height: 1.4;">Hợp nhất trang theo thứ tự tùy chỉnh, trích xuất tức thì</div>
                    </div>
                </div>
                <a href="/merge" class="btn btn-sm btn-outline-success w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold py-2" style="border-color: #10b981; color: #059669;">
                    <span>Vào Ghép PDF</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>

        <!-- Tile 3: Tách PDF (Sunset Orange-Rose) -->
        <div class="col-md-3">
            <div class="p-3 rounded-4 bg-white h-100 shadow-sm d-flex flex-column justify-content-between border hover-lift" style="border-color: rgba(244, 63, 94, 0.25) !important;">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="p-3 rounded-3 text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #ea580c, #f43f5e);">
                        <i data-lucide="split" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">Tách trang PDF</div>
                        <div class="text-muted mt-1" style="font-size: 0.76rem; line-height: 1.4;">Trích xuất trang lẻ hoặc chia nhóm đa file độc lập</div>
                    </div>
                </div>
                <a href="/split" class="btn btn-sm btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold py-2" style="border-color: #f43f5e; color: #e11d48;">
                    <span>Vào Tách PDF</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>

        <!-- Tile 4: PDF Editor (Violet-Pink) -->
        <div class="col-md-3">
            <div class="p-3 rounded-4 bg-white h-100 shadow-sm d-flex flex-column justify-content-between border hover-lift" style="border-color: rgba(168, 85, 247, 0.3) !important;">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="p-3 rounded-3 text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #7c3aed, #ec4899);">
                        <i data-lucide="sparkles" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.92rem;">
                            <span>PDF Editor Pro</span>
                            <span class="badge text-white rounded-pill" style="background: linear-gradient(135deg, #ec4899, #f43f5e); font-size: 0.6rem;">HOT</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.76rem; line-height: 1.4;">Xoay góc, xóa trang, kéo thả sắp xếp trực quan</div>
                    </div>
                </div>
                <a href="/editor" class="btn btn-sm btn-outline-primary w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold py-2" style="border-color: #8b5cf6; color: #7c3aed;">
                    <span>Mở Editor</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bento 2 Cột: Kho tài liệu nguồn & Lịch sử xuất gần đây -->
<div class="row g-4">
    <!-- Cột trái: Tài liệu trong kho -->
    <div class="col-lg-7">
        <div class="card card-aurora p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="folder-check" style="width: 18px; height: 18px; color: #0284c7;"></i>
                        <span>Tài liệu nguồn trong kho</span>
                    </h5>
                    <small class="text-muted">Các tệp PDF sẵn sàng để đưa vào phiên ghép hoặc chỉnh sửa</small>
                </div>
                <a href="/documents" class="text-primary text-decoration-none small fw-bold d-flex align-items-center gap-1">
                    <span>Xem tất cả</span>
                    <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                </a>
            </div>

            <?php if (empty($recentDocuments)): ?>
                <div class="text-center py-5">
                    <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                        <i data-lucide="folder-open" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Kho tài liệu còn trống</h6>
                    <p class="text-muted small mb-3">Tải lên các tệp PDF đầu tiên để bắt đầu ghép hoặc chỉnh sửa ngay.</p>
                    <button class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i data-lucide="upload" style="width: 14px; height: 14px;" class="me-1"></i>
                        <span>Tải tệp PDF ngay</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr class="text-muted border-bottom" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                <th scope="col" class="pb-2 ps-1" style="font-weight: 700;">Tài liệu</th>
                                <th scope="col" class="pb-2 text-center" style="font-weight: 700;">Quy mô</th>
                                <th scope="col" class="pb-2" style="font-weight: 700;">Dung lượng</th>
                                <th scope="col" class="pb-2 text-end pe-1" style="font-weight: 700;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDocuments as $doc): ?>
                                <tr class="border-bottom-subtle hover-bg-subtle transition-all">
                                    <td class="py-3 ps-1">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-2 rounded-3 text-white shadow-xs flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);">
                                                <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 210px; font-size: 0.87rem;" title="<?php echo htmlspecialchars($doc['original_filename']); ?>">
                                                    <?php echo htmlspecialchars($doc['original_filename']); ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.71rem;">
                                                    <?php echo !empty($doc['created_at']) ? date('H:i d/m/Y', strtotime($doc['created_at'])) : 'PDF nguồn'; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(79, 70, 229, 0.1); color: #4338ca; font-weight: 700; font-size: 0.72rem;">
                                            <?php echo (int)$doc['total_pages']; ?> trang
                                        </span>
                                    </td>
                                    <td class="py-3 text-secondary" style="font-size: 0.78rem; font-weight: 500;">
                                        <?php echo format_bytes((int)$doc['file_size']); ?>
                                    </td>
                                    <td class="text-end py-3 pe-1">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm p-1 rounded-2 hover-lift" style="background: rgba(6, 182, 212, 0.12); color: #0891b2; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border: none;" title="Xem nhanh tệp này" onclick="window.previewDocument ? window.previewDocument(<?php echo $doc['id']; ?>, '<?php echo addslashes($doc['original_filename']); ?>') : window.open('/documents/view?id=<?php echo $doc['id']; ?>', '_blank')">
                                                <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                            </button>
                                            <a href="/editor?doc_id=<?php echo $doc['id']; ?>" class="btn btn-sm p-1 rounded-2 hover-lift" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none;" title="Mở trang trong PDF Editor Pro">
                                                <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                            </a>
                                            <a href="/documents/download?id=<?php echo $doc['id']; ?>" class="btn btn-sm p-1 rounded-2 hover-lift" style="background: rgba(16, 185, 129, 0.12); color: #059669; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none;" title="Tải tệp PDF về máy">
                                                <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cột phải: Bản xuất thành phẩm gần đây -->
    <div class="col-lg-5">
        <div class="card card-aurora p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="history" style="width: 18px; height: 18px; color: #e11d48;"></i>
                        <span>Lịch sử xuất tệp</span>
                    </h5>
                    <small class="text-muted">Các phiên xuất thành phẩm gần đây</small>
                </div>
                <a href="/exports" class="text-primary text-decoration-none small fw-bold d-flex align-items-center gap-1">
                    <span>Xem tất cả</span>
                    <i data-lucide="arrow-right" style="width: 13px; height: 13px;"></i>
                </a>
            </div>

            <?php if (empty($recentExports)): ?>
                <div class="text-center py-5">
                    <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                        <i data-lucide="clock" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Chưa có bản xuất nào</h6>
                    <p class="text-muted small mb-0">Khi bạn hoàn thành ghép hoặc tách PDF, file thành phẩm sẽ hiển thị tại đây.</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($recentExports as $job): ?>
                        <?php 
                            $jobType = $job['job_type'] ?? 'editor';
                            $typeGrad = match($jobType) {
                                'merge' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                                'split' => 'linear-gradient(135deg, #ea580c 0%, #f43f5e 100%)',
                                default => 'linear-gradient(135deg, #7c3aed 0%, #ec4899 100%)'
                            };
                            $typeIcon = match($jobType) {
                                'merge' => 'combine',
                                'split' => 'split',
                                default => 'sparkles'
                            };
                            $typeLabel = match($jobType) {
                                'merge' => 'Ghép PDF',
                                'split' => 'Tách PDF',
                                default => 'PDF Editor'
                            };
                        ?>
                        <div class="p-3 rounded-3 bg-white border border-light-subtle shadow-2xs hover-lift d-flex justify-content-between align-items-center transition-all">
                            <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                <div class="p-2 rounded-3 text-white shadow-xs flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: <?php echo $typeGrad; ?>;">
                                    <i data-lucide="<?php echo $typeIcon; ?>" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div class="text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="<?php echo htmlspecialchars($job['output_name'] ?? 'Tệp kết quả #' . $job['id']); ?>">
                                        <?php echo htmlspecialchars($job['output_name'] ?? 'Tệp kết quả #' . $job['id']); ?>
                                    </div>
                                    <div class="text-muted d-flex align-items-center gap-2 mt-1" style="font-size: 0.71rem;">
                                        <span><?php echo date('H:i d/m/Y', strtotime($job['created_at'])); ?></span>
                                        <span>&bull;</span>
                                        <span class="badge rounded-pill fw-semibold" style="background: rgba(15, 23, 42, 0.06); color: #475569; font-size: 0.65rem;">
                                            <?php echo $typeLabel; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <?php if ($job['status'] === 'completed'): ?>
                                    <span class="badge rounded-pill px-2 py-1 fw-bold d-inline-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem;">
                                        <i data-lucide="check" style="width: 12px; height: 12px;"></i> Hoàn tất
                                    </span>
                                    <a href="/exports/download?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-light p-1 rounded-2" title="Tải tệp thành phẩm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i data-lucide="download" style="width: 14px; height: 14px; color: #475569;"></i>
                                    </a>
                                <?php elseif ($job['status'] === 'failed'): ?>
                                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: rgba(244, 63, 94, 0.12); color: #e11d48; font-size: 0.72rem;">
                                        <i data-lucide="alert-circle" style="width: 12px; height: 12px;" class="me-1"></i> Thất bại
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.72rem;">
                                        <span class="spinner-border spinner-border-sm me-1" style="width: 9px; height: 9px;"></span> Đang xử lý
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
