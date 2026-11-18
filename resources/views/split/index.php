<?php ob_start(); ?>

<style>
    .split-container {
        max-width: 1080px;
        margin: 0 auto;
    }
    .split-page-thumb-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }
    .split-page-thumb-card:hover {
        border-color: #f43f5e;
        box-shadow: 0 12px 24px -4px rgba(244, 63, 94, 0.2);
        transform: translateY(-3px);
    }
    .split-thumb-box {
        height: 140px;
        background: radial-gradient(circle, #ffffff 0%, #f8fafc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        overflow: hidden;
        position: relative;
    }
    .split-thumb-box canvas {
        max-height: 125px;
        max-width: 90%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        transition: transform 0.2s ease;
    }
    .split-thumb-box:hover .zoom-badge-hint {
        opacity: 1 !important;
        transform: translateX(-50%) translateY(0) !important;
    }
    .split-group-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 14px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    .split-group-card:hover {
        border-color: rgba(244, 63, 94, 0.4);
        box-shadow: 0 6px 18px rgba(244, 63, 94, 0.08);
    }
</style>

<div class="split-container">
    <!-- Header Hero Section -->
    <div class="card card-aurora p-4 mb-4 position-relative overflow-hidden" style="border-left: 5px solid #f43f5e !important;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-2 rounded-pill bg-danger-subtle text-danger border border-danger-subtle">
                    <i data-lucide="split" style="width: 14px; height: 14px; color: #ea580c;"></i>
                    <span class="fw-bold" style="font-size: 0.76rem;">Công cụ Tách Đa Phân Đoạn &bull; Chuẩn hóa vector</span>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Tách tài liệu PDF</h3>
                <p class="text-muted small mb-0">Chia tách tài liệu PDF thành nhiều tệp độc lập theo dải trang tùy chọn, giữ nguyên 100% chất lượng đồ họa và phông chữ.</p>
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

    <!-- Bước 1: Chọn tài liệu cần tách -->
    <div class="card card-aurora p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="badge text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; font-size: 0.8rem; background: linear-gradient(135deg, #ea580c, #f43f5e);">1</span>
                <span>Chọn tài liệu cần chia tách</span>
            </h5>
            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.74rem;">Tài liệu nguồn</span>
        </div>

        <?php if (empty($userDocuments)): ?>
            <div class="text-center py-5 text-muted small">
                <div class="p-3 bg-light rounded-circle d-inline-flex mb-3">
                    <i data-lucide="folder-open" style="width: 44px; height: 44px; color: #94a3b8;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Chưa có tài liệu nào trong kho</h6>
                <p class="mb-3 text-muted">Vui lòng tải tệp PDF lên để bắt đầu chọn trang và chia tách.</p>
                <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload-cloud" style="width: 14px; height: 14px;" class="me-1"></i>
                    <span>Tải PDF lên ngay</span>
                </button>
            </div>
        <?php else: ?>
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <label class="form-label small fw-bold text-secondary mb-1">Tài liệu cần tách:</label>
                    <select class="form-select form-select-sm rounded-3 py-2 border-primary-subtle" id="selectDocToSplit" onchange="window.location.href='/split?doc_id=' + this.value" style="font-weight: 500;">
                        <?php foreach ($userDocuments as $doc): ?>
                            <option value="<?php echo $doc['id']; ?>" <?php echo ($selectedDoc && $selectedDoc['id'] === $doc['id']) ? 'selected' : ''; ?>>
                                📄 <?php echo htmlspecialchars($doc['original_filename']); ?> &mdash; <?php echo (int)$doc['total_pages']; ?> trang (<?php echo format_bytes((int)$doc['file_size']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary d-none d-md-block">&nbsp;</label>
                    <div class="p-2 border rounded-3 bg-light text-center small text-muted">
                        Khả dụng: <strong class="text-dark" id="docTotalPages"><?php echo $selectedDoc ? (int)$selectedDoc['total_pages'] : 0; ?></strong> trang trong tệp
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($selectedDoc): ?>
        <!-- Bước 2: Xem trước nội dung các trang -->
        <div class="card card-aurora p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span class="badge text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; font-size: 0.8rem; background: linear-gradient(135deg, #ea580c, #f43f5e);">2</span>
                    <span>Xem trước nội dung các trang</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size: 0.72rem;">
                        <?php echo (int)$selectedDoc['total_pages']; ?> trang
                    </span>
                </h5>
                <div class="small text-muted d-none d-sm-block">
                    <i data-lucide="info" style="width: 14px; height: 14px; color: #ea580c;" class="me-1"></i>
                    Bấm vào trang để <strong>phóng to xem chi tiết</strong>
                </div>
            </div>

            <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                Đối chiếu hình ảnh thực tế của từng trang bên dưới để thiết lập khoảng tách chính xác:
            </p>

            <div class="row g-3" id="splitPagesThumbGrid" style="max-height: 420px; overflow-y: auto; padding: 4px;">
                <?php for ($p = 1; $p <= (int)$selectedDoc['total_pages']; $p++): ?>
                    <div class="col-6 col-sm-4 col-md-3">
                        <div class="split-page-thumb-card">
                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom bg-white">
                                <span class="badge text-white px-2 py-1" style="background: linear-gradient(135deg, #ea580c, #f43f5e); font-size: 0.7rem;">Trang #<?php echo $p; ?></span>
                                <button type="button" class="btn btn-xs btn-link text-danger p-0" title="Phóng to xem chi tiết" onclick="zoomSplitPage(<?php echo $selectedDoc['id']; ?>, <?php echo $p; ?>, <?php echo (int)$selectedDoc['total_pages']; ?>, '<?php echo htmlspecialchars(addslashes($selectedDoc['original_filename'])); ?>')">
                                    <i data-lucide="maximize-2" style="width: 13px; height: 13px;"></i>
                                </button>
                            </div>
                            <div class="split-thumb-box" onclick="zoomSplitPage(<?php echo $selectedDoc['id']; ?>, <?php echo $p; ?>, <?php echo (int)$selectedDoc['total_pages']; ?>, '<?php echo htmlspecialchars(addslashes($selectedDoc['original_filename'])); ?>')" title="Bấm để phóng to xem trang">
                                <canvas id="split_canvas_<?php echo $p; ?>"></canvas>
                                <span class="zoom-badge-hint position-absolute" style="opacity: 0; bottom: 6px; left: 50%; transform: translateX(-50%) translateY(4px); background: rgba(15,23,42,0.85); color: #fff; font-size: 0.68rem; padding: 2px 10px; border-radius: 12px; transition: all 0.2s ease; pointer-events: none; white-space: nowrap;">
                                    🔍 Phóng to
                                </span>
                            </div>
                            <div class="p-2 text-center bg-light border-top">
                                <button type="button" class="btn btn-xs btn-outline-danger w-100 py-1 rounded-2 fw-semibold" style="font-size: 0.72rem;" onclick="appendPageToActiveGroup(<?php echo $p; ?>)" title="Thêm trang này vào nhóm tách hiện tại">
                                    + Chọn trang này
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Bước 3: Cấu hình các nhóm trang tách ra -->
        <div class="card card-aurora p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span class="badge text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 26px; height: 26px; font-size: 0.8rem; background: linear-gradient(135deg, #ea580c, #f43f5e);">3</span>
                    <span>Cấu hình các nhóm trang tách ra</span>
                </h5>
                <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 rounded-pill px-3 shadow-2xs hover-lift" onclick="addGroup()">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                    <span>Thêm nhóm mới</span>
                </button>
            </div>

            <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                Nhập phạm vi trang cho từng tệp (ví dụ: <code>1-3</code> hoặc <code>1, 3, 5</code>). Hệ thống tự động kiểm tra tính hợp lệ trước khi cắt.
            </p>

            <div id="splitGroupsContainer">
                <!-- Group 1 mặc định -->
                <div class="split-group-card p-3 mb-3" id="group_0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-danger d-flex align-items-center gap-1">
                            <i data-lucide="file-check" style="width: 15px; height: 15px; color: #ea580c;"></i>
                            <span>Tệp đầu ra #1</span>
                        </span>
                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 rounded-pill" onclick="removeGroup(0)" style="display: none;" id="btnRemove_0">Xóa nhóm</button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Tên file kết quả</label>
                            <input type="text" class="form-control form-control-sm grp-filename rounded-2" value="<?php echo pathinfo($selectedDoc['original_filename'], PATHINFO_FILENAME); ?>_Phan1.pdf">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Phạm vi trang (VD: 1-<?php echo min(2, (int)$selectedDoc['total_pages']); ?>)</label>
                            <input type="text" class="form-control form-control-sm grp-range rounded-2" value="1-<?php echo min(2, (int)$selectedDoc['total_pages']); ?>" placeholder="1-2 hoặc 1, 3">
                        </div>
                    </div>
                </div>
            </div>

            <div id="splitResultContainer" style="display: none;" class="mt-3"></div>

            <div class="mt-3 border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Chi phí thực hiện:</span>
                    <span class="badge bg-warning text-dark border border-warning" style="font-size: 0.74rem;">
                        <i data-lucide="coins" style="width: 12px; height: 12px;" class="me-1"></i>5 tokens / phiên tách
                    </span>
                </div>
                <button type="button" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-pill shadow hover-lift" id="btnExecuteSplit" onclick="executeSplit()" style="background: linear-gradient(135deg, #ea580c 0%, #f43f5e 100%); border: none;">
                    <i data-lucide="split" style="width: 17px; height: 17px;"></i>
                    <span class="fw-bold">Bắt đầu tách thành các tệp PDF</span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const maxDocPages = <?php echo $selectedDoc ? (int)$selectedDoc['total_pages'] : 0; ?>;
    const docId = <?php echo $selectedDoc ? (int)$selectedDoc['id'] : 0; ?>;
    let groupCount = 1;

    function addGroup() {
        groupCount++;
        const idx = groupCount - 1;
        const container = document.getElementById('splitGroupsContainer');

        const card = document.createElement('div');
        card.className = 'split-group-card p-3 mb-3';
        card.id = `group_${idx}`;
        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold small text-danger d-flex align-items-center gap-1">
                    <i data-lucide="file-check" style="width: 15px; height: 15px; color: #ea580c;"></i>
                    <span>Tệp đầu ra #${idx + 1}</span>
                </span>
                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 rounded-pill" onclick="removeGroup(${idx})">Xóa nhóm</button>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Tên file kết quả</label>
                    <input type="text" class="form-control form-control-sm grp-filename rounded-2" value="Tach_Phan_${idx + 1}.pdf">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Phạm vi trang (VD: 1-${maxDocPages})</label>
                    <input type="text" class="form-control form-control-sm grp-range rounded-2" placeholder="VD: 3-${maxDocPages}">
                </div>
            </div>
        `;
        container.appendChild(card);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeGroup(idx) {
        const el = document.getElementById(`group_${idx}`);
        if (el) el.remove();
    }

    function zoomSplitPage(docId, pageNum, maxPages, title) {
        if (typeof window.openPageZoomModal === 'function') {
            window.openPageZoomModal({
                docId: docId,
                pageNum: pageNum,
                totalPages: maxPages,
                title: `${title} — Trang ${pageNum}`,
                rotation: 0,
                scale: 1.75,
                onPrev: function() {
                    if (pageNum > 1) zoomSplitPage(docId, pageNum - 1, maxPages, title);
                },
                onNext: function() {
                    if (pageNum < maxPages) zoomSplitPage(docId, pageNum + 1, maxPages, title);
                }
            });
        }
    }

    function appendPageToActiveGroup(pageNum) {
        const ranges = document.querySelectorAll('.grp-range');
        if (ranges.length === 0) return;
        const lastInput = ranges[ranges.length - 1];
        let val = lastInput.value.trim();
        if (!val) {
            lastInput.value = pageNum;
        } else {
            lastInput.value = val + ', ' + pageNum;
        }
        lastInput.focus();
    }

    async function renderSplitThumbnails() {
        if (!docId || maxDocPages <= 0) return;
        try {
            if (typeof pdfCache === 'undefined') window.pdfCache = {};
            if (!window.pdfCache[docId]) {
                const url = `/documents/view?id=${docId}`;
                window.pdfCache[docId] = await pdfjsLib.getDocument(url).promise;
            }
            const pdf = window.pdfCache[docId];

            for (let p = 1; p <= maxDocPages; p++) {
                const canvas = document.getElementById(`split_canvas_${p}`);
                if (!canvas) continue;
                const page = await pdf.getPage(p);
                const viewport = page.getViewport({ scale: 0.22 });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                const ctx = canvas.getContext('2d');
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            }
        } catch (e) {
            console.error('Lỗi nạp thumbnail tài liệu tách:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderSplitThumbnails();
    });
    window.addEventListener('load', () => {
        renderSplitThumbnails();
    });

    function parsePageRange(rangeStr, maxPages) {
        const pages = new Set();
        const parts = rangeStr.split(',');

        for (let part of parts) {
            part = part.trim();
            if (!part) continue;

            if (part.includes('-')) {
                const [startStr, endStr] = part.split('-');
                const start = parseInt(startStr);
                const end = parseInt(endStr);
                if (isNaN(start) || isNaN(end) || start < 1 || end > maxPages || start > end) {
                    return null;
                }
                for (let i = start; i <= end; i++) {
                    pages.add(i);
                }
            } else {
                const p = parseInt(part);
                if (isNaN(p) || p < 1 || p > maxPages) {
                    return null;
                }
                pages.add(p);
            }
        }

        return Array.from(pages).sort((a, b) => a - b);
    }

    async function executeSplit() {
        if (!window.IS_LOGGED_IN) {
            if (typeof window.showRequireLoginModal === 'function') {
                window.showRequireLoginModal(() => {
                    executeSplit();
                });
            } else {
                window.location.href = '/login';
            }
            return;
        }

        const groups = [];
        const groupCards = document.querySelectorAll('.split-group-card');

        for (let card of groupCards) {
            const filename = card.querySelector('.grp-filename').value.trim();
            const rangeStr = card.querySelector('.grp-range').value.trim();

            if (!filename) {
                alert('Vui lòng nhập tên cho tất cả các tệp đầu ra.');
                return;
            }

            const parsedPages = parsePageRange(rangeStr, maxDocPages);
            if (!parsedPages || parsedPages.length === 0) {
                alert(`Phạm vi trang "${rangeStr}" không hợp lệ hoặc vượt quá tổng số trang (${maxDocPages} trang).`);
                return;
            }

            groups.push({
                filename: filename,
                pages: parsedPages,
            });
        }

        const btn = document.getElementById('btnExecuteSplit');
        const resContainer = document.getElementById('splitResultContainer');

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý tách các tệp...`;
        resContainer.style.display = 'none';

        try {
            const res = await fetch('/api/split', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                },
                body: JSON.stringify({
                    document_id: docId,
                    groups: groups,
                })
            });

            const data = await res.json();

            if (res.status === 402 || data.out_of_tokens) {
                if (typeof window.showOutOfTokensModal === 'function') {
                    window.showOutOfTokensModal({
                        message: data.message || 'Bạn đã sử dụng hết Token! Vui lòng nâng cấp gói để tiếp tục tách tài liệu.'
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

                let html = `<div class="card border-success shadow-sm rounded-4 overflow-hidden mb-3"><div class="card-header bg-success text-white fw-bold small d-flex align-items-center gap-2 py-2 px-3"><i data-lucide="check-circle" style="width: 16px; height: 16px;"></i><span>Kết quả tách tệp PDF thành công:</span></div><ul class="list-group list-group-flush small">`;
                data.results.forEach(item => {
                    if (item.success) {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <span class="text-success fw-bold me-2">&check;</span>
                                    <strong class="text-dark">${item.filename}</strong>
                                    <span class="badge bg-light text-secondary border ms-2">${item.pages} trang</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 rounded-pill" onclick="reviewPdf('/exports/download?token=${item.download_token}&inline=1', '${item.filename}', '/exports/download?token=${item.download_token}')" title="Xem trước nội dung tệp này">
                                        <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                        <span>Xem trước</span>
                                    </button>
                                    <a href="/exports/download?token=${item.download_token}" class="btn btn-xs btn-success d-inline-flex align-items-center gap-1 rounded-pill" target="_blank" download="${item.filename}" title="Tải file về máy">
                                        <i data-lucide="download" style="width: 13px; height: 13px;"></i>
                                        <span>Tải xuống</span>
                                    </a>
                                </div>
                            </li>
                        `;
                    } else {
                        html += `
                            <li class="list-group-item text-danger py-2 px-3">
                                <span class="fw-bold me-2">&cross;</span>
                                <strong>${item.filename}:</strong> ${item.error}
                            </li>
                        `;
                    }
                });
                html += `</ul></div>`;
                resContainer.innerHTML = html;
                resContainer.style.display = 'block';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                resContainer.innerHTML = `<div class="alert alert-danger small mb-0 rounded-3">${data.message}</div>`;
                resContainer.style.display = 'block';
            }
        } catch (e) {
            resContainer.innerHTML = `<div class="alert alert-danger small mb-0 rounded-3">Lỗi kết nối máy chủ: ${e.message}</div>`;
            resContainer.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="split" style="width: 17px; height: 17px;"></i><span>Bắt đầu tách thành các tệp PDF</span>`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    async function refreshSplitDocuments() {
        try {
            const res = await fetch('/api/documents/list');
            const data = await res.json();
            if (!data.success || !Array.isArray(data.documents)) return;

            const select = document.getElementById('selectDocToSplit');
            if (select) {
                const currentVal = select.value;
                select.innerHTML = '';
                data.documents.forEach(doc => {
                    const opt = document.createElement('option');
                    opt.value = doc.id;
                    opt.textContent = `📄 ${doc.original_filename} — ${doc.total_pages} trang (${doc.formatted_size || ''})`;
                    if (String(doc.id) === String(currentVal)) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
            } else if (data.documents.length > 0) {
                window.location.reload();
            }
        } catch (e) {
            console.error('Error refreshing split docs:', e);
        }
    }

    window.addEventListener('auth:updated', () => {
        refreshSplitDocuments();
    });
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
