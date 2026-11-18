<!-- Require Login Modal (Yêu cầu đăng nhập trước khi xuất file PDF) -->
<div class="modal fade" id="requireLoginModal" tabindex="-1" aria-labelledby="requireLoginModalTitle" aria-hidden="true" style="backdrop-filter: blur(8px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
            <!-- Header có gradient bắt mắt -->
            <div class="modal-header border-0 p-4 pb-0 d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 text-white shadow-sm d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%); width: 48px; height: 48px;">
                        <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-1" id="requireLoginModalTitle">
                            Đăng nhập để xuất tài liệu
                        </h5>
                        <p class="text-muted small mb-0">Cần xác thực để tạo Bản chụp bất biến (Snapshot) an toàn</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body p-4 pt-3">
                <div class="alert alert-light border small text-secondary mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="info" style="width: 16px; height: 16px; color: #6366f1; flex-shrink: 0;"></i>
                    <span>Bạn đang ở <strong>Chế độ Trải nghiệm</strong>. Đăng nhập ngay dưới đây (chỉ mất 1 giây) để hoàn tất xuất file mà <strong>không bị mất dữ liệu đang làm dở</strong>.</span>
                </div>

                <!-- 2 Nút Đăng nhập 1 chạm nhanh nhất -->
                <label class="form-label small fw-bold text-secondary text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    Đăng nhập nhanh 1 chạm:
                </label>
                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-between p-2 px-3 rounded-3 shadow-2xs" onclick="quickLoginByType('demo')">
                        <div class="d-flex align-items-center gap-2">
                            <span class="p-1 rounded-circle bg-primary-subtle text-primary">
                                <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            </span>
                            <div class="text-start">
                                <div class="fw-bold small text-dark">👤 Người Dùng Thử Nghiệm</div>
                                <div class="text-muted" style="font-size: 0.72rem;">demo@pdfworkspace.test</div>
                            </div>
                        </div>
                        <span class="badge bg-primary px-2 py-1">Vào ngay &rarr;</span>
                    </button>

                    <button type="button" class="btn btn-outline-warning d-flex align-items-center justify-content-between p-2 px-3 rounded-3 shadow-2xs text-dark" onclick="quickLoginByType('admin')">
                        <div class="d-flex align-items-center gap-2">
                            <span class="p-1 rounded-circle bg-warning-subtle text-warning">
                                <i data-lucide="shield" style="width: 16px; height: 16px;"></i>
                            </span>
                            <div class="text-start">
                                <div class="fw-bold small text-dark">🛡️ Quản Trị Viên (Admin)</div>
                                <div class="text-muted" style="font-size: 0.72rem;">admin@pdfworkspace.test</div>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark px-2 py-1">Vào ngay &rarr;</span>
                    </button>
                </div>

                <div class="position-relative text-center my-3">
                    <hr class="text-muted">
                    <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white text-muted small fw-semibold">
                        Hoặc đăng nhập bằng tài khoản của bạn
                    </span>
                </div>

                <!-- Form đăng nhập thông thường -->
                <form id="quickLoginForm" onsubmit="handleQuickLoginForm(event)">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary mb-1">Email</label>
                        <input type="email" id="quickLoginEmail" class="form-control form-control-sm" placeholder="nhap_email@domain.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-1">Mật khẩu</label>
                        <input type="password" id="quickLoginPassword" class="form-control form-control-sm" placeholder="••••••••" required>
                    </div>

                    <div id="quickLoginAlert" class="alert small mb-3" style="display: none;"></div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="btnQuickLoginSubmit">
                        <i data-lucide="log-in" style="width: 15px; height: 15px;"></i>
                        <span>Đăng nhập & Tiếp tục xuất PDF</span>
                    </button>
                </form>
            </div>

            <div class="modal-footer bg-light p-3 border-top d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <span class="text-muted">Chưa có tài khoản? <a href="/register" class="fw-semibold text-primary">Đăng ký ngay</a></span>
                <button type="button" class="btn btn-sm btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.pendingAuthAction = null;

    /**
     * Mở modal yêu cầu đăng nhập và ghi nhớ hành động cần tiếp tục sau khi đăng nhập thành công
     * @param {Function} [onSuccessCallback] - Hàm sẽ tự động chạy sau khi đăng nhập thành công
     */
    window.showRequireLoginModal = function(onSuccessCallback = null) {
        window.pendingAuthAction = onSuccessCallback;
        const modalEl = document.getElementById('requireLoginModal');
        if (!modalEl) {
            window.location.href = '/login';
            return;
        }

        const alertEl = document.getElementById('quickLoginAlert');
        if (alertEl) alertEl.style.display = 'none';

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();

        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    };

    /**
     * Đăng nhập 1 chạm nhanh
     */
    async function quickLoginByType(type) {
        await executeQuickLogin({ quick_type: type });
    }

    /**
     * Xử lý submit form đăng nhập thông thường
     */
    async function handleQuickLoginForm(e) {
        e.preventDefault();
        const email = document.getElementById('quickLoginEmail').value.trim();
        const password = document.getElementById('quickLoginPassword').value;
        await executeQuickLogin({ email, password });
    }

    /**
     * Gửi request đăng nhập nhanh tới backend
     */
    async function executeQuickLogin(payload) {
        const alertEl = document.getElementById('quickLoginAlert');
        const btnSubmit = document.getElementById('btnQuickLoginSubmit');

        if (alertEl) alertEl.style.display = 'none';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang đăng nhập...`;
        }

        try {
            const res = await fetch('/api/login-quick', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.success) {
                // Đăng nhập thành công!
                window.IS_LOGGED_IN = true;

                // Đồng bộ ngay lập tức giao diện Topbar & Sidebar toàn hệ thống
                if (typeof window.syncAuthUserUI === 'function') {
                    window.syncAuthUserUI(data.user, data.csrf_token);
                }

                if (alertEl) {
                    alertEl.className = 'alert alert-success small mb-3';
                    alertEl.textContent = 'Đăng nhập thành công! Đang tiếp tục xử lý...';
                    alertEl.style.display = 'block';
                }

                // Đóng modal sau 0.4s
                setTimeout(() => {
                    const modalEl = document.getElementById('requireLoginModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();

                    // Tự động kích hoạt tiếp hành động xuất file người dùng vừa yêu cầu
                    if (typeof window.pendingAuthAction === 'function') {
                        const action = window.pendingAuthAction;
                        window.pendingAuthAction = null;
                        action();
                    } else {
                        // Tải lại trang để đồng bộ session
                        window.location.reload();
                    }
                }, 400);
            } else {
                if (alertEl) {
                    alertEl.className = 'alert alert-danger small mb-3';
                    alertEl.textContent = data.message || 'Đăng nhập thất bại.';
                    alertEl.style.display = 'block';
                }
            }
        } catch (err) {
            if (alertEl) {
                alertEl.className = 'alert alert-danger small mb-3';
                alertEl.textContent = 'Lỗi kết nối máy chủ: ' + err.message;
                alertEl.style.display = 'block';
            }
        } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i data-lucide="log-in" style="width: 15px; height: 15px;"></i><span>Đăng nhập & Tiếp tục xuất PDF</span>`;
                if (window.lucide) lucide.createIcons();
            }
        }
    }
</script>
