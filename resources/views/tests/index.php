<?php ob_start(); ?>

<div style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Bộ kiểm thử nghiệm thu hệ thống</h4>
            <p class="text-muted small mb-0">Chạy tự động 5 bài kiểm thử bắt buộc để đánh giá tính toàn vẹn, phân quyền và bảo mật theo đặc tả đồ án.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" id="btnRunTests" onclick="triggerRunTests()">
            <i data-lucide="play" style="width: 14px; height: 14px;"></i>
            <span>Chạy kiểm thử ngay</span>
        </button>
    </div>

    <!-- Thông tin 5 bài test -->
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i data-lucide="shield-check" style="width: 18px; height: 18px; color: #6366f1;"></i>
            <span>Danh sách 5 bài kiểm thử bắt buộc theo đặc tả đồ án:</span>
        </h6>
        <div class="list-group list-group-flush small">
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2 bg-indigo-50 text-primary shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: #eef2ff;">1</span>
                    <div>
                        <strong class="text-dark">Test 1:</strong> Ghép A2, B1, A1 theo đúng thứ tự nhãn và số trang.
                        <div class="text-muted" style="font-size: 0.75rem;">Kiểm tra ghép trang từ nhiều file nguồn khác nhau</div>
                    </div>
                </div>
                <span class="badge border" style="background: rgba(99, 102, 241, 0.1); color: #4f46e5; border-color: rgba(99, 102, 241, 0.3);">Yêu cầu 1</span>
            </div>
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2 text-white shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #a855f7, #ec4899);">2</span>
                    <div>
                        <strong class="text-dark">Test 2:</strong> Xoay một trang 90 độ và loại bỏ một trang khác (chỉ trang được yêu cầu thay đổi).
                        <div class="text-muted" style="font-size: 0.75rem;">Kiểm tra độc lập góc xoay và cờ bao gồm</div>
                    </div>
                </div>
                <span class="badge border" style="background: rgba(168, 85, 247, 0.1); color: #7c3aed; border-color: rgba(168, 85, 247, 0.3);">PDF Editor</span>
            </div>
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2 text-white shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f59e0b, #d97706);">3</span>
                    <div>
                        <strong class="text-dark">Test 3:</strong> Tách thành hai nhóm trang độc lập; từ chối trang không tồn tại.
                        <div class="text-muted" style="font-size: 0.75rem;">Kiểm tra tính an toàn phạm vi số trang</div>
                    </div>
                </div>
                <span class="badge border" style="background: rgba(245, 158, 11, 0.1); color: #d97706; border-color: rgba(245, 158, 11, 0.3);">Tách PDF</span>
            </div>
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2 text-white shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ef4444, #e11d48);">4</span>
                    <div>
                        <strong class="text-dark">Test 4:</strong> Tệp rỗng hoặc tệp hỏng bị chặn an toàn, không tạo tệp kết quả giả mạo.
                        <div class="text-muted" style="font-size: 0.75rem;">Kiểm tra phòng thủ và xử lý tệp độc hại</div>
                    </div>
                </div>
                <span class="badge border" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.3);">Bảo mật</span>
            </div>
            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2 text-white shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981, #059669);">5</span>
                    <div>
                        <strong class="text-dark">Test 5:</strong> Phân quyền đa tài khoản và tính bất biến của Snapshot sau khi sửa kế hoạch.
                        <div class="text-muted" style="font-size: 0.75rem;">Kiểm tra phân quyền nghiêm ngặt và tính đóng băng dữ liệu</div>
                    </div>
                </div>
                <span class="badge border" style="background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.3);">Yêu cầu 2</span>
            </div>
        </div>
    </div>

    <!-- Kết quả kiểm thử -->
    <div id="testResultArea" style="display: none;">
        <div class="card card-custom p-4 mb-4 border-success">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-success mb-0">Kết quả chạy kiểm thử tự động</h6>
                <span class="badge bg-success badge-status" id="testBadgeScore">5 / 5 PASS (100%)</span>
            </div>

            <div class="list-group list-group-flush mb-3 small" id="testDetailsList">
                <!-- Chi tiết từng bài test -->
            </div>

            <details>
                <summary class="small text-muted cursor-pointer">Xem nhật ký chi tiết từ Terminal (Console Output)</summary>
                <pre class="bg-dark text-light p-3 rounded-3 mt-2 small" id="testTerminalLogs" style="max-height: 300px; overflow-y: auto; font-family: monospace;"></pre>
            </details>
        </div>
    </div>
</div>

<script>
    async function triggerRunTests() {
        const btn = document.getElementById('btnRunTests');
        const area = document.getElementById('testResultArea');
        const detailsList = document.getElementById('testDetailsList');
        const badgeScore = document.getElementById('testBadgeScore');
        const logsEl = document.getElementById('testTerminalLogs');

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang chạy kiểm thử...`;
        area.style.display = 'none';

        try {
            const res = await fetch('/api/tests/run', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                }
            });

            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (jsonErr) {
                alert('Lỗi phản hồi máy chủ: ' + text.substring(0, 200));
                return;
            }

            if (data.success) {
                const summary = data.summary;
                badgeScore.textContent = `${summary.passed} / ${summary.total} PASS (${Math.round((summary.passed / summary.total) * 100)}%)`;
                
                detailsList.innerHTML = '';
                summary.details.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'list-group-item px-0 py-2 d-flex justify-content-between align-items-start';
                    row.innerHTML = `
                        <div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle me-2">PASS</span>
                            <strong>${item.test}:</strong> ${item.message}
                        </div>
                    `;
                    detailsList.appendChild(row);
                });

                logsEl.textContent = data.logs;
                area.style.display = 'block';
            } else {
                alert('Lỗi chạy kiểm thử: ' + data.message);
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ: ' + e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="play" style="width: 14px; height: 14px;"></i><span>Chạy kiểm thử ngay</span>`;
            lucide.createIcons();
        }
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
