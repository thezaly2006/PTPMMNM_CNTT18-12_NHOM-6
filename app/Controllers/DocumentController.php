<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Document;
use App\Services\DocumentService;

class DocumentController extends BaseController
{
    private DocumentService $documentService;

    public function __construct()
    {
        $this->documentService = new DocumentService();
    }

    public function index(): void
    {
        $userId = auth_id();
        $search = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? 'created_at_desc');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        if (!empty($_GET['per_page'])) {
            $pp = (int)$_GET['per_page'];
            if (in_array($pp, [10, 20, 50], true)) {
                $perPage = $pp;
            }
        }

        $totalDocs = Document::countByUser($userId, $search);
        $totalPages = (int)ceil($totalDocs / $perPage);
        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $documents = Document::getPaginatedByUser($userId, $page, $perPage, $search, $sort);

        $this->render('documents.index', [
            'pageTitle' => 'Tài liệu của tôi - PDF Workspace',
            'activeNav' => 'documents',
            'documents' => $documents,
            'search' => $search,
            'sort' => $sort,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalDocs,
            'totalPages' => $totalPages,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function upload(): void
    {
        $userId = auth_id();

        if (empty($_FILES['files'])) {
            $this->json(['success' => false, 'message' => 'Không có tệp nào được chọn.'], 400);
            return;
        }

        $files = $_FILES['files'];
        $results = [];
        $successCount = 0;
        $failCount = 0;

        // Xử lý upload mảng tệp
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $fileCount; $i++) {
            $fileData = [
                'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                'type' => is_array($files['type']) ? $files['type'][$i] : $files['type'],
                'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                'size' => is_array($files['size']) ? $files['size'][$i] : $files['size'],
            ];

            $res = $this->documentService->handleUpload($userId, $fileData);
            if ($res['success']) {
                $successCount++;
            } else {
                $failCount++;
            }
            $results[] = $res;
        }

        $this->json([
            'success' => $failCount === 0,
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'results' => $results,
        ]);
    }

    public function delete(): void
    {
        $userId = auth_id();
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'ID tài liệu không hợp lệ.');
            redirect('/documents');
        }

        $deleted = Document::delete($id, $userId);

        if ($deleted) {
            flash('success', 'Đã xóa tài liệu thành công.');
        } else {
            flash('error', 'Không thể xóa tài liệu (tài liệu không tồn tại hoặc bạn không có quyền).');
        }

        redirect('/documents');
    }

    /**
     * Trả về PDF xem trực tiếp trên trình duyệt / PDF.js thumbnail
     */
    public function viewInline(): void
    {
        $userId = auth_id();
        $id = (int)($_GET['id'] ?? 0);

        $user = auth_user();
        if ($user && ($user['role'] ?? '') === 'admin') {
            $doc = Document::findById($id);
        } else {
            $doc = Document::findByIdAndUser($id, $userId);
        }

        if (!$doc || !file_exists($doc['file_path'])) {
            http_response_code(404);
            die('Không tìm thấy tài liệu hoặc bạn không có quyền truy cập.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . rawurlencode($doc['original_filename']) . '"');
        header('Content-Length: ' . filesize($doc['file_path']));
        header('Cache-Control: private, max-age=3600');
        readfile($doc['file_path']);
        exit;
    }

    public function download(): void
    {
        $userId = auth_id();
        $id = (int)($_GET['id'] ?? 0);

        $user = auth_user();
        if ($user && ($user['role'] ?? '') === 'admin') {
            $doc = Document::findById($id);
        } else {
            $doc = Document::findByIdAndUser($id, $userId);
        }

        if (!$doc || !file_exists($doc['file_path'])) {
            http_response_code(404);
            die('Không tìm thấy tài liệu hoặc bạn không có quyền truy cập.');
        }

        $isInline = isset($_GET['inline']) && $_GET['inline'] === '1';
        $disposition = $isInline ? 'inline' : 'attachment';

        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($doc['original_filename']) . '"');
        header('Content-Length: ' . filesize($doc['file_path']));
        readfile($doc['file_path']);
        exit;
    }
}
