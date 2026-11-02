<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class ExportJob
{
    public static function createJob(int $userId, ?int $planId, string $jobType): int
    {
        Database::query(
            "INSERT INTO export_jobs (user_id, plan_id, job_type, status, created_at)
             VALUES (:user_id, :plan_id, :job_type, 'pending', NOW())",
            [
                'user_id' => $userId,
                'plan_id' => $planId,
                'job_type' => $jobType,
            ]
        );
        return Database::lastInsertId();
    }

    /**
     * Tạo Bản Chụp Bất Biến (Immutable Snapshot) cho lần xuất
     */
    public static function saveSnapshotPages(int $jobId, array $pages): void
    {
        $pdo = Database::getConnection();
        $sql = "INSERT INTO export_job_pages (export_job_id, document_id, source_page_number, output_order, rotation_degrees, fit_portrait, source_filename_snapshot, created_at)
                VALUES (:export_job_id, :document_id, :source_page_number, :output_order, :rotation_degrees, :fit_portrait, :source_filename_snapshot, NOW())";
        $stmt = $pdo->prepare($sql);

        $order = 1;
        foreach ($pages as $p) {
            $stmt->execute([
                'export_job_id' => $jobId,
                'document_id' => (int)$p['document_id'],
                'source_page_number' => (int)$p['source_page_number'],
                'output_order' => $order++,
                'rotation_degrees' => (int)($p['rotation_degrees'] ?? 0),
                'fit_portrait' => !empty($p['fit_portrait']) ? 1 : 0,
                'source_filename_snapshot' => $p['original_filename'] ?? 'Tệp nguồn',
            ]);
        }
    }

    public static function markCompleted(int $jobId): void
    {
        Database::query(
            "UPDATE export_jobs SET status = 'completed', completed_at = NOW(), error_message = NULL WHERE id = :id",
            ['id' => $jobId]
        );
    }

    public static function markFailed(int $jobId, string $errorMessage): void
    {
        Database::query(
            "UPDATE export_jobs SET status = 'failed', completed_at = NOW(), error_message = :error WHERE id = :id",
            [
                'id' => $jobId,
                'error' => $errorMessage,
            ]
        );
    }

    public static function countAllByUser(
        int $userId,
        ?string $search = null,
        ?string $jobType = null,
        ?string $status = null
    ): int {
        $sql = "SELECT COUNT(DISTINCT j.id) AS total
                FROM export_jobs j
                LEFT JOIN outputs o ON o.export_job_id = j.id
                WHERE j.user_id = :user_id";
        $params = ['user_id' => $userId];

        if (!empty($search)) {
            $cleanId = ltrim(trim($search), '#');
            if (is_numeric($cleanId)) {
                $sql .= " AND (j.id = :search_id OR o.original_filename LIKE :search_name OR o.stored_filename LIKE :search_stored)";
                $params['search_id'] = (int)$cleanId;
                $params['search_name'] = '%' . $search . '%';
                $params['search_stored'] = '%' . $search . '%';
            } else {
                $sql .= " AND (o.original_filename LIKE :s_orig OR o.stored_filename LIKE :s_stored OR j.job_type LIKE :s_type)";
                $searchWildcard = '%' . $search . '%';
                $params['s_orig'] = $searchWildcard;
                $params['s_stored'] = $searchWildcard;
                $params['s_type'] = $searchWildcard;
            }
        }

        if (!empty($jobType) && in_array($jobType, ['merge', 'split', 'editor'], true)) {
            $sql .= " AND j.job_type = :job_type";
            $params['job_type'] = $jobType;
        }

        if (!empty($status) && in_array($status, ['completed', 'failed', 'pending'], true)) {
            $sql .= " AND j.status = :status";
            $params['status'] = $status;
        }

        $res = Database::fetchOne($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    public static function getPaginatedByUser(
        int $userId,
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $jobType = null,
        ?string $status = null,
        string $sort = 'created_at_desc'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT j.*, o.download_token, o.stored_filename, o.file_size, o.original_filename AS output_name, o.total_pages AS output_pages
                FROM export_jobs j
                LEFT JOIN outputs o ON o.export_job_id = j.id
                WHERE j.user_id = :user_id";
        $params = ['user_id' => $userId];

        if (!empty($search)) {
            $cleanId = ltrim(trim($search), '#');
            if (is_numeric($cleanId)) {
                $sql .= " AND (j.id = :search_id OR o.original_filename LIKE :search_name OR o.stored_filename LIKE :search_stored)";
                $params['search_id'] = (int)$cleanId;
                $params['search_name'] = '%' . $search . '%';
                $params['search_stored'] = '%' . $search . '%';
            } else {
                $sql .= " AND (o.original_filename LIKE :s_orig OR o.stored_filename LIKE :s_stored OR j.job_type LIKE :s_type)";
                $searchWildcard = '%' . $search . '%';
                $params['s_orig'] = $searchWildcard;
                $params['s_stored'] = $searchWildcard;
                $params['s_type'] = $searchWildcard;
            }
        }

        if (!empty($jobType) && in_array($jobType, ['merge', 'split', 'editor'], true)) {
            $sql .= " AND j.job_type = :job_type";
            $params['job_type'] = $jobType;
        }

        if (!empty($status) && in_array($status, ['completed', 'failed', 'pending'], true)) {
            $sql .= " AND j.status = :status";
            $params['status'] = $status;
        }

        switch ($sort) {
            case 'created_at_asc':
                $sql .= " ORDER BY j.created_at ASC";
                break;
            case 'size_desc':
                $sql .= " ORDER BY o.file_size DESC";
                break;
            case 'pages_desc':
                $sql .= " ORDER BY o.total_pages DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY o.original_filename ASC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY j.created_at DESC";
                break;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return Database::fetchAll($sql, $params);
    }

    public static function getAllByUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT j.*, o.download_token, o.stored_filename, o.file_size, o.original_filename AS output_name, o.total_pages AS output_pages
             FROM export_jobs j
             LEFT JOIN outputs o ON o.export_job_id = j.id
             WHERE j.user_id = :user_id
             ORDER BY j.created_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function getJobDetails(int $jobId, int $userId): ?array
    {
        return Database::fetchOne(
            "SELECT j.*, o.download_token, o.stored_filename, o.file_size, o.original_filename AS output_name, o.total_pages AS output_pages, o.created_at AS output_created_at
             FROM export_jobs j
             LEFT JOIN outputs o ON o.export_job_id = j.id
             WHERE j.id = :id AND j.user_id = :user_id LIMIT 1",
            ['id' => $jobId, 'user_id' => $userId]
        );
    }

    public static function getJobPagesSnapshot(int $jobId): array
    {
        return Database::fetchAll(
            "SELECT jp.*, d.original_filename AS current_doc_name
             FROM export_job_pages jp
             LEFT JOIN documents d ON d.id = jp.document_id
             WHERE jp.export_job_id = :job_id
             ORDER BY jp.output_order ASC",
            ['job_id' => $jobId]
        );
    }

    public static function countCompletedByUser(int $userId): int
    {
        $res = Database::fetchOne(
            "SELECT COUNT(*) AS total FROM export_jobs WHERE user_id = :user_id AND status = 'completed'",
            ['user_id' => $userId]
        );
        return (int)($res['total'] ?? 0);
    }

    public static function getRecentByUser(int $userId, int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT j.*, o.download_token, o.stored_filename, o.file_size, o.original_filename AS output_name
             FROM export_jobs j
             LEFT JOIN outputs o ON o.export_job_id = j.id
             WHERE j.user_id = :user_id
             ORDER BY j.created_at DESC LIMIT {$limit}",
            ['user_id' => $userId]
        );
    }

    public static function countAllForAdmin(
        ?string $search = null,
        ?string $jobType = null,
        ?string $status = null,
        ?int $filterUserId = null
    ): int {
        $sql = "SELECT COUNT(DISTINCT j.id) AS total
                FROM export_jobs j
                LEFT JOIN outputs o ON o.export_job_id = j.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $cleanId = ltrim(trim($search), '#');
            if (is_numeric($cleanId)) {
                $sql .= " AND (j.id = :search_id OR o.original_filename LIKE :search_name OR o.stored_filename LIKE :search_stored)";
                $params['search_id'] = (int)$cleanId;
                $params['search_name'] = '%' . $search . '%';
                $params['search_stored'] = '%' . $search . '%';
            } else {
                $sql .= " AND (o.original_filename LIKE :s_orig OR o.stored_filename LIKE :s_stored OR j.job_type LIKE :s_type)";
                $searchWildcard = '%' . $search . '%';
                $params['s_orig'] = $searchWildcard;
                $params['s_stored'] = $searchWildcard;
                $params['s_type'] = $searchWildcard;
            }
        }

        if (!empty($jobType) && in_array($jobType, ['merge', 'split', 'editor'], true)) {
            $sql .= " AND j.job_type = :job_type";
            $params['job_type'] = $jobType;
        }

        if (!empty($status) && in_array($status, ['completed', 'failed', 'pending'], true)) {
            $sql .= " AND j.status = :status";
            $params['status'] = $status;
        }

        if (!empty($filterUserId)) {
            $sql .= " AND j.user_id = :user_id";
            $params['user_id'] = $filterUserId;
        }

        $res = Database::fetchOne($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    public static function getPaginatedForAdmin(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $jobType = null,
        ?string $status = null,
        ?int $filterUserId = null,
        string $sort = 'created_at_desc'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT j.*, u.name AS user_name, u.email AS user_email,
                       o.download_token, o.stored_filename, o.file_size, o.original_filename AS output_name, o.total_pages AS output_pages
                FROM export_jobs j
                LEFT JOIN users u ON u.id = j.user_id
                LEFT JOIN outputs o ON o.export_job_id = j.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $cleanId = ltrim(trim($search), '#');
            if (is_numeric($cleanId)) {
                $sql .= " AND (j.id = :search_id OR o.original_filename LIKE :search_name OR o.stored_filename LIKE :search_stored)";
                $params['search_id'] = (int)$cleanId;
                $params['search_name'] = '%' . $search . '%';
                $params['search_stored'] = '%' . $search . '%';
            } else {
                $sql .= " AND (o.original_filename LIKE :s_orig OR o.stored_filename LIKE :s_stored OR j.job_type LIKE :s_type)";
                $searchWildcard = '%' . $search . '%';
                $params['s_orig'] = $searchWildcard;
                $params['s_stored'] = $searchWildcard;
                $params['s_type'] = $searchWildcard;
            }
        }

        if (!empty($jobType) && in_array($jobType, ['merge', 'split', 'editor'], true)) {
            $sql .= " AND j.job_type = :job_type";
            $params['job_type'] = $jobType;
        }

        if (!empty($status) && in_array($status, ['completed', 'failed', 'pending'], true)) {
            $sql .= " AND j.status = :status";
            $params['status'] = $status;
        }

        if (!empty($filterUserId)) {
            $sql .= " AND j.user_id = :user_id";
            $params['user_id'] = $filterUserId;
        }

        switch ($sort) {
            case 'created_at_asc':
                $sql .= " ORDER BY j.created_at ASC";
                break;
            case 'size_desc':
                $sql .= " ORDER BY o.file_size DESC";
                break;
            case 'pages_desc':
                $sql .= " ORDER BY o.total_pages DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY o.original_filename ASC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY j.created_at DESC";
                break;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        return Database::fetchAll($sql, $params);
    }

    public static function getJobDetailsForAdmin(int $jobId): ?array
    {
        return Database::fetchOne(
            "SELECT j.*, u.name AS user_name, u.email AS user_email,
                    o.download_token, o.stored_filename, o.file_size, o.original_filename AS output_name, o.total_pages AS output_pages, o.created_at AS output_created_at
             FROM export_jobs j
             LEFT JOIN users u ON u.id = j.user_id
             LEFT JOIN outputs o ON o.export_job_id = j.id
             WHERE j.id = :id LIMIT 1",
            ['id' => $jobId]
        );
    }

    public static function getOverallStats(): array
    {
        $jobStats = Database::fetchOne(
            "SELECT 
                COUNT(*) AS total_jobs,
                SUM(IF(status = 'completed', 1, 0)) AS completed_jobs,
                SUM(IF(status = 'failed', 1, 0)) AS failed_jobs,
                SUM(IF(job_type = 'merge', 1, 0)) AS merge_jobs,
                SUM(IF(job_type = 'split', 1, 0)) AS split_jobs,
                SUM(IF(job_type = 'editor', 1, 0)) AS editor_jobs
             FROM export_jobs"
        );

        $docStats = Database::fetchOne("SELECT COUNT(*) AS total_docs, IFNULL(SUM(file_size), 0) AS total_source_size FROM documents");
        $outputStats = Database::fetchOne("SELECT COUNT(*) AS total_outputs, IFNULL(SUM(file_size), 0) AS total_output_size FROM outputs");
        $userStats = Database::fetchOne("SELECT COUNT(*) AS total_users, SUM(IF(role = 'admin', 1, 0)) AS total_admins FROM users");

        return [
            'total_jobs' => (int)($jobStats['total_jobs'] ?? 0),
            'completed_jobs' => (int)($jobStats['completed_jobs'] ?? 0),
            'failed_jobs' => (int)($jobStats['failed_jobs'] ?? 0),
            'merge_jobs' => (int)($jobStats['merge_jobs'] ?? 0),
            'split_jobs' => (int)($jobStats['split_jobs'] ?? 0),
            'editor_jobs' => (int)($jobStats['editor_jobs'] ?? 0),
            'total_docs' => (int)($docStats['total_docs'] ?? 0),
            'total_source_size' => (int)($docStats['total_source_size'] ?? 0),
            'total_outputs' => (int)($outputStats['total_outputs'] ?? 0),
            'total_output_size' => (int)($outputStats['total_output_size'] ?? 0),
            'total_users' => (int)($userStats['total_users'] ?? 0),
            'total_admins' => (int)($userStats['total_admins'] ?? 0),
        ];
    }

    public static function getRecentAll(int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT j.*, u.name AS user_name, o.original_filename AS output_name
             FROM export_jobs j
             LEFT JOIN users u ON u.id = j.user_id
             LEFT JOIN outputs o ON o.export_job_id = j.id
             ORDER BY j.created_at DESC LIMIT {$limit}"
        );
    }
}
