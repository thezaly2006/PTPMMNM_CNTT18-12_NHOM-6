<!-- Page Zoom Modal (Modal Phóng To Trang Sắc Nét Theo Chuẩn Ảnh 2) -->
<div class="modal fade" id="pageZoomModal" tabindex="-1" aria-labelledby="pageZoomModalTitle" aria-hidden="true" style="backdrop-filter: blur(8px); background-color: rgba(15, 23, 42, 0.75);">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered" style="max-width: 95vw; height: 95vh; margin: auto;">
        <div class="modal-content border-0 shadow-2xl h-100" style="background: #0f172a; color: #f8fafc; border-radius: 16px; overflow: hidden;">
            <!-- Top Control Bar -->
            <div class="modal-header py-2 px-4 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between" style="background: rgba(15, 23, 42, 0.95);">
                <div class="d-flex align-items-center gap-3 overflow-hidden me-3">
                    <div class="p-2 rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm">
                        <i data-lucide="zoom-in" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div class="text-truncate">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="modal-title fw-bold text-white text-truncate mb-0" id="pageZoomModalTitle" style="font-size: 1rem;">
                                Phóng to chi tiết trang PDF
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0" id="pageZoomPageBadge" style="font-size: 0.72rem;">
                                Trang 1
                            </span>
                            <span class="badge bg-secondary text-light px-2 py-0" id="pageZoomRotationBadge" style="font-size: 0.72rem;">
                                0°
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 d-none" id="pageZoomFitBadge" style="font-size: 0.72rem;">
                                ✓ A4 Dọc
                            </span>
                        </div>
                        <div class="text-muted small text-truncate" id="pageZoomModalSubtitle" style="font-size: 0.76rem;">
                            Độ phân giải cao sắc nét &bull; Nhấp và kéo để cuộn xem
                        </div>
                    </div>
                </div>

                <!-- Các nút điều khiển Zoom và Thao tác -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <div class="btn-group btn-group-sm bg-dark border border-secondary border-opacity-50 rounded-pill p-1">
                        <button type="button" class="btn btn-sm text-light px-2" title="Thu nhỏ (-)" onclick="zoomPageModalChangeScale(-0.25)">
                            <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                        </button>
                        <span class="d-flex align-items-center px-2 small text-light fw-semibold" id="pageZoomScaleText" style="min-width: 52px; justify-content: center;">
                            175%
                        </span>
                        <button type="button" class="btn btn-sm text-light px-2" title="Phóng to (+)" onclick="zoomPageModalChangeScale(0.25)">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1" onclick="zoomPageModalResetScale()" title="Đặt lại tỷ lệ chuẩn">
                        <i data-lucide="maximize-2" style="width: 14px; height: 14px;"></i>
                        <span>Vừa vặn</span>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 d-inline-flex align-items-center gap-1" id="btnPageZoomRotate" onclick="zoomPageModalRotate()" title="Xoay trang 90°">
                        <i data-lucide="rotate-cw" style="width: 14px; height: 14px;"></i>
                        <span>Xoay 90°</span>
                    </button>

                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
            </div>

            <!-- Body: Vùng hiển thị Canvas to nét như Ảnh 2 -->
            <div class="modal-body p-0 position-relative d-flex align-items-center justify-content-center overflow-auto" id="pageZoomCanvasWrapper" style="background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); min-height: 480px;">
                <!-- Loading spinner -->
                <div id="pageZoomLoading" class="position-absolute top-50 start-50 translate-middle text-center p-3 rounded-4 bg-dark bg-opacity-75 shadow-lg" style="z-index: 20;">
                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                    <div class="small text-light fw-semibold">Đang kết xuất độ phân giải cao...</div>
                </div>

                <!-- Nút chuyển trang Trái (Prev) -->
                <button type="button" class="btn btn-dark btn-lg position-absolute start-0 top-50 translate-middle-y ms-3 rounded-circle shadow-lg d-flex align-items-center justify-content-center border border-secondary border-opacity-50" id="btnPageZoomPrev" onclick="zoomPageModalPrev()" title="Trang trước (←)" style="width: 52px; height: 52px; z-index: 15; opacity: 0.85; transition: all 0.2s ease;">
                    <i data-lucide="chevron-left" style="width: 28px; height: 28px;"></i>
                </button>

                <!-- Vùng chứa Canvas hiển thị rõ nét -->
                <div class="p-4 d-flex align-items-center justify-content-center" style="min-width: 100%; min-height: 100%;">
                    <div id="pageZoomCanvasContainer" style="transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); display: inline-block;">
                        <canvas id="pageZoomCanvas" style="box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6); border-radius: 8px; background: #ffffff; display: block; max-width: 100%;"></canvas>
                    </div>
                </div>

                <!-- Nút chuyển trang Phải (Next) -->
                <button type="button" class="btn btn-dark btn-lg position-absolute end-0 top-50 translate-middle-y me-3 rounded-circle shadow-lg d-flex align-items-center justify-content-center border border-secondary border-opacity-50" id="btnPageZoomNext" onclick="zoomPageModalNext()" title="Trang sau (→)" style="width: 52px; height: 52px; z-index: 15; opacity: 0.85; transition: all 0.2s ease;">
                    <i data-lucide="chevron-right" style="width: 28px; height: 28px;"></i>
                </button>
            </div>

            <!-- Bottom Navigation Bar -->
            <div class="modal-footer py-2 px-4 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center" style="background: rgba(15, 23, 42, 0.95); font-size: 0.8rem;">
                <div class="d-flex align-items-center gap-3 text-secondary small">
                    <span class="d-none d-md-inline"><kbd class="bg-dark border border-secondary text-light">←</kbd> <kbd class="bg-dark border border-secondary text-light">→</kbd> Đổi trang</span>
                    <span class="d-none d-md-inline"><kbd class="bg-dark border border-secondary text-light">R</kbd> Xoay</span>
                    <span class="d-none d-md-inline"><kbd class="bg-dark border border-secondary text-light">ESC</kbd> Đóng</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-light fw-semibold" id="pageZoomPaginationText">Trang 1 / 1</span>
                    <button type="button" class="btn btn-sm btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // State của Page Zoom Modal
    window.pageZoomState = {
        docId: null,
        pageNum: 1,
        totalPages: 1,
        title: '',
        rotation: 0,
        fitPortrait: false,
        scale: 1.75, // Scale lớn giúp chữ và hình ảnh siêu nét (chuẩn Ảnh 2)
        onPrev: null,
        onNext: null,
        onRotate: null,
        renderTask: null
    };

    /**
     * Hàm mở Modal Phóng to trang PDF với độ phân giải cao Retina/HD
     */
    window.openPageZoomModal = function(config) {
        window.pageZoomState.docId = config.docId;
        window.pageZoomState.pageNum = config.pageNum || 1;
        window.pageZoomState.totalPages = config.totalPages || 1;
        window.pageZoomState.title = config.title || 'Tài liệu PDF';
        window.pageZoomState.rotation = config.rotation || 0;
        window.pageZoomState.fitPortrait = !!config.fitPortrait;
        window.pageZoomState.scale = config.scale || 1.75;
        window.pageZoomState.onPrev = config.onPrev || null;
        window.pageZoomState.onNext = config.onNext || null;
        window.pageZoomState.onRotate = config.onRotate || null;

        const modalEl = document.getElementById('pageZoomModal');
        if (!modalEl) return;

        updatePageZoomModalUI();
        renderPageZoomCanvas();

        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();

        if (window.lucide && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    };

    function updatePageZoomModalUI() {
        const s = window.pageZoomState;
        const titleEl = document.getElementById('pageZoomModalTitle');
        const badgeEl = document.getElementById('pageZoomPageBadge');
        const rotBadge = document.getElementById('pageZoomRotationBadge');
        const fitBadge = document.getElementById('pageZoomFitBadge');
        const scaleText = document.getElementById('pageZoomScaleText');
        const pagText = document.getElementById('pageZoomPaginationText');
        const btnPrev = document.getElementById('btnPageZoomPrev');
        const btnNext = document.getElementById('btnPageZoomNext');

        if (titleEl) titleEl.textContent = s.title;
        if (badgeEl) badgeEl.textContent = `Trang ${s.pageNum}`;
        if (rotBadge) rotBadge.textContent = `${s.rotation % 360}°`;
        if (fitBadge) {
            fitBadge.classList.toggle('d-none', !s.fitPortrait);
        }
        if (scaleText) {
            scaleText.textContent = `${Math.round(s.scale * 100)}%`;
        }
        if (pagText) {
            pagText.textContent = `Trang ${s.pageNum} / ${s.totalPages}`;
        }

        if (btnPrev) {
            btnPrev.disabled = (s.pageNum <= 1 && !s.onPrev);
            btnPrev.style.opacity = (s.pageNum <= 1 && !s.onPrev) ? '0.3' : '0.85';
        }
        if (btnNext) {
            btnNext.disabled = (s.pageNum >= s.totalPages && !s.onNext);
            btnNext.style.opacity = (s.pageNum >= s.totalPages && !s.onNext) ? '0.3' : '0.85';
        }
    }

    async function renderPageZoomCanvas() {
        const s = window.pageZoomState;
        const canvas = document.getElementById('pageZoomCanvas');
        const loading = document.getElementById('pageZoomLoading');
        if (!canvas || !s.docId) return;

        if (loading) loading.style.display = 'block';

        try {
            // Hủy render task cũ nếu đang vẽ dở
            if (s.renderTask) {
                try { s.renderTask.cancel(); } catch (e) {}
            }

            // Tận dụng pdfCache nếu có
            if (typeof pdfCache === 'undefined') {
                window.pdfCache = {};
            }
            if (!window.pdfCache[s.docId]) {
                const url = `/documents/view?id=${s.docId}`;
                window.pdfCache[s.docId] = await pdfjsLib.getDocument(url).promise;
            }

            const pdf = window.pdfCache[s.docId];
            const page = await pdf.getPage(s.pageNum);

            // Tính viewport với độ nét cao (scale) và góc xoay
            const viewport = page.getViewport({ scale: s.scale, rotation: s.rotation });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            s.renderTask = page.render(renderContext);
            await s.renderTask.promise;
            s.renderTask = null;
        } catch (err) {
            if (err && err.name !== 'RenderingCancelledException') {
                console.error('Lỗi khi render phóng to trang:', err);
            }
        } finally {
            if (loading) loading.style.display = 'none';
        }
    }

    window.zoomPageModalChangeScale = function(delta) {
        const s = window.pageZoomState;
        s.scale = Math.max(0.75, Math.min(3.5, s.scale + delta));
        updatePageZoomModalUI();
        renderPageZoomCanvas();
    };

    window.zoomPageModalResetScale = function() {
        window.pageZoomState.scale = 1.75;
        updatePageZoomModalUI();
        renderPageZoomCanvas();
    };

    window.zoomPageModalRotate = function() {
        const s = window.pageZoomState;
        s.rotation = ((s.rotation || 0) + 90) % 360;
        updatePageZoomModalUI();
        renderPageZoomCanvas();
        if (typeof s.onRotate === 'function') {
            s.onRotate(s.rotation);
        }
    };

    window.zoomPageModalPrev = function() {
        const s = window.pageZoomState;
        if (typeof s.onPrev === 'function') {
            s.onPrev();
            return;
        }
        if (s.pageNum > 1) {
            s.pageNum--;
            updatePageZoomModalUI();
            renderPageZoomCanvas();
        }
    };

    window.zoomPageModalNext = function() {
        const s = window.pageZoomState;
        if (typeof s.onNext === 'function') {
            s.onNext();
            return;
        }
        if (s.pageNum < s.totalPages) {
            s.pageNum++;
            updatePageZoomModalUI();
            renderPageZoomCanvas();
        }
    };

    // Lắng nghe phím tắt điều hướng trong Page Zoom Modal
    document.addEventListener('keydown', function(e) {
        const modalEl = document.getElementById('pageZoomModal');
        if (!modalEl || !modalEl.classList.contains('show')) return;

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            zoomPageModalPrev();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            zoomPageModalNext();
        } else if (e.key === 'r' || e.key === 'R') {
            e.preventDefault();
            zoomPageModalRotate();
        } else if (e.key === '+' || e.key === '=') {
            e.preventDefault();
            zoomPageModalChangeScale(0.25);
        } else if (e.key === '-') {
            e.preventDefault();
            zoomPageModalChangeScale(-0.25);
        }
    });
</script>
