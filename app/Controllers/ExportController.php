<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Document;
use App\Models\EditPlan;
use App\Models\ExportJob;
use App\Models\Output;
use App\Services\PdfEngineService;

class ExportController extends BaseController
{
    private PdfEngineService $engine;

    public function __construct()
    {
        $this->engine = new PdfEngineService();
    }

    /**
     * API Xuất PDF từ Kế hoạch chỉnh sửa (Editor hoặc Merge)
     */
    public function exportFromPlan(): void
    {
        $userId = auth_id();

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        $planId = !empty($data['plan_id']) ? (int)$data['plan_id'] : null;
        $outputFilename = trim($data['output_filename'] ?? '') ?: 'PDF_Workspace_Export.pdf';
        if (!str_ends_with(strtolower($outputFilename), '.pdf')) {
            $outputFilename .= '.pdf';
        }
        $jobType = in_array($data['job_type'] ?? '', ['merge', 'split', 'editor'], true) ? $data['job_type'] : 'editor';

        // 1. Kiểm tra kế hoạch và lấy danh sách các trang
        $pages = [];
        if ($planId) {
            $allPages = EditPlan::getPlanPages($planId, $userId);
            foreach ($allPages as $p) {
                if (!empty($p['is_included'])) {
                    $pages[] = $p;
                }
            }
        } elseif (!empty($data['pages']) && is_array($data['pages'])) {
            $pages = $data['pages'];
        }

        if (empty($pages)) {
            $this->json(['success' => false, 'message' => 'Không có trang nào được chọn để xuất PDF.'], 400);
            return;
        }

        // 2. Tạo công việc xuất (Export Job)
        $jobId = ExportJob::createJob($userId, $planId, $jobType);

        try {
            // 3. TẠO BẢN CHỤP BẤT BIẾN (Immutable Snapshot)
            ExportJob::saveSnapshotPages($jobId, $pages);

            // 4. Bổ sung thông tin đường dẫn file nguồn và kiểm tra quyền
            $pagesToProcess = [];
            foreach ($pages as $p) {
                $docId = (int)$p['document_id'];
                $doc = Document::findByIdAndUser($docId, $userId);
                if (!$doc || !file_exists($doc['file_path'])) {
                    throw new \RuntimeException("Không thể truy cập tệp nguồn ID: {$docId}.");
                }

                $pagesToProcess[] = [
                    'document_id' => $docId,
                    'original_filename' => $doc['original_filename'],
                    'file_path' => $doc['file_path'],
                    'source_page_number' => (int)$p['source_page_number'],
                    'rotation_degrees' => (int)($p['rotation_degrees'] ?? 0),
                    'fit_portrait' => !empty($p['fit_portrait']) ? 1 : 0,
                ];
            }

            // 5. Thực thi qua PdfEngineService (Tạo file tạm -> Kiểm tra -> Di chuyển storage/outputs)
            $result = $this->engine->processExport($pagesToProcess, $outputFilename);

            // 6. Lưu vào bảng outputs và tạo Download Token an toàn
            $downloadToken = Output::createOutput(
                $jobId,
                $userId,
                $outputFilename,
                $result['stored_filename'],
                $result['file_path'],
                $result['file_size'],
                $result['total_pages']
            );

            // 7. Đánh dấu hoàn thành
            ExportJob::markCompleted($jobId);

            $this->json([
                'success' => true,
                'job_id' => $jobId,
                'download_token' => $downloadToken,
                'message' => 'Tài liệu PDF đã được xuất thành công!',
            ]);
        } catch (\Throwable $e) {
            // Ghi nhận lỗi vào export_jobs
            ExportJob::markFailed($jobId, $e->getMessage());

            $this->json([
                'success' => false,
                'job_id' => $jobId,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Màn hình 5: Ghép PDF
     */
    public function showMerge(): void
    {
        $userId = auth_id();
        $userDocuments = Document::findByUser($userId, null, 'created_at_desc');

        $this->render('merge.index', [
            'pageTitle' => 'Ghép PDF - PDF Workspace',
            'activeNav' => 'merge',
            'userDocuments' => $userDocuments,
        ]);
    }

    /**
     * Màn hình 6: Tách PDF
     */
    public function showSplit(): void
    {
        $userId = auth_id();
        $docId = !empty($_GET['doc_id']) ? (int)$_GET['doc_id'] : null;
        $userDocuments = Document::findByUser($userId, null, 'created_at_desc');

        $selectedDoc = null;
        if ($docId) {
            $selectedDoc = Document::findByIdAndUser($docId, $userId);
        } elseif (!empty($userDocuments)) {
            $selectedDoc = $userDocuments[0];
        }

        $this->render('split.index', [
            'pageTitle' => 'Tách PDF - PDF Workspace',
            'activeNav' => 'split',
            'userDocuments' => $userDocuments,
            'selectedDoc' => $selectedDoc,
        ]);
    }

    /**
     * API Thực thi Tách PDF thành nhiều nhóm độc lập
     */
    public function executeSplit(): void
    {
        $userId = auth_id();
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        $docId = (int)($data['document_id'] ?? 0);
        $groups = $data['groups'] ?? [];

        $doc = Document::findByIdAndUser($docId, $userId);
        if (!$doc) {
            $this->json(['success' => false, 'message' => 'Tài liệu không tồn tại.'], 404);
            return;
        }

        if (empty($groups)) {
            $this->json(['success' => false, 'message' => 'Vui lòng cấu hình ít nhất 1 nhóm trang để tách.'], 400);
            return;
        }

        $results = [];
        $totalPages = (int)$doc['total_pages'];

        foreach ($groups as $idx => $grp) {
            $grpName = trim($grp['filename'] ?? '') ?: 'Tach_Nhom_' . ($idx + 1) . '.pdf';
            if (!str_ends_with(strtolower($grpName), '.pdf')) $grpName .= '.pdf';

            $pageNumbers = $grp['pages'] ?? [];
            if (empty($pageNumbers)) {
                $results[] = [
                    'group_index' => $idx + 1,
                    'filename' => $grpName,
                    'success' => false,
                    'error' => 'Nhóm không có trang nào.',
                ];
                continue;
            }

            // Kiểm tra số trang hợp lệ, ngăn lựa chọn trang không tồn tại
            $valid = true;
            $pagesToProcess = [];
            $snapshotPages = [];

            foreach ($pageNumbers as $pNum) {
                $pNum = (int)$pNum;
                if ($pNum < 1 || $pNum > $totalPages) {
                    $valid = false;
                    break;
                }
                $pagesToProcess[] = [
                    'document_id' => $doc['id'],
                    'original_filename' => $doc['original_filename'],
                    'file_path' => $doc['file_path'],
                    'source_page_number' => $pNum,
                    'rotation_degrees' => 0,
                ];
                $snapshotPages[] = [
                    'document_id' => $doc['id'],
                    'original_filename' => $doc['original_filename'],
                    'source_page_number' => $pNum,
                    'rotation_degrees' => 0,
                ];
            }

            if (!$valid) {
                $results[] = [
                    'group_index' => $idx + 1,
                    'filename' => $grpName,
                    'success' => false,
                    'error' => 'Chứa số trang không tồn tại trong tài liệu gốc.',
                ];
                continue;
            }

            // Tạo Export Job riêng cho nhóm này
            $jobId = ExportJob::createJob($userId, null, 'split');

            try {
                ExportJob::saveSnapshotPages($jobId, $snapshotPages);
                $res = $this->engine->processExport($pagesToProcess, $grpName);

                $token = Output::createOutput(
                    $jobId,
                    $userId,
                    $grpName,
                    $res['stored_filename'],
                    $res['file_path'],
                    $res['file_size'],
                    $res['total_pages']
                );
                ExportJob::markCompleted($jobId);

                $results[] = [
                    'group_index' => $idx + 1,
                    'filename' => $grpName,
                    'success' => true,
                    'download_token' => $token,
                    'pages' => $res['total_pages'],
                ];
            } catch (\Throwable $e) {
                ExportJob::markFailed($jobId, $e->getMessage());
                $results[] = [
                    'group_index' => $idx + 1,
                    'filename' => $grpName,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $this->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    /**
     * Màn hình 7: Lịch sử xuất file (Hỗ trợ tìm kiếm, lọc & phân trang)
     */
    public function showExports(): void
    {
        $userId = auth_id();
        $search = trim($_GET['q'] ?? '');
        $type = trim($_GET['type'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sort = trim($_GET['sort'] ?? 'created_at_desc');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        if (!empty($_GET['per_page'])) {
            $pp = (int)$_GET['per_page'];
            if (in_array($pp, [10, 20, 50], true)) {
                $perPage = $pp;
            }
        }

        $totalJobs = ExportJob::countAllByUser($userId, $search, $type, $status);
        $totalPages = (int)ceil($totalJobs / $perPage);
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $exports = ExportJob::getPaginatedByUser($userId, $page, $perPage, $search, $type, $status, $sort);

        $this->render('exports.index', [
            'pageTitle' => 'Lịch sử xuất PDF - PDF Workspace',
            'activeNav' => 'exports',
            'exports' => $exports,
            'search' => $search,
            'type' => $type,
            'status' => $status,
            'sort' => $sort,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalJobs,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Màn hình 8: Chi tiết bản xuất (Bảng đối chiếu trang nguồn - Hỗ trợ phân trang)
     */
    public function showExportDetail(): void
    {
        $userId = auth_id();
        $jobId = (int)($_GET['id'] ?? 0);

        $job = ExportJob::getJobDetails($jobId, $userId);
        if (!$job) {
            flash('error', 'Không tìm thấy thông tin bản xuất hoặc bạn không có quyền truy cập.');
            redirect('/exports');
        }

        $allSnapshotPages = ExportJob::getJobPagesSnapshot($jobId);
        $totalSnapshotPages = count($allSnapshotPages);

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $totalPages = (int)ceil($totalSnapshotPages / $perPage);
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $snapshotPages = array_slice($allSnapshotPages, ($page - 1) * $perPage, $perPage);

        $this->render('exports.detail', [
            'pageTitle' => 'Chi tiết bản xuất #' . $job['id'] . ' - PDF Workspace',
            'activeNav' => 'exports',
            'job' => $job,
            'snapshotPages' => $snapshotPages,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalSnapshotPages,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Tải xuống tệp PDF an toàn qua Token
     */
    public function downloadOutput(): void
    {
        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            http_response_code(400);
            die("Mã xác thực tải xuống không hợp lệ.");
        }

        $output = Output::findByToken($token);
        if (!$output || !file_exists($output['file_path'])) {
            http_response_code(404);
            die("Không tìm thấy tệp hoặc tệp chưa được tạo thành công.");
        }

        // Tải xuống file hoàn chỉnh hoặc xem inline khi review
        $isInline = isset($_GET['inline']) && $_GET['inline'] === '1';
        $disposition = $isInline ? 'inline' : 'attachment';

        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($output['original_filename']) . '"');
        header('Content-Length: ' . filesize($output['file_path']));
        header('Cache-Control: private, max-age=3600');
        header('Pragma: public');
        readfile($output['file_path']);
        exit;
    }
}
