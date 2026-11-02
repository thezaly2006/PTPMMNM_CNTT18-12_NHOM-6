<?php ob_start(); ?>

<style>
    .split-page-thumb-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        position: relative;
    }
    .split-page-thumb-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .split-thumb-box {
        height: 130px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
        overflow: hidden;
        position: relative;
    }
    .split-thumb-box canvas {
        max-height: 115px;
        max-width: 90%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.12);
        border-radius: 3px;
        transition: transform 0.2s ease;
    }
    .split-thumb-box:hover .zoom-badge-hint {
        opacity: 1 !important;
        transform: translateX(-50%) translateY(0) !important;
    }
</style>

<div class="split-container" style="max-width: 1050px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Tách tài liệu PDF</h4>
            <p class="text-muted small mb-0">Chia tách một tài liệu PDF thành nhiều tệp PDF độc lập theo từng khoảng trang hoặc nhóm trang tùy chọn.</p>
        </div>
    </div>

    <!-- Chọn tài liệu cần tách -->
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-dark mb-3">1. Chọn tài liệu cần tách</h6>
        <?php if (empty($userDocuments)): ?>
            <div class="text-center py-4 text-muted small">
                <p class="mb-2">Chưa có tài liệu nào để tách.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">Tải PDF lên ngay</button>
            </div>
        <?php else: ?>
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <select class="form-select" id="selectDocToSplit" onchange="window.location.href='/split?doc_id=' + this.value">
                        <?php foreach ($userDocuments as $doc): ?>
                            <option value="<?php echo $doc['id']; ?>" <?php echo ($selectedDoc && $selectedDoc['id'] === $doc['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doc['original_filename']); ?> &mdash; <?php echo (int)$doc['total_pages']; ?> trang (<?php echo format_bytes((int)$doc['file_size']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-light text-dark border p-2 w-100 text-center">
                        Tổng số trang khả dụng: <strong id="docTotalPages"><?php echo $selectedDoc ? (int)$selectedDoc['total_pages'] : 0; ?></strong> trang
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($selectedDoc): ?>
        <!-- Xem trước danh sách trang của tài liệu nguồn (Bấm để phóng to cực nét như Ảnh 2) -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold text-dark mb-0">2. Xem trước nội dung các trang tài liệu</h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">
                        <?php echo (int)$selectedDoc['total_pages']; ?> trang
                    </span>
                </div>
                <div class="small text-muted">
                    <i data-lucide="info" style="width: 14px; height: 14px;" class="me-1"></i>
                    Bấm vào trang bất kỳ để <strong>phóng to xem chi tiết (Ảnh 2)</strong>
                </div>
            </div>

            <p class="text-muted small mb-3">Kiểm tra nội dung trang để nhập chính xác khoảng trang cần chia tách:</p>

            <div class="row g-3" id="splitPagesThumbGrid" style="max-height: 380px; overflow-y: auto;">
                <?php for ($p = 1; $p <= (int)$selectedDoc['total_pages']; $p++): ?>
                    <div class="col-6 col-sm-4 col-md-3">
                        <div class="split-page-thumb-card">
                            <div class="d-flex justify-content-between align-items-center p-1 px-2 border-bottom bg-white">
                                <span class="badge bg-primary px-2" style="font-size: 0.7rem;">Trang #<?php echo $p; ?></span>
                                <button type="button" class="btn btn-xs btn-link text-primary p-0" title="Phóng to xem chi tiết (Ảnh 2)" onclick="zoomSplitPage(<?php echo $selectedDoc['id']; ?>, <?php echo $p; ?>, <?php echo (int)$selectedDoc['total_pages']; ?>, '<?php echo htmlspecialchars(addslashes($selectedDoc['original_filename'])); ?>')">
                                    <i data-lucide="maximize-2" style="width: 12px; height: 12px;"></i>
                                </button>
                            </div>
                            <div class="split-thumb-box" onclick="zoomSplitPage(<?php echo $selectedDoc['id']; ?>, <?php echo $p; ?>, <?php echo (int)$selectedDoc['total_pages']; ?>, '<?php echo htmlspecialchars(addslashes($selectedDoc['original_filename'])); ?>')" title="Bấm để phóng to trang cực nét (Ảnh 2)">
                                <canvas id="split_canvas_<?php echo $p; ?>"></canvas>
                                <span class="zoom-badge-hint position-absolute" style="opacity: 0; bottom: 4px; left: 50%; transform: translateX(-50%) translateY(3px); background: rgba(15,23,42,0.85); color: #fff; font-size: 0.65rem; padding: 2px 8px; border-radius: 12px; transition: all 0.2s ease; pointer-events: none; white-space: nowrap;">
                                    🔍 Phóng to
                                </span>
                            </div>
                            <div class="p-1 text-center bg-light border-top">
                                <button type="button" class="btn btn-xs btn-outline-secondary w-100 py-0" style="font-size: 0.68rem;" onclick="appendPageToActiveGroup(<?php echo $p; ?>)" title="Thêm trang này vào nhóm tách hiện tại">
                                    + Chọn trang này
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Thiết lập các nhóm tách trang -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">3. Cấu hình các nhóm trang tách ra</h6>
                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" onclick="addGroup()">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                    <span>Thêm nhóm mới</span>
                </button>
            </div>

            <p class="text-muted small mb-3">
                Nhập phạm vi trang cho từng tệp (ví dụ: <code>1-3</code> hoặc <code>1, 3, 5</code>). Hệ thống sẽ tự động xác thực và ngăn các trang vượt quá tổng số trang.
            </p>

            <div id="splitGroupsContainer">
                <!-- Group 1 mặc định -->
                <div class="split-group-card p-3 border rounded-3 mb-3 bg-light" id="group_0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-primary">Tệp đầu ra #1</span>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeGroup(0)" style="display: none;" id="btnRemove_0">Xóa nhóm</button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Tên file kết quả</label>
                            <input type="text" class="form-control form-control-sm grp-filename" value="<?php echo pathinfo($selectedDoc['original_filename'], PATHINFO_FILENAME); ?>_Phan1.pdf">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Phạm vi trang (VD: 1-<?php echo min(2, (int)$selectedDoc['total_pages']); ?>)</label>
                            <input type="text" class="form-control form-control-sm grp-range" value="1-<?php echo min(2, (int)$selectedDoc['total_pages']); ?>" placeholder="1-2 hoặc 1, 3">
                        </div>
                    </div>
                </div>
            </div>

            <div id="splitResultContainer" style="display: none;" class="mt-3"></div>

            <div class="mt-3 border-top pt-3">
                <button type="button" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" id="btnExecuteSplit" onclick="executeSplit()">
                    <i data-lucide="split" style="width: 16px; height: 16px;"></i>
                    <span>Bắt đầu tách thành các tệp PDF</span>
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
        card.className = 'split-group-card p-3 border rounded-3 mb-3 bg-light';
        card.id = `group_${idx}`;
        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold small text-primary">Tệp đầu ra #${idx + 1}</span>
                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeGroup(${idx})">Xóa nhóm</button>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Tên file kết quả</label>
                    <input type="text" class="form-control form-control-sm grp-filename" value="Tach_Phan_${idx + 1}.pdf">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Phạm vi trang (VD: 1-${maxDocPages})</label>
                    <input type="text" class="form-control form-control-sm grp-range" placeholder="VD: 3-${maxDocPages}">
                </div>
            </div>
        `;
        container.appendChild(card);
    }

    function removeGroup(idx) {
        const el = document.getElementById(`group_${idx}`);
        if (el) el.remove();
    }

    // Phóng to trang PDF cực nét (chuẩn Ảnh 2)
    function zoomSplitPage(docId, pageNum, maxPages, title) {
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

    // Chọn nhanh trang vào ô dải trang của nhóm cuối cùng hoặc nhóm 0
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

    // Vẽ thumbnail cho các trang của tài liệu đang chọn
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

    // Tự động render khi tải xong trang
    document.addEventListener('DOMContentLoaded', () => {
        renderSplitThumbnails();
    });
    window.addEventListener('load', () => {
        renderSplitThumbnails();
    });

    // Chuyển đổi chuỗi range như "1-3, 5, 7-9" thành mảng số trang [1, 2, 3, 5, 7, 8, 9]
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
                    return null; // Không hợp lệ
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
            if (data.success) {
                let html = `<div class="card border-success"><div class="card-header bg-success-subtle text-success fw-bold small">Kết quả tách tệp PDF:</div><ul class="list-group list-group-flush small">`;
                data.results.forEach(item => {
                    if (item.success) {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <span class="text-success fw-bold me-2">&check;</span>
                                    <strong class="text-dark">${item.filename}</strong>
                                    <span class="badge bg-light text-secondary border ms-2">${item.pages} trang</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1" onclick="reviewPdf('/exports/download?token=${item.download_token}&inline=1', '${item.filename}', '/exports/download?token=${item.download_token}')" title="Xem trước nội dung tệp này">
                                        <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                        <span>Xem trước</span>
                                    </button>
                                    <a href="/exports/download?token=${item.download_token}" class="btn btn-xs btn-success d-inline-flex align-items-center gap-1" target="_blank" download="${item.filename}" title="Tải file về máy">
                                        <i data-lucide="download" style="width: 13px; height: 13px;"></i>
                                        <span>Tải xuống</span>
                                    </a>
                                </div>
                            </li>
                        `;
                    } else {
                        html += `
                            <li class="list-group-item text-danger">
                                <span class="fw-bold me-2">&cross;</span>
                                <strong>${item.filename}:</strong> ${item.error}
                            </li>
                        `;
                    }
                });
                html += `</ul></div>`;
                resContainer.innerHTML = html;
                resContainer.style.display = 'block';
                lucide.createIcons();
            } else {
                resContainer.innerHTML = `<div class="alert alert-danger small mb-0">${data.message}</div>`;
                resContainer.style.display = 'block';
            }
        } catch (e) {
            resContainer.innerHTML = `<div class="alert alert-danger small mb-0">Lỗi kết nối máy chủ: ${e.message}</div>`;
            resContainer.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="split" style="width: 16px; height: 16px;"></i><span>Bắt đầu tách thành các tệp PDF</span>`;
            lucide.createIcons();
        }
    }
</script>

<?php 
$content = ob_get_clean();
require ROOT_PATH . '/resources/views/layouts/app.php';
?>
