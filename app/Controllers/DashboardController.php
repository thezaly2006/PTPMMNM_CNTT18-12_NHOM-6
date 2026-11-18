<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Document;
use App\Models\EditPlan;
use App\Models\ExportJob;

class DashboardController extends BaseController
{
    public function index(): void
    {
        if (auth_check()) {
            $userId = auth_id();
            $stats = [
                'total_documents' => Document::countByUser($userId),
                'total_plans' => EditPlan::countByUser($userId),
                'total_exports' => ExportJob::countCompletedByUser($userId),
            ];
            $recentDocuments = Document::getRecentByUser($userId, 6);
            $recentExports = ExportJob::getRecentByUser($userId, 5);
        } else {
            // Khi chưa đăng nhập: giao diện như người dùng mới tạo tài khoản (sạch sẽ, 0 số liệu)
            // Nếu khách có tải tệp lên trong phiên thì chỉ hiển thị tệp phiên khách đó
            $guestDocIds = $_SESSION['guest_doc_ids'] ?? [];
            $guestDocs = !empty($guestDocIds) ? Document::findByIds($guestDocIds, null, 'created_at_desc') : [];

            $stats = [
                'total_documents' => count($guestDocs),
                'total_plans' => 0,
                'total_exports' => 0,
            ];
            $recentDocuments = array_slice($guestDocs, 0, 6);
            $recentExports = [];
        }

        $this->render('dashboard.index', [
            'pageTitle' => 'Tổng quan - PDF Workspace',
            'activeNav' => 'dashboard',
            'stats' => $stats,
            'recentDocuments' => $recentDocuments,
            'recentExports' => $recentExports,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }
}
