<?php ob_start(); ?>

<!-- PDF.js & SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<style>
    .editor-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
    }
    .editor-toolbar {
        height: 60px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        gap: 12px;
    }
    .editor-body {
        display: flex;
        flex-grow: 1;
        overflow: hidden;
    }
    /* Left Sidebar: Source Documents */
    .editor-sidebar-left {
        width: 280px;
        border-right: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
    }
    .source-doc-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
        transition: all 0.15s ease;
    }
    .source-doc-item:hover {
        border-color: #6366f1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    /* Center Canvas: Page Cards */
    .editor-canvas {
        flex-grow: 1;
        background: #f1f5f9;
        overflow-y: auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
    }
    .page-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        align-content: start;
    }
    /* Page Card */
    .page-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        cursor: grab;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .page-card:hover {
        box-shadow: 0 6px 12px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }
    .page-card.selected {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .page-card.excluded {
        opacity: 0.5;
        background: #f8fafc;
        border-style: dashed;
    }
    .page-card-header {
        padding: 8px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.75rem;
    }
    .page-thumbnail-container {
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        background: #f8fafc;
        overflow: hidden;
        position: relative;
        cursor: zoom-in;
    }
    .page-thumbnail-container .zoom-overlay-hint {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.75);
        color: #ffffff;
        font-size: 0.68rem;
        padding: 3px 8px;
        border-radius: 20px;
        backdrop-filter: blur(4px);
        opacity: 0;
        transition: opacity 0.2s ease, transform 0.2s ease;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .page-thumbnail-container:hover .zoom-overlay-hint {
        opacity: 1;
        transform: translateX(-50%) translateY(-2px);
    }
    .page-canvas {
        max-width: 100%;
        max-height: 180px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-radius: 4px;
        transition: transform 0.25s ease;
    }
    .a4-portrait-frame {
        width: 130px;
        height: 184px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        position: relative;
    }
    .a4-portrait-frame::after {
        content: "Khổ A4 Dọc";
        position: absolute;
        bottom: 3px;
        font-size: 0.6rem;
        color: #94a3b8;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .page-card-footer {
        padding: 6px 10px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    /* Right Inspector Panel */
    .editor-sidebar-right {
        width: 280px;
        border-left: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    /* Bottom Status Bar */
    .editor-statusbar {
        height: 38px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        font-size: 0.8rem;
        color: #64748b;
    }
    /* Sortable Ghost */
    .sortable-ghost {
        opacity: 0.4;
        background: #e0e7ff;
        border: 2px dashed #4f46e5;
    }
</style>

<div class="editor-wrapper">
    <!-- Top Toolbar -->
    <div class="editor-toolbar">
        <div class="d-flex align-items-center gap-2">
            <a href="/dashboard" class="btn btn-sm btn-light border text-muted" title="Về tổng quan">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            </a>
            <input type="text" id="planTitleInput" class="form-control form-control-sm fw-semibold text-dark" style="width: 260px;" value="<?php echo htmlspecialchars($currentPlan['title'] ?? 'Kế hoạch xử lý PDF mới'); ?>" placeholder="Đặt tên kế hoạch...">
        </div>

        <!-- Các công cụ Thao tác -->
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="btn btn-sm btn-light border" id="btnUndo" onclick="undo()" title="Hoàn tác thao tác gần nhất (Ctrl+Z)" disabled>
                <i data-lucide="undo-2" style="width: 15px; height: 15px;"></i>
                <span class="d-none d-lg-inline ms-1">Hoàn tác</span>
            </button>
            <button type="button" class="btn btn-sm btn-light border" id="btnRedo" onclick="redo()" title="Làm lại (Ctrl+Y)" disabled>
                <i data-lucide="redo-2" style="width: 15px; height: 15px;"></i>
                <span class="d-none d-lg-inline ms-1">Làm lại</span>
            </button>

            <div class="vr mx-1"></div>

            <button type="button" class="btn btn-sm btn-light border" onclick="rotateSelected(90)" title="Xoay trang được chọn sang phải 90°">
                <i data-lucide="rotate-cw" style="width: 15px; height: 15px;"></i>
                <span class="d-none d-lg-inline ms-1">Xoay 90°</span>
            </button>
            <button type="button" class="btn btn-sm btn-light border" onclick="resetSelectedRotation()" title="Đặt lại góc xoay trang được chọn về 0° (nằm ngang chuẩn)">
                <i data-lucide="refresh-cw" style="width: 15px; height: 15px;"></i>
                <span class="d-none d-lg-inline ms-1">Góc 0°</span>
            </button>
            <button type="button" class="btn btn-sm btn-light border" onclick="toggleFitAllPortrait()" title="Chuyển toàn bộ trang về khổ A4 dọc (Fit to Portrait)">
                <i data-lucide="shrink" style="width: 15px; height: 15px;"></i>
                <span class="d-none d-lg-inline ms-1">Tất cả A4 Dọc</span>
            </button>
            <button type="button" class="btn btn-sm btn-light border" onclick="toggleSelectedInclude()" title="Bật/Tắt trang khỏi bản xuất">
                <i data-lucide="eye-off" style="width: 15px; height: 15px;"></i>
                <span class="d-none d-lg-inline ms-1">Ẩn/Hiện</span>
            </button>
            <button type="button" class="btn btn-sm btn-light border text-danger" onclick="removeSelectedPage()" title="Xóa trang khỏi kế hoạch">
                <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
            </button>
        </div>

        <!-- Các nút Lưu & Xuất PDF -->
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" id="btnSavePlan" onclick="savePlan()">
                <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                <span>Lưu kế hoạch</span>
            </button>
            <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1" onclick="openExportModal()">
                <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                <span>Xuất PDF</span>
            </button>
        </div>
    </div>

    <!-- Body: Left Sidebar + Center Canvas + Right Inspector -->
    <div class="editor-body">
        <!-- Sidebar trái: Tài liệu nguồn -->
        <div class="editor-sidebar-left p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold small text-dark text-uppercase">Tệp nguồn</span>
                <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="plus" style="width: 12px; height: 12px;"></i> Thêm tệp
                </button>
            </div>

            <div class="overflow-y-auto flex-grow-1" id="sourceDocumentsList">
                <?php if (empty($userDocuments)): ?>
                    <div class="text-center text-muted small py-4">
                        <i data-lucide="file-question" style="width: 32px; height: 32px; color: #94a3b8;" class="mb-2"></i>
                        <p class="mb-2">Chưa có tệp PDF nào.</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">Tải lên ngay</button>
                    </div>
                <?php else: ?>
                    <?php foreach ($userDocuments as $uDoc): ?>
                        <div class="source-doc-item">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i data-lucide="file-text" style="width: 16px; height: 16px; color: #6366f1;"></i>
                                <span class="fw-medium text-dark small text-truncate" title="<?php echo htmlspecialchars($uDoc['original_filename']); ?>">
                                    <?php echo htmlspecialchars($uDoc['original_filename']); ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                <span><?php echo (int)$uDoc['total_pages']; ?> trang</span>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" onclick="addDocumentPages(<?php echo $uDoc['id']; ?>)">
                                    + Thêm tất cả
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Khu vực chính: Grid hiển thị thumbnail các trang -->
        <div class="editor-canvas">
            <div id="emptyWorkspaceMessage" class="text-center m-auto py-5" style="display: none;">
                <i data-lucide="layers" style="width: 54px; height: 54px; color: #94a3b8;" class="mb-3"></i>
                <h5 class="fw-semibold text-dark">Kế hoạch chỉnh sửa đang trống</h5>
                <p class="text-muted small mb-3">Hãy bấm "Thêm tất cả" từ danh sách tệp nguồn bên trái để bắt đầu sắp xếp và xử lý trang.</p>
            </div>

            <div class="page-grid" id="pagesGrid">
                <!-- Các thẻ trang PDF sẽ được render động tại đây -->
            </div>
        </div>

        <!-- Sidebar phải: Inspector chi tiết trang đang chọn -->
        <div class="editor-sidebar-right" id="pageInspector">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Chi tiết trang</h6>
            <div id="inspectorContent">
                <div class="text-muted small text-center py-4">
                    <i data-lucide="mouse-pointer" style="width: 24px; height: 24px; color: #cbd5e1;" class="mb-2"></i>
                    <p class="mb-0">Nhấp vào một trang bất kỳ để xem thông tin và thao tác chi tiết.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar phía dưới -->
    <div class="editor-statusbar">
        <div class="d-flex align-items-center gap-3">
            <span>Tổng số: <strong id="statusTotalPages">0</strong> trang</span>
            <span>&bull;</span>
            <span>Được chọn xuất: <strong id="statusIncludedPages" class="text-success">0</strong></span>
            <span>&bull;</span>
            <span>Đã loại trừ: <strong id="statusExcludedPages" class="text-secondary">0</strong></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span id="syncStatusBadge" class="badge bg-light text-muted border">Chưa lưu thay đổi</span>
        </div>
    </div>
</div>

<!-- Modal Xuất PDF từ Editor -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold">Xác nhận xuất tài liệu PDF</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="outputFilename" class="form-label small fw-semibold">Tên tệp kết quả (.pdf)</label>
                    <input type="text" class="form-control" id="outputFilename" value="PDF_Workspace_Export.pdf">
                </div>

                <div class="card bg-light p-3 border mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Số trang dự kiến:</span>
                        <strong id="modalExpectedPages">0 trang</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Bản chụp an toàn (Snapshot):</span>
                        <span class="text-success fw-semibold">Kích hoạt bất biến</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Bảo vệ file gốc:</span>
                        <span class="text-success fw-semibold">Không sửa đổi tệp gốc</span>
                    </div>
                </div>

                <div id="exportAlert" style="display: none;" class="alert mb-0 small"></div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btnConfirmExport" onclick="executeExport()">
                    <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                    <span>Bắt đầu xuất PDF</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Thiết lập worker cho PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // State kế hoạch chỉnh sửa
    let planId = <?php echo json_encode($currentPlan['id'] ?? null); ?>;
    let pages = <?php echo json_encode($initialPages ?? []); ?>;
    let selectedPageIndex = null;
    let isSaved = true;

    // Cache tài liệu PDF để không tải lại nhiều lần
    const pdfCache = {};

    // History Stack cho Hoàn tác (Undo) và Làm lại (Redo)
    const undoStack = [];
    const redoStack = [];

    function pushHistory() {
        undoStack.push(JSON.parse(JSON.stringify(pages)));
        redoStack.length = 0; // Xóa redo khi có thao tác mới
        updateHistoryButtons();
        markUnsaved();
    }

    function undo() {
        if (undoStack.length === 0) return;
        redoStack.push(JSON.parse(JSON.stringify(pages)));
        pages = undoStack.pop();
        renderPagesGrid();
        updateHistoryButtons();
        markUnsaved();
    }

    function redo() {
        if (redoStack.length === 0) return;
        undoStack.push(JSON.parse(JSON.stringify(pages)));
        pages = redoStack.pop();
        renderPagesGrid();
        updateHistoryButtons();
        markUnsaved();
    }

    function updateHistoryButtons() {
        document.getElementById('btnUndo').disabled = (undoStack.length === 0);
        document.getElementById('btnRedo').disabled = (redoStack.length === 0);
    }

    function markUnsaved() {
        isSaved = false;
        const badge = document.getElementById('syncStatusBadge');
        badge.className = 'badge bg-warning text-dark border';
        badge.textContent = 'Chưa lưu thay đổi';
    }

    function markSaved() {
        isSaved = true;
        const badge = document.getElementById('syncStatusBadge');
        badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
        badge.textContent = 'Đã lưu kế hoạch';
    }

    // Khởi tạo SortableJS cho Grid kéo thả trang
    const gridEl = document.getElementById('pagesGrid');
    new Sortable(gridEl, {
        animation: 200,
        ghostClass: 'sortable-ghost',
        handle: '.page-card-header',
        onEnd: function(evt) {
            if (evt.oldIndex !== evt.newIndex) {
                pushHistory();
                const movedItem = pages.splice(evt.oldIndex, 1)[0];
                pages.splice(evt.newIndex, 0, movedItem);
                reorderPages();
                renderPagesGrid();
            }
        }
    });

    function reorderPages() {
        pages.forEach((p, idx) => {
            p.output_order = idx + 1;
        });
    }

    // Kết xuất giao diện Grid các trang
    function renderPagesGrid() {
        gridEl.innerHTML = '';
        const emptyMsg = document.getElementById('emptyWorkspaceMessage');

        if (pages.length === 0) {
            emptyMsg.style.display = 'block';
            updateStatusBar();
            renderInspector();
            return;
        }
        emptyMsg.style.display = 'none';

        pages.forEach((page, index) => {
            page.output_order = index + 1;

            const card = document.createElement('div');
            card.className = `page-card ${selectedPageIndex === index ? 'selected' : ''} ${!page.is_included ? 'excluded' : ''}`;
            card.dataset.index = index;

            card.innerHTML = `
                <div class="page-card-header">
                    <span class="badge bg-primary px-2">#${page.output_order}</span>
                    <span class="text-muted text-truncate ms-1" style="max-width: 80px;" title="${page.original_filename || 'Tài liệu'}">
                        Trang ${page.source_page_number}
                    </span>
                    ${page.fit_portrait ? '<span class="badge bg-success text-white ms-auto me-1" style="font-size:0.65rem;">✓ A4 Dọc</span>' : ''}
                    <div class="d-flex align-items-center ms-${page.fit_portrait ? '0' : 'auto'} gap-1">
                        <button type="button" class="btn btn-xs btn-link text-primary p-0" title="Phóng to xem chi tiết trang này (Ảnh 2)" onclick="event.stopPropagation(); zoomEditorPage(${index})">
                            <i data-lucide="maximize-2" style="width: 13px; height: 13px;"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-link text-danger p-0" title="Xóa trang" onclick="event.stopPropagation(); removePageAt(${index})">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </div>
                <div class="page-thumbnail-container" onclick="zoomEditorPage(${index})" title="Nhấp để phóng to xem chi tiết nội dung trang (Ảnh 2)">
                    ${page.fit_portrait ? `
                        <div class="a4-portrait-frame" title="Trang nằm gọn trong khổ A4 dọc chuẩn">
                            <canvas id="thumb_canvas_${index}" class="page-canvas" style="transform: rotate(${page.rotation_degrees || 0}deg); max-height: 150px; max-width: 100%;"></canvas>
                        </div>
                    ` : `
                        <canvas id="thumb_canvas_${index}" class="page-canvas" style="transform: rotate(${page.rotation_degrees || 0}deg);"></canvas>
                    `}
                    <div class="zoom-overlay-hint">
                        <i data-lucide="zoom-in" style="width: 12px; height: 12px;"></i> Phóng to xem
                    </div>
                </div>
                <div class="page-card-footer">
                    <button type="button" class="btn btn-xs btn-light border py-0 px-1" title="Xoay 90°" onclick="event.stopPropagation(); rotatePageAt(${index}, 90)">
                        <i data-lucide="rotate-cw" style="width: 12px; height: 12px;"></i>
                    </button>
                    ${(page.rotation_degrees || 0) > 0 ? `
                        <button type="button" class="btn btn-xs btn-warning text-dark py-0 px-1" title="Đặt lại về góc 0° nằm ngang chuẩn" onclick="event.stopPropagation(); resetRotationAt(${index})">
                            <span style="font-size: 0.7rem; font-weight: bold;">0°</span>
                        </button>
                    ` : ''}
                    <button type="button" class="btn btn-xs ${page.fit_portrait ? 'btn-success text-white' : 'btn-light border'} py-0 px-1" title="Bật/Tắt chuyển khổ A4 dọc (Cách 2)" onclick="event.stopPropagation(); toggleFitPortraitAt(${index})">
                        <i data-lucide="shrink" style="width: 12px; height: 12px;"></i>
                        <span style="font-size: 0.7rem;">${page.fit_portrait ? 'A4 Dọc' : 'Khổ gốc'}</span>
                    </button>
                    <div class="form-check form-switch m-0" title="Bao gồm trong file xuất">
                        <input class="form-check-input" type="checkbox" ${page.is_included ? 'checked' : ''} onchange="event.stopPropagation(); toggleIncludeAt(${index})">
                    </div>
                    <span class="small text-muted" style="font-size: 0.7rem;">${page.rotation_degrees || 0}°</span>
                </div>
            `;

            gridEl.appendChild(card);
            renderPdfPageThumbnail(page.document_id, page.source_page_number, `thumb_canvas_${index}`);
        });

        lucide.createIcons();
        updateStatusBar();
        renderInspector();
    }

    // Tải và vẽ thumbnail PDF bằng PDF.js
    async function renderPdfPageThumbnail(docId, pageNum, canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        try {
            if (!pdfCache[docId]) {
                const url = `/documents/view?id=${docId}`;
                pdfCache[docId] = await pdfjsLib.getDocument(url).promise;
            }
            const pdf = pdfCache[docId];
            const page = await pdf.getPage(pageNum);

            const viewport = page.getViewport({ scale: 0.35 });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const ctx = canvas.getContext('2d');
            const renderContext = {
                canvasContext: ctx,
                viewport: viewport,
            };
            await page.render(renderContext).promise;
        } catch (e) {
            console.error('Lỗi khi render thumbnail:', e);
        }
    }

    function selectPage(index) {
        selectedPageIndex = index;
        document.querySelectorAll('.page-card').forEach((el, i) => {
            el.classList.toggle('selected', i === index);
        });
        renderInspector();
    }

    // Phóng to trang xem chi tiết cực nét (chuẩn Ảnh 2)
    function zoomEditorPage(index) {
        if (index < 0 || index >= pages.length) return;
        selectPage(index);
        const p = pages[index];

        window.openPageZoomModal({
            docId: p.document_id,
            pageNum: p.source_page_number,
            totalPages: pages.length,
            title: `${p.original_filename || 'Tài liệu'} — Trang ${p.source_page_number} (Đầu ra #${p.output_order})`,
            rotation: p.rotation_degrees || 0,
            fitPortrait: !!p.fit_portrait,
            scale: 1.75,
            onRotate: function(newRot) {
                pushHistory();
                p.rotation_degrees = newRot;
                renderPagesGrid();
            },
            onPrev: function() {
                if (index > 0) zoomEditorPage(index - 1);
            },
            onNext: function() {
                if (index < pages.length - 1) zoomEditorPage(index + 1);
            }
        });
    }

    function rotatePageAt(index, deg) {
        pushHistory();
        pages[index].rotation_degrees = ((pages[index].rotation_degrees || 0) + deg) % 360;
        if (pages[index].rotation_degrees < 0) pages[index].rotation_degrees += 360;
        
        const canvas = document.getElementById(`thumb_canvas_${index}`);
        if (canvas) {
            canvas.style.transform = `rotate(${pages[index].rotation_degrees}deg)`;
        }
        renderPagesGrid();
    }

    function rotateSelected(deg) {
        if (selectedPageIndex !== null && pages[selectedPageIndex]) {
            rotatePageAt(selectedPageIndex, deg);
        } else if (pages.length > 0) {
            rotatePageAt(0, deg);
        }
    }

    function resetRotationAt(index) {
        pushHistory();
        pages[index].rotation_degrees = 0;
        renderPagesGrid();
    }

    function resetSelectedRotation() {
        if (selectedPageIndex !== null && pages[selectedPageIndex]) {
            resetRotationAt(selectedPageIndex);
        } else if (pages.length > 0) {
            resetRotationAt(0);
        }
    }

    function toggleFitPortraitAt(index) {
        pushHistory();
        pages[index].fit_portrait = pages[index].fit_portrait ? 0 : 1;
        renderPagesGrid();
    }

    function toggleFitAllPortrait() {
        pushHistory();
        const anyOff = pages.some(p => !p.fit_portrait);
        pages.forEach(p => p.fit_portrait = anyOff ? 1 : 0);
        renderPagesGrid();
    }

    function toggleIncludeAt(index) {
        pushHistory();
        pages[index].is_included = pages[index].is_included ? 0 : 1;
        renderPagesGrid();
    }

    function toggleSelectedInclude() {
        if (selectedPageIndex !== null && pages[selectedPageIndex]) {
            toggleIncludeAt(selectedPageIndex);
        }
    }

    function removePageAt(index) {
        pushHistory();
        pages.splice(index, 1);
        if (selectedPageIndex === index) {
            selectedPageIndex = null;
        } else if (selectedPageIndex > index) {
            selectedPageIndex--;
        }
        reorderPages();
        renderPagesGrid();
    }

    function removeSelectedPage() {
        if (selectedPageIndex !== null) {
            removePageAt(selectedPageIndex);
        }
    }

    // Thêm các trang từ một tài liệu nguồn
    async function addDocumentPages(docId) {
        try {
            const res = await fetch(`/api/documents/pages?doc_id=${docId}`);
            const data = await res.json();
            if (data.success && data.pages.length > 0) {
                pushHistory();
                data.pages.forEach(p => {
                    pages.push({
                        document_id: p.document_id,
                        original_filename: p.original_filename,
                        source_page_number: p.source_page_number,
                        output_order: pages.length + 1,
                        rotation_degrees: 0,
                        is_included: 1,
                    });
                });
                renderPagesGrid();
            }
        } catch (e) {
            alert('Lỗi nạp trang: ' + e.message);
        }
    }

    // Cập nhật Inspector chi tiết
    function renderInspector() {
        const inspectorEl = document.getElementById('inspectorContent');
        if (selectedPageIndex === null || !pages[selectedPageIndex]) {
            inspectorEl.innerHTML = `
                <div class="text-muted small text-center py-4">
                    <i data-lucide="mouse-pointer" style="width: 24px; height: 24px; color: #cbd5e1;" class="mb-2"></i>
                    <p class="mb-0">Nhấp vào một trang bất kỳ để xem thông tin và thao tác chi tiết.</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        const page = pages[selectedPageIndex];
        inspectorEl.innerHTML = `
            <div class="mb-3">
                <span class="badge bg-primary mb-2">Trang đầu ra #${page.output_order}</span>
                <h6 class="fw-semibold text-dark text-truncate mb-1" title="${page.original_filename}">
                    ${page.original_filename}
                </h6>
                <div class="text-muted small">Trang gốc trong file: <strong>Trang ${page.source_page_number}</strong></div>
            </div>

            <div class="list-group list-group-flush mb-3 small">
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Góc xoay:</span>
                    <strong>${page.rotation_degrees || 0}°</strong>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Khổ giấy xuất:</span>
                    <strong class="${page.fit_portrait ? 'text-success' : 'text-secondary'}">
                        ${page.fit_portrait ? '✓ A4 Dọc (Cách 2)' : 'Khổ gốc'}
                    </strong>
                </div>
                <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Trạng thái xuất:</span>
                    <span class="${page.is_included ? 'text-success fw-semibold' : 'text-danger'}">
                        ${page.is_included ? 'Được bao gồm' : 'Đã loại trừ'}
                    </span>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-sm btn-dark text-white d-flex align-items-center justify-content-center gap-2 shadow-sm" onclick="zoomEditorPage(${selectedPageIndex})" title="Phóng to nội dung trang này cực nét như Ảnh 2">
                    <i data-lucide="zoom-in" style="width: 15px; height: 15px;"></i>
                    <span>Phóng to xem chi tiết (Ảnh 2)</span>
                </button>

                <button type="button" class="btn btn-sm ${page.fit_portrait ? 'btn-success text-white' : 'btn-outline-primary'} d-flex align-items-center justify-content-center gap-2" onclick="toggleFitPortraitAt(${selectedPageIndex})">
                    <i data-lucide="${page.fit_portrait ? 'check' : 'shrink'}" style="width: 14px; height: 14px;"></i>
                    <span>${page.fit_portrait ? '✓ Đang bật A4 Dọc (Cách 2)' : 'Chuyển sang khổ A4 Dọc (Cách 2)'}</span>
                </button>
                <div class="alert ${page.fit_portrait ? 'alert-success' : 'alert-light border'} py-2 px-2 small mb-0" style="font-size: 0.72rem;">
                    ${page.fit_portrait 
                        ? '<strong>Cách 2 đã bật:</strong> Trang xuất ra khổ A4 dọc (210×297mm), sơ đồ nằm ngang căn giữa trang, chữ xuôi chiều đọc bình thường từ trái sang phải.'
                        : '<strong>Khổ gốc:</strong> Trang giữ kích thước gốc. Bấm nút trên nếu muốn ép vừa vặn vào khổ A4 dọc tiêu chuẩn.'
                    }
                </div>

                ${(page.rotation_degrees || 0) > 0 ? `
                    <button type="button" class="btn btn-sm btn-warning text-dark d-flex align-items-center justify-content-center gap-2" onclick="resetRotationAt(${selectedPageIndex})">
                        <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                        <span>Đặt lại góc xoay về 0° (Nằm ngang chuẩn)</span>
                    </button>
                ` : ''}

                <div class="btn-group w-100">
                    <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center gap-1" onclick="rotatePageAt(${selectedPageIndex}, 90)">
                        <i data-lucide="rotate-cw" style="width: 13px; height: 13px;"></i>
                        <span>Xoay +90°</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center gap-1" onclick="rotatePageAt(${selectedPageIndex}, -90)">
                        <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i>
                        <span>Xoay -90°</span>
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center gap-2" onclick="toggleIncludeAt(${selectedPageIndex})">
                    <i data-lucide="${page.is_included ? 'eye-off' : 'eye'}" style="width: 14px; height: 14px;"></i>
                    <span>${page.is_included ? 'Loại khỏi bản xuất' : 'Khôi phục vào bản xuất'}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center gap-2" onclick="removePageAt(${selectedPageIndex})">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    <span>Xóa khỏi kế hoạch</span>
                </button>
            </div>
        `;
        lucide.createIcons();
    }

    function updateStatusBar() {
        const total = pages.length;
        const included = pages.filter(p => p.is_included).length;
        const excluded = total - included;

        document.getElementById('statusTotalPages').textContent = total;
        document.getElementById('statusIncludedPages').textContent = included;
        document.getElementById('statusExcludedPages').textContent = excluded;
    }

    // Lưu kế hoạch về Database qua API
    async function savePlan() {
        const title = document.getElementById('planTitleInput').value.trim();
        const btnSave = document.getElementById('btnSavePlan');

        btnSave.disabled = true;
        btnSave.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...`;

        try {
            const res = await fetch('/api/plans/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                },
                body: JSON.stringify({
                    plan_id: planId,
                    title: title,
                    pages: pages,
                })
            });

            const data = await res.json();
            if (data.success) {
                planId = data.plan_id;
                markSaved();
                // Cập nhật URL nếu là plan mới mà không reload
                history.replaceState(null, '', `/editor?plan_id=${planId}`);
            } else {
                alert('Không thể lưu kế hoạch: ' + data.message);
            }
        } catch (e) {
            alert('Lỗi kết nối máy chủ khi lưu kế hoạch: ' + e.message);
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = `<i data-lucide="save" style="width: 15px; height: 15px;"></i><span>Lưu kế hoạch</span>`;
            lucide.createIcons();
        }
    }

    // Mở modal xuất file
    function openExportModal() {
        const includedCount = pages.filter(p => p.is_included).length;
        if (includedCount === 0) {
            alert('Không có trang nào được chọn để xuất! Vui lòng chọn ít nhất 1 trang.');
            return;
        }

        document.getElementById('modalExpectedPages').textContent = `${includedCount} trang`;
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        modal.show();
    }

    // Chuyển sang chức năng xuất PDF thực tế
    async function executeExport() {
        // Tự động lưu kế hoạch trước khi xuất để đảm bảo tính bất biến
        await savePlan();

        const filename = document.getElementById('outputFilename').value.trim() || 'PDF_Workspace_Export.pdf';
        const exportAlert = document.getElementById('exportAlert');
        const btnExport = document.getElementById('btnConfirmExport');

        btnExport.disabled = true;
        btnExport.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang tạo PDF...`;
        exportAlert.style.display = 'none';

        try {
            const res = await fetch('/api/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                },
                body: JSON.stringify({
                    plan_id: planId,
                    output_filename: filename,
                    job_type: 'editor',
                })
            });

            const data = await res.json();
            if (data.success) {
                exportAlert.className = 'alert alert-success small mb-0 p-3 shadow-sm border-success';
                exportAlert.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="p-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                            <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        </div>
                        <strong class="text-success" style="font-size: 0.95rem;">Xuất PDF thành công!</strong>
                    </div>
                    <p class="text-muted small mb-3">Tệp <strong>${filename}</strong> đã được đóng băng an toàn. Hãy xem trước nội dung hoặc tải về máy:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-3" onclick="reviewPdf('/exports/download?token=${data.download_token}&inline=1', '${filename}', '/exports/download?token=${data.download_token}')">
                            <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                            <span>Xem trước (Review PDF)</span>
                        </button>
                        <a href="/exports/download?token=${data.download_token}" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm px-3" target="_blank" download="${filename}">
                            <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                            <span>Tải xuống ngay</span>
                        </a>
                        <a href="/exports" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-2">
                            <span>Lịch sử xuất &rarr;</span>
                        </a>
                    </div>
                `;
                exportAlert.style.display = 'block';
                btnExport.style.display = 'none';
                lucide.createIcons();
            } else {
                exportAlert.className = 'alert alert-danger small mb-0';
                exportAlert.textContent = data.message || 'Lỗi khi xuất PDF.';
                exportAlert.style.display = 'block';
                btnExport.disabled = false;
                btnExport.innerHTML = `Thử lại`;
            }
        } catch (e) {
            exportAlert.className = 'alert alert-danger small mb-0';
            exportAlert.textContent = 'Lỗi máy chủ: ' + e.message;
            exportAlert.style.display = 'block';
            btnExport.disabled = false;
            btnExport.innerHTML = `Thử lại`;
        }
    }

    // Phím tắt bàn phím: Ctrl+Z (Undo), Ctrl+Y (Redo), Ctrl+S (Save)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
            e.preventDefault();
            undo();
        } else if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
            e.preventDefault();
            redo();
        } else if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            savePlan();
        }
    });

    // Render ban đầu khi mở trang
    renderPagesGrid();
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
