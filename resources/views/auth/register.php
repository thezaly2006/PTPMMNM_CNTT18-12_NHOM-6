<?php ob_start(); ?>

<div class="mb-4">
    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mb-2 fw-semibold" style="font-size: 0.78rem;">
        <i data-lucide="sparkles" style="width: 13px; height: 13px;" class="me-1"></i> Thành viên mới
    </span>
    <h3 class="fw-bold text-dark mb-1">Tạo tài khoản mới</h3>
    <p class="text-muted small mb-0">Bắt đầu trải nghiệm toàn bộ công cụ xử lý & xuất PDF trực tuyến.</p>
</div>

<form action="/register" method="POST" class="mb-3">
    <?php echo csrf_field(); ?>

    <div class="mb-3">
        <label for="name" class="form-label small fw-semibold text-dark">Họ và tên</label>
        <div class="input-group">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i data-lucide="user" style="width: 17px; height: 17px;"></i>
            </span>
            <input type="text" class="form-control border-start-0 ps-0" id="name" name="name" required placeholder="Nguyễn Văn A" autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold text-dark">Địa chỉ Email</label>
        <div class="input-group">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i data-lucide="mail" style="width: 17px; height: 17px;"></i>
            </span>
            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required placeholder="name@example.com">
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold text-dark">Mật khẩu (tối thiểu 6 ký tự)</label>
        <div class="input-group">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i data-lucide="lock" style="width: 17px; height: 17px;"></i>
            </span>
            <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password" minlength="6" required placeholder="••••••••">
            <button class="btn btn-outline-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('password', 'eyeIconRegPassword')" title="Hiện/Ẩn mật khẩu" style="background: #f8fafc;">
                <i data-lucide="eye" id="eyeIconRegPassword" style="width: 17px; height: 17px;"></i>
            </button>
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label small fw-semibold text-dark">Xác nhận mật khẩu</label>
        <div class="input-group">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i data-lucide="shield-check" style="width: 17px; height: 17px;"></i>
            </span>
            <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password_confirmation" name="password_confirmation" minlength="6" required placeholder="••••••••">
            <button class="btn btn-outline-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('password_confirmation', 'eyeIconConfirmPassword')" title="Hiện/Ẩn mật khẩu" style="background: #f8fafc;">
                <i data-lucide="eye" id="eyeIconConfirmPassword" style="width: 17px; height: 17px;"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2 mb-3 shadow-sm py-2">
        <span>Tạo tài khoản & Bắt đầu</span>
        <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
    </button>
</form>

<div class="text-center pt-2 border-top">
    <p class="text-muted small mb-0">
        Đã có tài khoản? <a href="/login" class="text-primary text-decoration-none fw-semibold">Đăng nhập ngay &rarr;</a>
    </p>
</div>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/auth.php';
?>