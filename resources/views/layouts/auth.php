<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'PDF Workspace'); ?></title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.468.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="https://unpkg.com/lucide@latest"><\/script>');
        }
    </script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 10% 15%, rgba(99, 102, 241, 0.16), transparent 45%),
                        radial-gradient(circle at 90% 85%, rgba(236, 72, 153, 0.14), transparent 50%),
                        radial-gradient(circle at 50% 50%, rgba(14, 165, 233, 0.08), transparent 55%),
                        #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 1060px;
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(99, 102, 241, 0.25), 0 10px 25px -5px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.9);
            display: flex;
            min-height: 640px;
        }

        /* Cột bên trái: Brand & Showcase sinh động */
        .auth-showcase {
            width: 48%;
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 35%, #8b5cf6 70%, #db2777 100%);
            padding: 48px 40px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Vòng tròn hiệu ứng nền mờ */
        .showcase-blob-1 {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
        }
        .showcase-blob-2 {
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 220px;
            height: 220px;
            background: rgba(253, 224, 71, 0.18);
            border-radius: 50%;
            filter: blur(35px);
            pointer-events: none;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 24px;
        }

        .brand-logo-box {
            width: 46px;
            height: 46px;
            background: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            padding: 14px 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.25s ease;
        }
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateX(4px);
        }
        .feature-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }


        /* Cột bên phải: Form đăng nhập / đăng ký */
        .auth-form-side {
            width: 52%;
            padding: 48px 46px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-control {
            border-radius: 12px;
            padding: 11px 16px;
            border: 1px solid #cbd5e1;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .input-group-text {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #64748b;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #ec4899 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.32);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #ffffff;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 60%, #db2777 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.42);
            color: #ffffff;
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        .demo-pill-card {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border: 1px dashed #c7d2fe;
            border-radius: 14px;
            padding: 14px;
            transition: all 0.2s ease;
        }
        .demo-pill-card:hover {
            border-color: #6366f1;
            background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%);
        }

        @media (max-width: 991.98px) {
            .auth-showcase {
                display: none !important;
            }
            .auth-form-side {
                width: 100%;
                padding: 36px 24px;
            }
            .auth-wrapper {
                max-width: 480px;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- Cột bên trái: Hero Showcase rực rỡ & chuyên nghiệp -->
        <div class="auth-showcase d-none d-lg-flex">
            <div class="showcase-blob-1"></div>
            <div class="showcase-blob-2"></div>

            <!-- Top Brand -->
            <div class="position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="brand-logo-box">
                        <i data-lucide="layers" style="width: 26px; height: 26px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">PDF Workspace</h5>
                        <small class="text-white-50">Không gian làm việc PDF 2.0</small>
                    </div>
                </div>
                <div class="brand-badge">
                    <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i>
                    <span>Công nghệ trực quan & Đóng băng Snapshot</span>
                </div>
            </div>

            <!-- Middle Feature List -->
            <div class="position-relative my-4" style="z-index: 2;">
                <h4 class="fw-bold mb-2">Biên tập & Xử lý PDF theo tiêu chuẩn kiểm thử</h4>
                <p class="text-white-50 small mb-4">
                    Giải pháp sắp xếp, ghép nhiều file, tách trang và đối chiếu bản chụp bất biến theo thời gian thực (Real-time).
                </p>

                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i data-lucide="edit-3" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">Kéo thả & Xoay A4 chuẩn</div>
                        <div class="text-white-50" style="font-size: 0.73rem;">Trực quan hóa trang, tự động căn chỉnh khổ dọc A4</div>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">Bản chụp bất biến (Snapshot)</div>
                        <div class="text-white-50" style="font-size: 0.73rem;">Bảo toàn 100% trang gốc, đối chiếu minh bạch</div>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i data-lucide="upload-cloud" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">Tải nhiều tệp cùng lúc</div>
                        <div class="text-white-50" style="font-size: 0.73rem;">Hàng đợi thông minh, xử lý tuần tự không lo đầy tải</div>
                    </div>
                </div>
            </div>


        </div>

        <!-- Cột bên phải: Form xác thực -->
        <div class="auth-form-side">
            <!-- Mobile Brand Header (Hiện khi ở màn hình nhỏ) -->
            <div class="d-lg-none text-center mb-4">
                <div class="d-inline-flex p-3 rounded-4 text-white shadow-sm mb-2" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <i data-lucide="layers" style="width: 30px; height: 30px;"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0">PDF Workspace</h4>
                <p class="text-muted small">Xử lý, ghép & tách PDF trực tuyến</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 small rounded-3" role="alert">
                    <i data-lucide="alert-circle" class="me-2 flex-shrink-0" style="width: 18px; height: 18px;"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3 small rounded-3" role="alert">
                    <i data-lucide="check-circle-2" class="me-2 flex-shrink-0" style="width: 18px; height: 18px;"></i>
                    <div><?php echo htmlspecialchars($success); ?></div>
                </div>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshIcons() {
            if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                lucide.createIcons();
            }
        }
        refreshIcons();
        document.addEventListener('DOMContentLoaded', refreshIcons);
        window.addEventListener('load', refreshIcons);

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                if (icon) icon.setAttribute('data-lucide', 'eye');
            }
            refreshIcons();
        }
    </script>
</body>
</html>