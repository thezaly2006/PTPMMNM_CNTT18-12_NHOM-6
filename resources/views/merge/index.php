<?php ob_start(); ?>

<!-- PDF.js & SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<style>
    .merge-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .merge-queue-item {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 9px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: grab;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
    }
    .merge-queue-item:hover {
        border-color: #10b981;
        box-shadow: 0 8px 18px -2px rgba(16, 185, 129, 0.16);
        transform: translateY(-2px);
    }
    .merge-queue-item.sortable-ghost {
        opacity: 0.35;
        background: #ecfdf5;
        border: 2px dashed #10b981;
    }
    #mergeQueue {
        max-height: 440px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .source-page-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
        position: relative;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
    }
    .source-page-card:hover {
        border-color: #10b981;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.14);
    }
    .source-page-card.checked {
        border-color: #059669;
        background: rgba(16, 185, 129, 0.04);
        box-shadow: 0 0 0 1px #059669;
    }
    .source-thumb-box {
        height: 125px;
        background: radial-gradient(circle, #ffffff 0%, #f8fafc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        overflow: hidden;
        position: relative;
    }
    .source-thumb-box canvas {
        max-height: 110px;
        max-width: 90%;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        border-radius: 4px;
        transition: transform 0.2s ease;
    }
    .source-thumb-box:hover .zoom-badge-hint {
        opacity: 1 !important;
        transform: translateX(-50%) translateY(0) !important;
    }
    .queue-thumb-box {
        width: 38px;
        height: 48px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .queue-thumb-box canvas {
        max-height: 44px;
        max-width: 34px;
    }
</style>

<div class="merge-container">
    <!-- Top Header Hero -->
    <div class="card card-aurora p-4 mb-4 position-relative overflow-hidden" style="border-left: 5px solid #10b981 !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 rounded-pill bg-success-subtle text-success border border-success-subtle">
                    <i data-lucide="combine" style="width: 14px; height: 14px; color: #059669;"></i>
                    <span class="fw-bold" style="font-size: 0.76rem;">Công cụ Ghép Nhiều Tài Liệu PDF &bull; Đa luồng tốc độ cao</span>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Ghép nhiều tài liệu PDF</h3>
                <p class="text-muted small mb-0">Hợp nhất các trang từ nhiều tài liệu khác nhau theo thứ tự tùy chỉnh, kéo thả trực quan và chuẩn hóa khổ A4.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 rounded-pill px-3 shadow-2xs hover-lift" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload-cloud" style="width: 14px; height: 14px;"></i>
                    <span>Nạp thêm PDF</span>
                </button>
                <a href="/editor" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 rounded-pill px-3 shadow-2xs hover-lift">
                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                    <span>Mở PDF Editor</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Chọn tài liệu và chọn trang nguồn -->
        <div class="col-lg-6">
            <div class="card card-aurora p-4 h-100" id="mergeSourceCol">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span class="badge text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; font-size: 0.8rem; background: linear-gradient(135deg, #059669, #10b981);">1</span>
                        <span>Chọn trang nguồn</span>
                    </h5>
                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.74rem;">Tài liệu trong kho</span>
                </div>

                <?php if (empty($userDocuments)): ?>
                    <div class="text-center py-5 text-muted small">
                        <div class="p-3 bg-light rounded-circle d-inline-flex mb-2">
                            <i data-lucide="folder-plus" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Chưa có tệp PDF nào</h6>
                        <p class="mb-3 text-muted">Vui lòng tải tệp PDF lên để bắt đầu ghép trang.</p>
                        <button class="btn btn-primary btn-sm rounded-pill px-4 hover-lift shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i data-lucide="upload" style="width: 14px; height: 14px;" class="me-1"></i>
                            <span>Tải tệp lên ngay</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Chọn tài liệu PDF cần lấy trang:</label>
                        <select class="form-select form-select-sm rounded-3 py-2 border-success-subtle shadow-2xs" id="selectDocument" onchange="loadSourceDocPages()" style="font-weight: 500;">
                            <option value="">-- Chọn một tài liệu --</option>
                            <?php foreach ($userDocuments as $doc): ?>
                                <option value="<?php echo $doc['id']; ?>" data-name="<?php echo htmlspecialchars($doc['original_filename']); ?>" data-pages="<?php echo $doc['total_pages']; ?>">
                                    📄 <?php echo htmlspecialchars($doc['original_filename']); ?> (<?php echo $doc['total_pages']; ?> trang)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="sourcePagePickerArea" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                            <span class="small fw-bold text-dark">Các trang trong tài liệu:</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-xs btn-outline-success py-1 px-2 fw-semibold" onclick="selectAllSourcePages()">Chọn tất cả</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" onclick="deselectAllSourcePages()">Bỏ chọn</button>
                            </div>
                        </div>

                        <div class="row g-2 mb-3" id="sourcePagesList" style="max-height: 380px; overflow-y: auto; padding: 2px;">
                            <!-- Checkbox danh sách trang -->
                        </div>

                        <button type="button" class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-pill shadow-sm hover-lift" onclick="addSelectedPagesToMergeQueue()" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border: none;">
                            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                            <span class="fw-bold">Thêm các trang đã chọn vào danh sách ghép</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột phải: Hàng đợi ghép trang và cấu hình xuất -->
        <div class="col-lg-6">
            <div class="card card-aurora p-4">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span class="badge text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; font-size: 0.8rem; background: linear-gradient(135deg, #059669, #10b981);">2</span>
                        <span>Thứ tự trang đầu ra (<span id="mergeCount" class="text-success fw-bold">0</span> trang)</span>
                    </h5>
                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1" onclick="clearMergeQueue()" id="btnClearQueue" style="display: none;">
                        <i data-lucide="trash-2" style="width: 12px; height: 12px;" class="me-1"></i>Xóa tất cả
                    </button>
                </div>

                <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                    Kéo thả để sắp xếp lại thứ tự, xoay góc hoặc chỉnh chuẩn khổ A4 dọc cho từng trang thành phẩm.
                </p>

                <div id="emptyQueueMessage" class="text-center py-5 border rounded-3 bg-light text-muted small">
                    <div class="p-3 bg-white rounded-circle d-inline-flex shadow-2xs mb-2">
                        <i data-lucide="layers" style="width: 32px; height: 32px; color: #cbd5e1;"></i>
                    </div>
                    <p class="mb-0 fw-bold text-dark">Danh sách ghép đang trống</p>
                    <span class="text-muted" style="font-size: 0.76rem;">Hãy chọn các trang từ cột bên trái rồi bấm nút thêm.</span>
                </div>

                <div id="mergeQueue" class="mb-3">
                    <!-- Các trang trong hàng đợi ghép -->
                </div>

                <!-- Cấu hình tên tệp xuất -->
                <div class="border-top pt-3">
                    <div class="mb-3">
                        <label for="mergeOutputFilename" class="form-label small fw-bold text-secondary d-flex justify-content-between">
                            <span>Tên tệp PDF xuất ra:</span>
                            <span class="badge bg-warning text-dark border border-warning" style="font-size: 0.72rem;">
                                <i data-lucide="coins" style="width: 11px; height: 11px;" class="me-1"></i>Chi phí: 5 tokens
                            </span>
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i data-lucide="file" style="width: 14px; height: 14px;"></i>
                            </span>
                            <input type="text" class="form-control form-control-sm border-start-0 fw-semibold" id="mergeOutputFilename" value="Tai_Lieu_Ghep_Hop_Nhat.pdf" placeholder="Nhap_ten_file.pdf">
                        </div>
                    </div>

                    <div id="mergeResultAlert" style="display: none;" class="alert small mb-3 rounded-3"></div>

                    <button type="button" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-pill shadow hover-lift" id="btnExecuteMerge" disabled onclick="executeMerge()" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border: none;">
                        <i data-lucide="combine" style="width: 17px; height: 17px;"></i>
                        <span class="fw-bold">Thực hiện ghép PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDocPages = 0;
    let currentDocId = null;
    let currentDocName = '';
    let mergeQueue = [];

    const queueEl = document.getElementById('mergeQueue');
    new Sortable(queueEl, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            if (evt.oldIndex !== evt.newIndex) {
                const moved = mergeQueue.splice(evt.oldIndex, 1)[0];
                mergeQueue.splice(evt.newIndex, 0, moved);
                renderMergeQueue();
            }
        }
    });

    function loadSourceDocPages() {
        const select = document.getElementById('selectDocument');
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            document.getElementById('sourcePagePickerArea').style.display = 'none';
            return;
        }

        currentDocId = parseInt(opt.value);
        currentDocName = opt.dataset.name;
        currentDocPages = parseInt(opt.dataset.pages);

        const listEl = document.getElementById('sourcePagesList');
        listEl.innerHTML = '';

        for (let i = 1; i <= currentDocPages; i++) {
            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4';
            col.innerHTML = `
                <div class="source-page-card checked" id="src_card_${i}">
                    <div class="d-flex justify-content-between align-items-center p-1 px-2 border-bottom bg-white">
                        <div class="form-check m-0">
                            <input class="form-check-input src-page-check" type="checkbox" value="${i}" id="chk_p_${i}" checked onchange="toggleSrcCardCheck(${i})">
                            <label class="form-check-label small fw-bold" for="chk_p_${i}" style="font-size: 0.72rem;">Trang ${i}</label>
                        </div>
                        <button type="button" class="btn btn-xs btn-link text-success p-0" title="Phóng to xem chi tiết" onclick="zoomMergePage(${currentDocId}, ${i}, ${currentDocPages}, '${escapeJs(currentDocName)}')">
                            <i data-lucide="maximize-2" style="width: 12px; height: 12px;"></i>
                        </button>
                    </div>
                    <div class="source-thumb-box" onclick="zoomMergePage(${currentDocId}, ${i}, ${currentDocPages}, '${escapeJs(currentDocName)}')" title="Nhấp để phóng to xem trang">
                        <canvas id="merge_src_canvas_${i}"></canvas>
                        <span class="zoom-badge-hint position-absolute" style="opacity: 0; bottom: 4px; left: 50%; transform: translateX(-50%) translateY(3px); background: rgba(15,23,42,0.8); color: #fff; font-size: 0.65rem; padding: 2px 8px; border-radius: 12px; transition: all 0.2s ease; pointer-events: none; white-space: nowrap;">
                            🔍 Phóng to
                        </span>
                    </div>
                </div>
            `;
            listEl.appendChild(col);
            renderMergeSourceThumbnail(currentDocId, i, `merge_src_canvas_${i}`);
        }

        document.getElementById('sourcePagePickerArea').style.display = 'block';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function escapeJs(str) {
        return (str || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    function toggleSrcCardCheck(i) {
        const chk = document.getElementById(`chk_p_${i}`);
        const card = document.getElementById(`src_card_${i}`);
        if (chk && card) {
            card.classList.toggle('checked', chk.checked);
        }
    }

    async function renderMergeSourceThumbnail(docId, pageNum, canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        try {
            if (typeof pdfCache === 'undefined') window.pdfCache = {};
            if (!window.pdfCache[docId]) {
                const url = `/documents/view?id=${docId}`;
                window.pdfCache[docId] = await pdfjsLib.getDocument(url).promise;
            }
            const pdf = window.pdfCache[docId];
            const page = await pdf.getPage(pageNum);
            const viewport = page.getViewport({ scale: 0.22 });

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const ctx = canvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        } catch (e) {
            console.error('Lỗi thumbnail nguồn:', e);
        }
    }

    function zoomMergePage(docId, pageNum, totalPages, docName) {
        if (typeof window.openPageZoomModal === 'function') {
            window.openPageZoomModal({
                docId: docId,
                pageNum: pageNum,
                totalPages: totalPages,
                title: `${docName} — Trang ${pageNum}`,
                rotation: 0,
                scale: 1.75,
                onPrev: function() {
                    if (pageNum > 1) zoomMergePage(docId, pageNum - 1, totalPages, docName);
                },
                onNext: function() {
                    if (pageNum < totalPages) zoomMergePage(docId, pageNum + 1, totalPages, docName);
                }
            });
        }
    }

    function zoomMergeQueueItem(idx) {
        if (!mergeQueue[idx]) return;
        const item = mergeQueue[idx];
        if (typeof window.openPageZoomModal === 'function') {
            window.openPageZoomModal({
                docId: item.document_id,
                pageNum: item.source_page_number,
                totalPages: mergeQueue.length,
                title: `${item.original_filename} — Trang ${item.source_page_number} (Hàng đợi #${idx + 1})`,
                rotation: item.rotation_degrees || 0,
                fitPortrait: !!item.fit_portrait,
                scale: 1.75,
                onRotate: function(newRot) {
                    item.rotation_degrees = newRot;
                    renderMergeQueue();
                },
                onPrev: function() {
                    if (idx > 0) zoomMergeQueueItem(idx - 1);
                },
                onNext: function() {
                    if (idx < mergeQueue.length - 1) zoomMergeQueueItem(idx + 1);
                }
            });
        }
    }

    function selectAllSourcePages() {
        document.querySelectorAll('.src-page-check').forEach(chk => {
            chk.checked = true;
            const pNum = chk.value;
            const card = document.getElementById(`src_card_${pNum}`);
            if (card) card.classList.add('checked');
        });
    }

    function deselectAllSourcePages() {
        document.querySelectorAll('.src-page-check').forEach(chk => {
            chk.checked = false;
            const pNum = chk.value;
            const card = document.getElementById(`src_card_${pNum}`);
            if (card) card.classList.remove('checked');
        });
    }

    function addSelectedPagesToMergeQueue() {
        const checked = Array.from(document.querySelectorAll('.src-page-check:checked')).map(c => parseInt(c.value));
        if (checked.length === 0) {
            alert('Vui lòng tích chọn ít nhất 1 trang.');
            return;
        }

        checked.forEach(pNum => {
            mergeQueue.push({
                document_id: currentDocId,
                original_filename: currentDocName,
                source_page_number: pNum,
                rotation_degrees: 0,
            });
        });

        renderMergeQueue();
    }

    function renderMergeQueue() {
        queueEl.innerHTML = '';
        const emptyMsg = document.getElementById('emptyQueueMessage');
        const btnClear = document.getElementById('btnClearQueue');
        const btnExec = document.getElementById('btnExecuteMerge');
        const countSpan = document.getElementById('mergeCount');

        countSpan.textContent = mergeQueue.length;

        if (mergeQueue.length === 0) {
            emptyMsg.style.display = 'block';
            btnClear.style.display = 'none';
            btnExec.disabled = true;
            return;
        }

        emptyMsg.style.display = 'none';
        btnClear.style.display = 'inline-block';
        btnExec.disabled = false;

        mergeQueue.forEach((item, idx) => {
            const el = document.createElement('div');
            el.className = 'merge-queue-item';
            el.innerHTML = `
                <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1">
                    <span class="badge text-white px-2" style="background: linear-gradient(135deg, #059669, #10b981);">#${idx + 1}</span>
                    <div class="queue-thumb-box" onclick="zoomMergeQueueItem(${idx})" title="Nhấp phóng to xem trang">
                        <canvas id="q_thumb_canvas_${idx}"></canvas>
                    </div>
                    <div class="text-truncate">
                        <span class="fw-semibold text-dark small d-block text-truncate">${item.original_filename}</span>
                        <span class="text-muted small">Trang gốc: <strong>Trang ${item.source_page_number}</strong></span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 rounded-pill" title="Phóng to xem trang" onclick="zoomMergeQueueItem(${idx})">
                        <i data-lucide="zoom-in" style="width: 12px; height: 12px;"></i>
                    </button>
                    <button type="button" class="btn btn-xs ${item.fit_portrait ? 'btn-success text-white' : 'btn-light border'} py-0 px-2 rounded-pill" title="Chuyển sang khổ A4 dọc (Fit to Portrait)" onclick="toggleQueueFitPortrait(${idx})">
                        <i data-lucide="${item.fit_portrait ? 'check' : 'shrink'}" style="width: 12px; height: 12px;"></i>
                        <span style="font-size: 0.72rem;">${item.fit_portrait ? '✓ A4 Dọc' : 'Khổ gốc'}</span>
                    </button>
                    ${(item.rotation_degrees || 0) > 0 ? `
                        <button type="button" class="btn btn-xs btn-warning text-dark py-0 px-2 rounded-pill" title="Đặt lại góc 0°" onclick="resetQueueRotation(${idx})">
                            <span style="font-size: 0.72rem; font-weight: bold;">0°</span>
                        </button>
                    ` : ''}
                    <button type="button" class="btn btn-xs btn-light border py-0 px-2 rounded-pill" title="Xoay 90°" onclick="rotateQueueItem(${idx})">
                        <i data-lucide="rotate-cw" style="width: 12px; height: 12px;"></i>
                        <span style="font-size: 0.72rem;">${item.rotation_degrees || 0}°</span>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger p-1 rounded-pill" title="Xóa trang" onclick="removeQueueItem(${idx})">
                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
            `;
            queueEl.appendChild(el);
            renderMergeSourceThumbnail(item.document_id, item.source_page_number, `q_thumb_canvas_${idx}`);
        });

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function toggleQueueFitPortrait(idx) {
        mergeQueue[idx].fit_portrait = mergeQueue[idx].fit_portrait ? 0 : 1;
        renderMergeQueue();
    }

    function resetQueueRotation(idx) {
        mergeQueue[idx].rotation_degrees = 0;
        renderMergeQueue();
    }

    function rotateQueueItem(idx) {
        mergeQueue[idx].rotation_degrees = ((mergeQueue[idx].rotation_degrees || 0) + 90) % 360;
        renderMergeQueue();
    }

    function removeQueueItem(idx) {
        mergeQueue.splice(idx, 1);
        renderMergeQueue();
    }

    function clearMergeQueue() {
        mergeQueue = [];
        renderMergeQueue();
    }

    async function executeMerge() {
        if (!window.IS_LOGGED_IN) {
            if (typeof window.showRequireLoginModal === 'function') {
                window.showRequireLoginModal(() => {
                    executeMerge();
                });
            } else {
                window.location.href = '/login';
            }
            return;
        }

        if (mergeQueue.length === 0) return;

        const filename = document.getElementById('mergeOutputFilename').value.trim() || 'Tai_Lieu_Ghep.pdf';
        const alertEl = document.getElementById('mergeResultAlert');
        const btn = document.getElementById('btnExecuteMerge');

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang ghép các trang...`;
        alertEl.style.display = 'none';

        try {
            const res = await fetch('/api/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                },
                body: JSON.stringify({
                    job_type: 'merge',
                    output_filename: filename,
                    pages: mergeQueue,
                })
            });

            const data = await res.json();

            if (res.status === 402 || data.out_of_tokens) {
                if (typeof window.showOutOfTokensModal === 'function') {
                    window.showOutOfTokensModal({
                        message: data.message || 'Bạn đã sử dụng hết Token! Vui lòng nâng cấp gói để tiếp tục ghép tài liệu.'
                    });
                } else {
                    alert(data.message || 'Hết token');
                }
                return;
            }

            if (data.success) {
                if (data.remaining_tokens !== undefined && typeof window.updateUserTokensBadge === 'function') {
                    window.updateUserTokensBadge(data.remaining_tokens);
                }

                alertEl.className = 'alert alert-success small mb-3 p-3 shadow-sm border-success rounded-4';
                alertEl.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="p-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                            <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        </div>
                        <strong class="text-success" style="font-size: 0.95rem;">Ghép PDF thành công!</strong>
                    </div>
                    <p class="text-muted small mb-3">Tệp đã hợp nhất gồm <strong>${mergeQueue.length} trang</strong>. Hãy xem trước nội dung hoặc tải về máy:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm px-3 rounded-pill hover-lift" onclick="reviewPdf('/exports/download?token=${data.download_token}&inline=1', '${filename}', '/exports/download?token=${data.download_token}')">
                            <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                            <span>Xem trước (Review PDF)</span>
                        </button>
                        <a href="/exports/download?token=${data.download_token}" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm px-3 rounded-pill hover-lift" target="_blank" download="${filename}">
                            <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                            <span>Tải xuống ngay</span>
                        </a>
                        <a href="/exports" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-3 rounded-pill">
                            <span>Lịch sử xuất &rarr;</span>
                        </a>
                    </div>
                `;
                alertEl.style.display = 'block';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                alertEl.className = 'alert alert-danger small mb-3 rounded-3';
                alertEl.textContent = 'Lỗi khi ghép: ' + data.message;
                alertEl.style.display = 'block';
            }
        } catch (e) {
            alertEl.className = 'alert alert-danger small mb-3 rounded-3';
            alertEl.textContent = 'Lỗi máy chủ: ' + e.message;
            alertEl.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="combine" style="width: 17px; height: 17px;"></i><span>Thực hiện ghép PDF</span>`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    async function refreshMergeDocuments() {
        try {
            const res = await fetch('/api/documents/list');
            const data = await res.json();
            if (!data.success || !Array.isArray(data.documents)) return;

            const select = document.getElementById('selectDocument');
            if (select) {
                const currentVal = select.value;
                select.innerHTML = '<option value="">-- Chọn một tài liệu --</option>';
                data.documents.forEach(doc => {
                    const opt = document.createElement('option');
                    opt.value = doc.id;
                    opt.dataset.name = doc.original_filename;
                    opt.dataset.pages = doc.total_pages;
                    opt.textContent = `📄 ${doc.original_filename} (${doc.total_pages} trang)`;
                    if (String(doc.id) === String(currentVal)) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
            } else {
                const container = document.getElementById('mergeSourceCol');
                if (container && data.documents.length > 0) {
                    let optionsHtml = '<option value="">-- Chọn một tài liệu --</option>';
                    data.documents.forEach(doc => {
                        optionsHtml += `<option value="${doc.id}" data-name="${escapeHtml(doc.original_filename)}" data-pages="${doc.total_pages}">📄 ${escapeHtml(doc.original_filename)} (${doc.total_pages} trang)</option>`;
                    });
                    container.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <span class="badge text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; font-size: 0.8rem; background: linear-gradient(135deg, #059669, #10b981);">1</span>
                                <span>Chọn trang nguồn</span>
                            </h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Chọn tài liệu PDF:</label>
                            <select class="form-select form-select-sm rounded-3 py-2 border-success-subtle shadow-2xs" id="selectDocument" onchange="loadSourceDocPages()">
                                ${optionsHtml}
                            </select>
                        </div>
                        <div id="sourcePagePickerArea" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                                <span class="small fw-bold text-dark">Các trang trong tài liệu:</span>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-xs btn-outline-success py-1 px-2" onclick="selectAllSourcePages()">Chọn tất cả</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" onclick="deselectAllSourcePages()">Bỏ chọn</button>
                                </div>
                            </div>
                            <div class="row g-2 mb-3" id="sourcePagesList" style="max-height: 380px; overflow-y: auto;"></div>
                            <button type="button" class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1 rounded-pill shadow-sm" onclick="addSelectedPagesToMergeQueue()" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border: none;">
                                <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i>
                                <span class="fw-bold">Thêm các trang đã chọn vào danh sách ghép</span>
                            </button>
                        </div>
                    `;
                    if (window.lucide) lucide.createIcons();
                }
            }
        } catch (e) {
            console.error('Error refreshing merge documents:', e);
        }
    }

    window.addEventListener('auth:updated', () => {
        refreshMergeDocuments();
    });
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
