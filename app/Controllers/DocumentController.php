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

        if (auth_check()) {
            $userId = auth_id();
            $totalDocs = Document::countByUser($userId, $search);
            $totalPages = (int)ceil($totalDocs / $perPage);
            if ($totalPages > 0 && $page > $totalPages) {
                $page = $totalPages;
            }
            $documents = Document::getPaginatedByUser($userId, $page, $perPage, $search, $sort);
        } else {
            // Khách trải nghiệm: ban đầu trống hoàn toàn, chỉ hiển thị tệp tải lên trong phiên hiện tại
            $guestDocIds = $_SESSION['guest_doc_ids'] ?? [];
            $totalDocs = Document::countByIds($guestDocIds, $search);
            $totalPages = (int)ceil($totalDocs / $perPage);
            if ($totalPages > 0 && $page > $totalPages) {
                $page = $totalPages;
            }
            $documents = Document::getPaginatedByIds($guestDocIds, $page, $perPage, $search, $sort);
        }

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
        $userId = auth_check() ? auth_id() : 1;

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
                if (!auth_check() && !empty($res['id'])) {
                    if (!isset($_SESSION['guest_doc_ids']) || !is_array($_SESSION['guest_doc_ids'])) {
                        $_SESSION['guest_doc_ids'] = [];
                    }
                    $_SESSION['guest_doc_ids'][] = (int)$res['id'];
                }
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
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'ID tài liệu không hợp lệ.');
            redirect('/documents');
        }

        if (auth_check()) {
            $deleted = Document::delete($id, auth_id());
        } else {
            $guestDocIds = $_SESSION['guest_doc_ids'] ?? [];
            if (in_array($id, $guestDocIds, true)) {
                $deleted = Document::deleteByAdmin($id);
                $_SESSION['guest_doc_ids'] = array_values(array_diff($guestDocIds, [$id]));
            } else {
                $deleted = false;
            }
        }

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
        $id = (int)($_GET['id'] ?? 0);

        $user = auth_user();
        if ($user && ($user['role'] ?? '') === 'admin') {
            $doc = Document::findById($id);
        } elseif (auth_check()) {
            $doc = Document::findByIdAndUser($id, auth_id());
        } else {
            $guestDocIds = $_SESSION['guest_doc_ids'] ?? [];
            $doc = in_array($id, $guestDocIds, true) ? Document::findById($id) : null;
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
        $id = (int)($_GET['id'] ?? 0);

        $user = auth_user();
        if ($user && ($user['role'] ?? '') === 'admin') {
            $doc = Document::findById($id);
        } elseif (auth_check()) {
            $doc = Document::findByIdAndUser($id, auth_id());
        } else {
            $guestDocIds = $_SESSION['guest_doc_ids'] ?? [];
            $doc = in_array($id, $guestDocIds, true) ? Document::findById($id) : null;
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

    /**
     * API JSON trả về danh sách tài liệu hiện tại (cho realtime sync khi đăng nhập)
     */
    public function listJson(): void
    {
        if (auth_check()) {
            $docs = Document::findByUser(auth_id(), null, 'created_at_desc');
        } else {
            $guestDocIds = $_SESSION['guest_doc_ids'] ?? [];
            $docs = !empty($guestDocIds) ? Document::findByIds($guestDocIds, null, 'created_at_desc') : [];
        }

        $list = [];
        foreach ($docs as $d) {
            $list[] = [
                'id' => (int)$d['id'],
                'original_filename' => $d['original_filename'],
                'total_pages' => (int)$d['total_pages'],
                'file_size' => (int)$d['file_size'],
                'formatted_size' => format_bytes((int)$d['file_size']),
            ];
        }

        $this->json([
            'success' => true,
            'documents' => $list,
        ]);
    }
}
