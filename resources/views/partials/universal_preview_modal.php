<!-- Universal PDF Review Modal (Xem trước toàn bộ tài liệu PDF trước khi tải xuống) -->
<div class="modal fade" id="universalPdfReviewModal" tabindex="-1" aria-labelledby="universalPdfReviewTitle" aria-hidden="true" style="backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 94vw; height: 92vh;">
        <div class="modal-content border-0 shadow-2xl h-100" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <!-- Modal Header -->
            <div class="modal-header py-2 px-3 border-bottom d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-3">
                    <div class="p-1 rounded-3 bg-danger text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;">
                        <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div class="text-truncate">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="modal-title fw-bold text-white text-truncate mb-0" id="universalPdfReviewTitle" style="font-size: 0.95rem;">
                                Xem trước tài liệu PDF
                            </h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0" style="font-size: 0.68rem; font-weight: 600;">
                                Chế độ Review
                            </span>
                        </div>
                        <div class="text-white-50 small text-truncate" id="universalPdfReviewSubtitle" style="font-size: 0.75rem;">
                            Kiểm tra nội dung hoàn chỉnh trước khi lưu trữ hoặc tải xuống
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="#" id="universalPdfReviewNewTab" target="_blank" class="btn btn-sm btn-outline-light d-none d-sm-inline-flex align-items-center gap-1 py-1 px-2" title="Mở trong tab trình duyệt mới">
                        <i data-lucide="external-link" style="width: 14px; height: 14px;"></i>
                        <span class="small">Tab mới</span>
                    </a>
                    <a href="#" id="universalPdfReviewDownload" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 py-1 px-3 fw-semibold shadow-sm" title="Tải xuống tệp PDF">
                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                        <span class="small">Tải xuống PDF</span>
                    </a>
                    <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
            </div>

            <!-- Modal Body (Nhúng iframe xem PDF toàn vẹn) -->
            <div class="modal-body p-0 position-relative d-flex flex-column h-100" style="background: #f1f5f9;">
                <div id="universalPdfReviewLoading" class="position-absolute top-50 start-50 translate-middle text-center p-4 rounded-4 shadow-sm bg-white" style="z-index: 10;">
                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                    <div class="small text-dark fw-bold">Đang nạp tài liệu PDF...</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Vui lòng chờ trong giây lát</div>
                </div>

                <iframe id="universalPdfReviewFrame" class="w-100 flex-grow-1 border-0" style="min-height: 520px; height: 100%; display: none;"></iframe>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i data-lucide="info" style="width: 14px; height: 14px; color: #6366f1;"></i>
                    <span class="d-none d-md-inline">Tài liệu đã được đối chiếu an toàn. Bạn có thể duyệt từng trang hoặc in trực tiếp.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Đóng cửa sổ</button>
                    <a href="#" id="universalPdfReviewDownloadFooter" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 px-3 fw-semibold shadow-sm">
                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                        <span>Xác nhận Tải xuống</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Hàm mở Modal xem trước PDF toàn diện dùng chung toàn hệ thống
     * @param {string} viewUrl - URL xem inline của file PDF (kèm ?inline=1)
     * @param {string} title - Tên file hoặc tiêu đề hiển thị
     * @param {string} [downloadUrl] - URL tải file về máy (nếu khác viewUrl)
     */
    window.reviewPdf = function(viewUrl, title = 'Tài liệu PDF', downloadUrl = null) {
        if (!viewUrl) return;

        // Chuẩn hóa URL xem inline
        let finalViewUrl = viewUrl;
        if (!finalViewUrl.includes('inline=1')) {
            finalViewUrl += (finalViewUrl.includes('?') ? '&' : '?') + 'inline=1';
        }

        const finalDlUrl = downloadUrl || viewUrl.replace(/[?&]inline=1/, '');

        const modalEl = document.getElementById('universalPdfReviewModal');
        if (!modalEl) {
            window.open(finalViewUrl, '_blank');
            return;
        }

        const titleEl = document.getElementById('universalPdfReviewTitle');
        const frameEl = document.getElementById('universalPdfReviewFrame');
        const loadingEl = document.getElementById('universalPdfReviewLoading');
        const newTabEl = document.getElementById('universalPdfReviewNewTab');
        const dlEl = document.getElementById('universalPdfReviewDownload');
        const dlFootEl = document.getElementById('universalPdfReviewDownloadFooter');

        if (titleEl) titleEl.textContent = title;
        if (newTabEl) newTabEl.href = finalViewUrl;
        if (dlEl) {
            dlEl.href = finalDlUrl;
            dlEl.setAttribute('download', title);
        }
        if (dlFootEl) {
            dlFootEl.href = finalDlUrl;
            dlFootEl.setAttribute('download', title);
        }

        if (loadingEl) loadingEl.style.display = 'block';
        if (frameEl) {
            frameEl.style.display = 'none';
            frameEl.onload = function() {
                if (loadingEl) loadingEl.style.display = 'none';
                frameEl.style.display = 'block';
            };
            frameEl.src = finalViewUrl;
        }

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();

        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    };
</script>
