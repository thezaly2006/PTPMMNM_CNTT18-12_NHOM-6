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
        $userId = auth_id();

        $stats = [
            'total_documents' => Document::countByUser($userId),
            'total_plans' => EditPlan::countByUser($userId),
            'total_exports' => ExportJob::countCompletedByUser($userId),
        ];

        $recentDocuments = Document::getRecentByUser($userId, 6);
        $recentExports = ExportJob::getRecentByUser($userId, 5);

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
