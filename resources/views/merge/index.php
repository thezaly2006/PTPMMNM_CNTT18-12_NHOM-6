<?php ob_start(); ?>

<!-- PDF.js & SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<style>
    .merge-container {
        max-width: 1100px;
        margin: 0 auto;
    }
    .merge-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .merge-queue-item {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: grab;
        transition: all 0.15s ease;
    }
    .merge-queue-item:hover {
        border-color: #6366f1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .merge-queue-item.sortable-ghost {
        opacity: 0.4;
        background: #eef2ff;
        border-style: dashed;
    }
    #mergeQueue {
        max-height: 420px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .source-page-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        position: relative;
    }
    .source-page-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .source-page-card.checked {
        border-color: #4f46e5;
        background: rgba(99, 102, 241, 0.03);
    }
    .source-thumb-box {
        height: 120px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        overflow: hidden;
        position: relative;
    }
    .source-thumb-box canvas {
        max-height: 105px;
        max-width: 90%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.12);
        border-radius: 3px;
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
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        overflow: hidden;
        flex-shrink: 0;
    }
    .queue-thumb-box canvas {
        max-height: 44px;
        max-width: 34px;
    }
</style>

<div class="merge-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Ghép nhiều tài liệu PDF</h4>
            <p class="text-muted small mb-0">Chọn các trang từ nhiều tài liệu khác nhau, sắp xếp thứ tự và hợp nhất thành một tệp duy nhất.</p>
        </div>
        <a href="/editor" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
            <span>Chuyển sang PDF Editor</span>
        </a>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Chọn tài liệu và chọn trang nguồn -->
        <div class="col-lg-6">
            <div class="merge-card p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">1. Chọn trang từ tài liệu nguồn</h6>

                <?php if (empty($userDocuments)): ?>
                    <div class="text-center py-4 text-muted small">
                        <i data-lucide="folder-plus" style="width: 36px; height: 36px; color: #94a3b8;" class="mb-2"></i>
                        <p class="mb-2">Chưa có tệp PDF nào để ghép.</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">Tải tệp lên ngay</button>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Chọn tài liệu PDF</label>
                        <select class="form-select form-select-sm" id="selectDocument" onchange="loadSourceDocPages()">
                            <option value="">-- Chọn một tài liệu --</option>
                            <?php foreach ($userDocuments as $doc): ?>
                                <option value="<?php echo $doc['id']; ?>" data-name="<?php echo htmlspecialchars($doc['original_filename']); ?>" data-pages="<?php echo $doc['total_pages']; ?>">
                                    <?php echo htmlspecialchars($doc['original_filename']); ?> (<?php echo $doc['total_pages']; ?> trang)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="sourcePagePickerArea" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-dark">Chọn trang cần đưa vào hàng đợi:</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="selectAllSourcePages()">Tất cả</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="deselectAllSourcePages()">Bỏ chọn</button>
                            </div>
                        </div>

                        <div class="row g-2 mb-3" id="sourcePagesList" style="max-height: 380px; overflow-y: auto;">
                            <!-- Checkbox danh sách trang -->
                        </div>

                        <button type="button" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1" onclick="addSelectedPagesToMergeQueue()">
                            <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i>
                            <span>Thêm các trang đã chọn vào danh sách ghép</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột phải: Hàng đợi ghép trang và cấu hình xuất -->
        <div class="col-lg-6">
            <div class="merge-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">2. Thứ tự trang đầu ra (<span id="mergeCount">0</span> trang)</h6>
                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="clearMergeQueue()" id="btnClearQueue" style="display: none;">
                        Xóa tất cả
                    </button>
                </div>

                <p class="text-muted small mb-3">Bạn có thể kéo thả để đổi vị trí, xoay góc hoặc xóa từng trang. Ví dụ: Trang 2 (Tệp A), Trang 1 (Tệp B), Trang 1 (Tệp A).</p>

                <div id="emptyQueueMessage" class="text-center py-4 border rounded-3 bg-light text-muted small">
                    <i data-lucide="layers" style="width: 32px; height: 32px; color: #cbd5e1;" class="mb-2"></i>
                    <p class="mb-0">Danh sách ghép đang trống. Hãy chọn trang từ cột bên trái.</p>
                </div>

                <div id="mergeQueue" class="mb-4">
                    <!-- Các trang trong hàng đợi ghép -->
                </div>

                <!-- Cấu hình tên tệp xuất -->
                <div class="border-top pt-3">
                    <div class="mb-3">
                        <label for="mergeOutputFilename" class="form-label small fw-semibold text-secondary">Tên tệp PDF đầu ra</label>
                        <input type="text" class="form-control form-control-sm" id="mergeOutputFilename" value="Tai_Lieu_Ghep_Hop_Nhat.pdf" placeholder="Nhap_ten_file.pdf">
                    </div>

                    <div id="mergeResultAlert" style="display: none;" class="alert small mb-3"></div>

                    <button type="button" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" id="btnExecuteMerge" disabled onclick="executeMerge()">
                        <i data-lucide="combine" style="width: 16px; height: 16px;"></i>
                        <span>Thực hiện ghép PDF</span>
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

    // Khởi tạo Sortable kéo thả sắp xếp hàng đợi
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
                        <button type="button" class="btn btn-xs btn-link text-primary p-0" title="Phóng to xem chi tiết (Ảnh 2)" onclick="zoomMergePage(${currentDocId}, ${i}, ${currentDocPages}, '${escapeJs(currentDocName)}')">
                            <i data-lucide="maximize-2" style="width: 12px; height: 12px;"></i>
                        </button>
                    </div>
                    <div class="source-thumb-box" onclick="zoomMergePage(${currentDocId}, ${i}, ${currentDocPages}, '${escapeJs(currentDocName)}')" title="Nhấp để phóng to xem trang cực nét (Ảnh 2)">
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
        lucide.createIcons();
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

    // Render thumbnail trang nguồn
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

    // Phóng to trang xem chi tiết (chuẩn Ảnh 2)
    function zoomMergePage(docId, pageNum, totalPages, docName) {
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

    function zoomMergeQueueItem(idx) {
        if (!mergeQueue[idx]) return;
        const item = mergeQueue[idx];
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
                    <span class="badge bg-primary px-2">#${idx + 1}</span>
                    <div class="queue-thumb-box" onclick="zoomMergeQueueItem(${idx})" title="Nhấp phóng to xem trang (Ảnh 2)">
                        <canvas id="q_thumb_canvas_${idx}"></canvas>
                    </div>
                    <div class="text-truncate">
                        <span class="fw-semibold text-dark small d-block text-truncate">${item.original_filename}</span>
                        <span class="text-muted small">Trang gốc: <strong>Trang ${item.source_page_number}</strong></span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" title="Phóng to xem trang (Ảnh 2)" onclick="zoomMergeQueueItem(${idx})">
                        <i data-lucide="zoom-in" style="width: 12px; height: 12px;"></i>
                    </button>
                    <button type="button" class="btn btn-xs ${item.fit_portrait ? 'btn-success text-white' : 'btn-light border'} py-0 px-2" title="Chuyển sang khổ A4 dọc (Fit to Portrait)" onclick="toggleQueueFitPortrait(${idx})">
                        <i data-lucide="${item.fit_portrait ? 'check' : 'shrink'}" style="width: 12px; height: 12px;"></i>
                        <span style="font-size: 0.75rem;">${item.fit_portrait ? '✓ A4 Dọc' : 'Khổ gốc'}</span>
                    </button>
                    ${(item.rotation_degrees || 0) > 0 ? `
                        <button type="button" class="btn btn-xs btn-warning text-dark py-0 px-1" title="Đặt lại góc 0° nằm ngang chuẩn" onclick="resetQueueRotation(${idx})">
                            <span style="font-size: 0.75rem; font-weight: bold;">0°</span>
                        </button>
                    ` : ''}
                    <button type="button" class="btn btn-xs btn-light border py-0 px-2" title="Xoay 90°" onclick="rotateQueueItem(${idx})">
                        <i data-lucide="rotate-cw" style="width: 12px; height: 12px;"></i>
                        <span style="font-size: 0.75rem;">${item.rotation_degrees || 0}°</span>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger p-1" title="Xóa trang" onclick="removeQueueItem(${idx})">
                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
            `;
            queueEl.appendChild(el);
            renderMergeSourceThumbnail(item.document_id, item.source_page_number, `q_thumb_canvas_${idx}`);
        });

        lucide.createIcons();
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
            if (data.success) {
                alertEl.className = 'alert alert-success small mb-3 p-3 shadow-sm border-success';
                alertEl.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="p-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                            <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        </div>
                        <strong class="text-success" style="font-size: 0.95rem;">Ghép PDF thành công!</strong>
                    </div>
                    <p class="text-muted small mb-3">Tệp đã hợp nhất gồm <strong>${mergeQueue.length} trang</strong>. Hãy xem trước nội dung hoặc tải về máy:</p>
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
                alertEl.style.display = 'block';
                lucide.createIcons();
            } else {
                alertEl.className = 'alert alert-danger small mb-3';
                alertEl.textContent = 'Lỗi khi ghép: ' + data.message;
                alertEl.style.display = 'block';
            }
        } catch (e) {
            alertEl.className = 'alert alert-danger small mb-3';
            alertEl.textContent = 'Lỗi máy chủ: ' + e.message;
            alertEl.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="combine" style="width: 16px; height: 16px;"></i><span>Thực hiện ghép PDF</span>`;
            lucide.createIcons();
        }
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
