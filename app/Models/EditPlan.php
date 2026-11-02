<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class EditPlan
{
    public static function countByUser(int $userId): int
    {
        $res = Database::fetchOne(
            "SELECT COUNT(*) AS total FROM edit_plans WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
        return (int)($res['total'] ?? 0);
    }

    public static function getRecentByUser(int $userId, int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT p.*, COUNT(pp.id) as total_pages 
             FROM edit_plans p
             LEFT JOIN edit_plan_pages pp ON pp.plan_id = p.id AND pp.is_included = 1
             WHERE p.user_id = :user_id 
             GROUP BY p.id
             ORDER BY p.updated_at DESC LIMIT {$limit}",
            ['user_id' => $userId]
        );
    }

    public static function getAllByUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT p.*, COUNT(pp.id) as total_pages 
             FROM edit_plans p
             LEFT JOIN edit_plan_pages pp ON pp.plan_id = p.id AND pp.is_included = 1
             WHERE p.user_id = :user_id 
             GROUP BY p.id
             ORDER BY p.updated_at DESC",
            ['user_id' => $userId]
        );
    }

    public static function findByIdAndUser(int $id, int $userId): ?array
    {
        return Database::fetchOne(
            "SELECT * FROM edit_plans WHERE id = :id AND user_id = :user_id LIMIT 1",
            ['id' => $id, 'user_id' => $userId]
        );
    }

    public static function createPlan(int $userId, string $title, ?string $description = null): int
    {
        Database::query(
            "INSERT INTO edit_plans (user_id, title, description, created_at, updated_at) 
             VALUES (:user_id, :title, :description, NOW(), NOW())",
            [
                'user_id' => $userId,
                'title' => trim($title) ?: 'Phiên chỉnh sửa mới',
                'description' => $description,
            ]
        );
        return Database::lastInsertId();
    }

    public static function updatePlanTitle(int $planId, int $userId, string $title): void
    {
        Database::query(
            "UPDATE edit_plans SET title = :title, updated_at = NOW() WHERE id = :id AND user_id = :user_id",
            [
                'title' => trim($title),
                'id' => $planId,
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Lưu danh sách các trang trong phiên chỉnh sửa
     */
    public static function savePages(int $planId, int $userId, array $pages): void
    {
        $plan = self::findByIdAndUser($planId, $userId);
        if (!$plan) {
            throw new \RuntimeException("Không tìm thấy kế hoạch chỉnh sửa hoặc bạn không có quyền.");
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // Xóa danh sách trang cũ của kế hoạch
            Database::query("DELETE FROM edit_plan_pages WHERE plan_id = :plan_id", ['plan_id' => $planId]);

            // Thêm lại toàn bộ trang theo đúng thứ tự mới
            $sql = "INSERT INTO edit_plan_pages (plan_id, document_id, source_page_number, output_order, rotation_degrees, fit_portrait, is_included, created_at)
                    VALUES (:plan_id, :document_id, :source_page_number, :output_order, :rotation_degrees, :fit_portrait, :is_included, NOW())";
            $stmt = $pdo->prepare($sql);

            $order = 1;
            foreach ($pages as $p) {
                // Kiểm tra người dùng có quyền sở hữu tài liệu nguồn này không
                $docId = (int)$p['document_id'];
                $checkDoc = Document::findByIdAndUser($docId, $userId);
                if (!$checkDoc) {
                    continue; // Bỏ qua trang không có quyền
                }

                $rotation = (int)($p['rotation_degrees'] ?? 0);
                // Chuẩn hóa góc xoay (0, 90, 180, 270)
                $rotation = ((($rotation % 360) + 360) % 360);

                $stmt->execute([
                    'plan_id' => $planId,
                    'document_id' => $docId,
                    'source_page_number' => (int)$p['source_page_number'],
                    'output_order' => $order++,
                    'rotation_degrees' => $rotation,
                    'fit_portrait' => !empty($p['fit_portrait']) ? 1 : 0,
                    'is_included' => !empty($p['is_included']) ? 1 : 0,
                ]);
            }

            // Cập nhật updated_at của plan
            Database::query("UPDATE edit_plans SET updated_at = NOW() WHERE id = :id", ['id' => $planId]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Lấy toàn bộ thông tin trang của một kế hoạch
     */
    public static function getPlanPages(int $planId, int $userId): array
    {
        $plan = self::findByIdAndUser($planId, $userId);
        if (!$plan) {
            return [];
        }

        return Database::fetchAll(
            "SELECT pp.*, d.original_filename, d.stored_filename, d.file_size
             FROM edit_plan_pages pp
             JOIN documents d ON d.id = pp.document_id
             WHERE pp.plan_id = :plan_id
             ORDER BY pp.output_order ASC",
            ['plan_id' => $planId]
        );
    }

    public static function delete(int $planId, int $userId): bool
    {
        $plan = self::findByIdAndUser($planId, $userId);
        if (!$plan) {
            return false;
        }

        Database::query("DELETE FROM edit_plans WHERE id = :id AND user_id = :user_id", [
            'id' => $planId,
            'user_id' => $userId,
        ]);
        return true;
    }
}
