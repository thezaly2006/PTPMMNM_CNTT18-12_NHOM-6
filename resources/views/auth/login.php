<?php ob_start(); ?>

<div class="mb-4">
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-2 fw-semibold" style="font-size: 0.78rem;">
        <i data-lucide="key" style="width: 13px; height: 13px;" class="me-1"></i> Xác thực người dùng
    </span>
    <h3 class="fw-bold text-dark mb-1">Đăng nhập tài khoản</h3>
    <p class="text-muted small mb-0">Chào mừng bạn trở lại! Nhập thông tin để vào không gian làm việc.</p>
</div>

<form action="/login" method="POST" class="mb-3" id="loginForm">
    <?php echo csrf_field(); ?>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold text-dark">Địa chỉ Email</label>
        <div class="input-group">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i data-lucide="mail" style="width: 17px; height: 17px;"></i>
            </span>
            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" value="" required placeholder="name@example.com" autofocus>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label small fw-semibold text-dark mb-0">Mật khẩu</label>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i data-lucide="lock" style="width: 17px; height: 17px;"></i>
            </span>
            <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password" value="" required placeholder="Nhập mật khẩu">
            <button class="btn btn-outline-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('password', 'eyeIconPassword')" title="Hiện/Ẩn mật khẩu" style="background: #f8fafc;">
                <i data-lucide="eye" id="eyeIconPassword" style="width: 17px; height: 17px;"></i>
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" checked>
            <label class="form-check-label small text-muted" for="rememberMe">Ghi nhớ đăng nhập</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2 mb-3 shadow-sm py-2">
        <span>Đăng nhập vào hệ thống</span>
        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
    </button>
</form>

<div class="text-center pt-2 mb-3 border-top">
    <p class="text-muted small mb-0">
        Chưa có tài khoản? <a href="/register" class="text-primary text-decoration-none fw-semibold">Đăng ký tài khoản mới &rarr;</a>
    </p>
</div>

<div class="demo-pill-card p-3">
    <div class="small fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
        <span class="d-flex align-items-center gap-1">
            <i data-lucide="zap" style="width: 15px; height: 15px; color: #f59e0b;"></i>
            <span>Chọn nhanh tài khoản thử nghiệm:</span>
        </span>
        <span class="text-muted" style="font-size: 0.70rem;">Nhấp để điền</span>
    </div>
    <div class="row g-2">
        <div class="col-6">
            <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start p-2 rounded-3 bg-white shadow-xs" onclick="fillUserDemo()">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-bold" style="font-size: 0.75rem;">👤 User Thường</span>
                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.62rem;">User</span>
                </div>
                <div class="text-muted text-truncate font-monospace" style="font-size: 0.68rem;">demo@pdfworkspace.test</div>
                <div class="text-muted" style="font-size: 0.66rem;">Pass: <code>Password@123</code></div>
            </button>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-outline-warning text-dark btn-sm w-100 text-start p-2 rounded-3 bg-white shadow-xs" onclick="fillAdminDemo()">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-bold text-dark" style="font-size: 0.75rem;">🛡️ Quản Trị Viên</span>
                    <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.62rem;">ADMIN</span>
                </div>
                <div class="text-muted text-truncate font-monospace" style="font-size: 0.68rem;">admin@pdfworkspace.test</div>
                <div class="text-muted" style="font-size: 0.66rem;">Pass: <code>Admin@123</code></div>
            </button>
        </div>
    </div>
</div>

<script>
    function fillUserDemo() {
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        if (emailInput && passInput) {
            emailInput.value = 'demo@pdfworkspace.test';
            passInput.value = 'Password@123';
            emailInput.focus();
        }
    }

    function fillAdminDemo() {
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        if (emailInput && passInput) {
            emailInput.value = 'admin@pdfworkspace.test';
            passInput.value = 'Admin@123';
            emailInput.focus();
        }
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/auth.php';
?>