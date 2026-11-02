<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class Document
{
    public static function findByUser(int $userId, ?string $search = null, string $sort = 'created_at_desc'): array
    {
        $sql = "SELECT * FROM documents WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND original_filename LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY original_filename ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY original_filename DESC";
                break;
            case 'size_desc':
                $sql .= " ORDER BY file_size DESC";
                break;
            case 'pages_desc':
                $sql .= " ORDER BY total_pages DESC";
                break;
            case 'created_at_asc':
                $sql .= " ORDER BY created_at ASC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY created_at DESC";
                break;
        }

        $documents = Database::fetchAll($sql, $params);

        // Tự động đồng bộ số trang thực tế nếu trước đó bị đếm sai
        foreach ($documents as &$doc) {
            if (file_exists($doc['file_path'])) {
                try {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $actual = (int)$fpdi->setSourceFile($doc['file_path']);
                    if ($actual > 0 && $actual !== (int)$doc['total_pages']) {
                        $doc['total_pages'] = $actual;
                        Database::query("UPDATE documents SET total_pages = :tp WHERE id = :id", [
                            'tp' => $actual,
                            'id' => $doc['id']
                        ]);
                    }
                } catch (\Throwable $e) {}
            }
        }
        unset($doc);

        return $documents;
    }

    public static function findByIdAndUser(int $id, int $userId): ?array
    {
        $doc = Database::fetchOne(
            "SELECT * FROM documents WHERE id = :id AND user_id = :user_id LIMIT 1",
            ['id' => $id, 'user_id' => $userId]
        );

        if ($doc && file_exists($doc['file_path'])) {
            try {
                $fpdi = new \setasign\Fpdi\Fpdi();
                $actual = (int)$fpdi->setSourceFile($doc['file_path']);
                if ($actual > 0 && $actual !== (int)$doc['total_pages']) {
                    $doc['total_pages'] = $actual;
                    Database::query("UPDATE documents SET total_pages = :tp WHERE id = :id", [
                        'tp' => $actual,
                        'id' => $doc['id']
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        return $doc;
    }

    public static function create(
        int $userId,
        string $originalFilename,
        string $storedFilename,
        string $filePath,
        int $fileSize,
        int $totalPages,
        string $mimeType = 'application/pdf'
    ): int {
        Database::query(
            "INSERT INTO documents (user_id, original_filename, stored_filename, file_path, file_size, total_pages, mime_type, created_at)
             VALUES (:user_id, :original_filename, :stored_filename, :file_path, :file_size, :total_pages, :mime_type, NOW())",
            [
                'user_id' => $userId,
                'original_filename' => $originalFilename,
                'stored_filename' => $storedFilename,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'total_pages' => $totalPages,
                'mime_type' => $mimeType,
            ]
        );
        return Database::lastInsertId();
    }

    public static function createSourcePage(
        int $documentId,
        int $pageNumber,
        ?float $width = null,
        ?float $height = null,
        string $orientation = 'portrait'
    ): void {
        Database::query(
            "INSERT INTO source_pages (document_id, page_number, width, height, orientation, created_at)
             VALUES (:document_id, :page_number, :width, :height, :orientation, NOW())",
            [
                'document_id' => $documentId,
                'page_number' => $pageNumber,
                'width' => $width,
                'height' => $height,
                'orientation' => $orientation,
            ]
        );
    }

    public static function getSourcePages(int $documentId): array
    {
        return Database::fetchAll(
            "SELECT * FROM source_pages WHERE document_id = :document_id ORDER BY page_number ASC",
            ['document_id' => $documentId]
        );
    }

    public static function delete(int $id, int $userId): bool
    {
        $doc = self::findByIdAndUser($id, $userId);
        if (!$doc) {
            return false;
        }

        // Xóa file vật lý trong storage/sources
        if (file_exists($doc['file_path'])) {
            unlink($doc['file_path']);
        }

        // Xóa trong database (foreign key sẽ tự động cascade source_pages)
        Database::query(
            "DELETE FROM documents WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );

        return true;
    }

    public static function countByUser(int $userId, ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM documents WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND original_filename LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $res = Database::fetchOne($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    public static function getPaginatedByUser(
        int $userId,
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        string $sort = 'created_at_desc'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM documents WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND original_filename LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY original_filename ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY original_filename DESC";
                break;
            case 'size_desc':
                $sql .= " ORDER BY file_size DESC";
                break;
            case 'pages_desc':
                $sql .= " ORDER BY total_pages DESC";
                break;
            case 'created_at_asc':
                $sql .= " ORDER BY created_at ASC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY created_at DESC";
                break;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        $documents = Database::fetchAll($sql, $params);

        foreach ($documents as &$doc) {
            if (file_exists($doc['file_path'])) {
                try {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $actual = (int)$fpdi->setSourceFile($doc['file_path']);
                    if ($actual > 0 && $actual !== (int)$doc['total_pages']) {
                        $doc['total_pages'] = $actual;
                        Database::query("UPDATE documents SET total_pages = :tp WHERE id = :id", [
                            'tp' => $actual,
                            'id' => $doc['id']
                        ]);
                    }
                } catch (\Throwable $e) {}
            }
        }
        unset($doc);

        return $documents;
    }

    public static function getRecentByUser(int $userId, int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT * FROM documents WHERE user_id = :user_id ORDER BY created_at DESC LIMIT {$limit}",
            ['user_id' => $userId]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT d.*, u.name AS user_name, u.email AS user_email 
             FROM documents d
             LEFT JOIN users u ON u.id = d.user_id
             WHERE d.id = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public static function countAllForAdmin(?string $search = null, ?int $filterUserId = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM documents d WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.original_filename LIKE :search OR d.stored_filename LIKE :search_stored)";
            $params['search'] = '%' . $search . '%';
            $params['search_stored'] = '%' . $search . '%';
        }

        if (!empty($filterUserId)) {
            $sql .= " AND d.user_id = :user_id";
            $params['user_id'] = $filterUserId;
        }

        $res = Database::fetchOne($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    public static function getPaginatedForAdmin(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?int $filterUserId = null,
        string $sort = 'created_at_desc'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT d.*, u.name AS user_name, u.email AS user_email 
                FROM documents d
                LEFT JOIN users u ON u.id = d.user_id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.original_filename LIKE :search OR d.stored_filename LIKE :search_stored)";
            $params['search'] = '%' . $search . '%';
            $params['search_stored'] = '%' . $search . '%';
        }

        if (!empty($filterUserId)) {
            $sql .= " AND d.user_id = :user_id";
            $params['user_id'] = $filterUserId;
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY d.original_filename ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY d.original_filename DESC";
                break;
            case 'size_desc':
                $sql .= " ORDER BY d.file_size DESC";
                break;
            case 'pages_desc':
                $sql .= " ORDER BY d.total_pages DESC";
                break;
            case 'created_at_asc':
                $sql .= " ORDER BY d.created_at ASC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY d.created_at DESC";
                break;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        return Database::fetchAll($sql, $params);
    }

    public static function deleteByAdmin(int $id): bool
    {
        $doc = self::findById($id);
        if (!$doc) {
            return false;
        }

        if (file_exists($doc['file_path'])) {
            @unlink($doc['file_path']);
        }

        Database::query("DELETE FROM documents WHERE id = :id", ['id' => $id]);
        return true;
    }
}
