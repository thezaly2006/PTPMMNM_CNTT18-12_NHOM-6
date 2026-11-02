<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Document;
use App\Models\EditPlan;

class EditorController extends BaseController
{
    public function index(): void
    {
        $userId = auth_id();
        $planId = !empty($_GET['plan_id']) ? (int)$_GET['plan_id'] : null;
        $docId = !empty($_GET['doc_id']) ? (int)$_GET['doc_id'] : null;

        $userDocuments = Document::findByUser($userId, null, 'created_at_desc');

        $currentPlan = null;
        $initialPages = [];

        if ($planId) {
            $currentPlan = EditPlan::findByIdAndUser($planId, $userId);
            if ($currentPlan) {
                $initialPages = EditPlan::getPlanPages($planId, $userId);
            }
        } elseif ($docId) {
            // Khởi tạo phiên mới từ 1 tài liệu có sẵn
            $doc = Document::findByIdAndUser($docId, $userId);
            if ($doc) {
                $currentPlan = [
                    'id' => null,
                    'title' => 'Chỉnh sửa: ' . pathinfo($doc['original_filename'], PATHINFO_FILENAME),
                ];
                for ($p = 1; $p <= $doc['total_pages']; $p++) {
                    $initialPages[] = [
                        'document_id' => $doc['id'],
                        'original_filename' => $doc['original_filename'],
                        'source_page_number' => $p,
                        'output_order' => $p,
                        'rotation_degrees' => 0,
                        'is_included' => 1,
                    ];
                }
            }
        }

        $this->render('editor.index', [
            'pageTitle' => 'PDF Editor - PDF Workspace',
            'activeNav' => 'editor',
            'userDocuments' => $userDocuments,
            'currentPlan' => $currentPlan,
            'initialPages' => $initialPages,
        ]);
    }

    /**
     * API lưu kế hoạch chỉnh sửa và danh sách trang
     */
    public function savePlan(): void
    {
        $userId = auth_id();

        // Đọc dữ liệu JSON gửi lên
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        if (!$data || !isset($data['pages']) || !is_array($data['pages'])) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
            return;
        }

        $title = trim($data['title'] ?? '') ?: 'Phiên chỉnh sửa ' . date('d/m/Y H:i');
        $planId = !empty($data['plan_id']) ? (int)$data['plan_id'] : null;
        $pages = $data['pages'];

        try {
            if (!$planId) {
                $planId = EditPlan::createPlan($userId, $title);
            } else {
                EditPlan::updatePlanTitle($planId, $userId, $title);
            }

            EditPlan::savePages($planId, $userId, $pages);

            $this->json([
                'success' => true,
                'plan_id' => $planId,
                'title' => $title,
                'message' => 'Đã lưu kế hoạch chỉnh sửa thành công!',
            ]);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => 'Lỗi khi lưu: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API lấy thông tin trang của một tài liệu để thêm vào editor
     */
    public function getDocPages(): void
    {
        $userId = auth_id();
        $docId = (int)($_GET['doc_id'] ?? 0);

        $doc = Document::findByIdAndUser($docId, $userId);
        if (!$doc) {
            $this->json(['success' => false, 'message' => 'Tài liệu không tồn tại.'], 404);
            return;
        }

        $pages = [];
        for ($i = 1; $i <= $doc['total_pages']; $i++) {
            $pages[] = [
                'document_id' => $doc['id'],
                'original_filename' => $doc['original_filename'],
                'source_page_number' => $i,
                'rotation_degrees' => 0,
                'is_included' => 1,
            ];
        }

        $this->json([
            'success' => true,
            'document' => [
                'id' => $doc['id'],
                'original_filename' => $doc['original_filename'],
                'total_pages' => $doc['total_pages'],
            ],
            'pages' => $pages,
        ]);
    }
}
