<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class Subscription
{
    /**
     * Đếm tổng số đơn đăng ký gói theo bộ lọc
     */
    public static function countAll(
        ?string $search = null,
        ?string $tier = null,
        ?string $status = null,
        ?string $cycle = null
    ): int {
        $sql = "SELECT COUNT(*) AS total 
                FROM subscriptions s
                JOIN users u ON s.user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.name LIKE :s_name OR u.email LIKE :s_email OR s.transaction_code LIKE :s_code)";
            $params['s_name'] = '%' . $search . '%';
            $params['s_email'] = '%' . $search . '%';
            $params['s_code'] = '%' . $search . '%';
        }

        if (!empty($tier) && in_array($tier, ['plus', 'pro', 'premium'], true)) {
            $sql .= " AND s.plan_tier = :tier";
            $params['tier'] = $tier;
        }

        if (!empty($status) && in_array($status, ['completed', 'pending', 'cancelled'], true)) {
            $sql .= " AND s.status = :status";
            $params['status'] = $status;
        }

        if (!empty($cycle) && in_array($cycle, ['weekly', 'monthly', 'yearly'], true)) {
            $sql .= " AND s.billing_cycle = :cycle";
            $params['cycle'] = $cycle;
        }

        $res = Database::fetchOne($sql, $params);
        return (int)($res['total'] ?? 0);
    }

    /**
     * Lấy danh sách đăng ký gói phân trang cho Quản trị viên
     */
    public static function getPaginated(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $tier = null,
        ?string $status = null,
        ?string $cycle = null,
        string $sort = 'created_at_desc'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT s.*, 
                       u.name AS user_name, 
                       u.email AS user_email, 
                       u.tokens AS user_current_tokens,
                       u.plan_tier AS user_current_plan
                FROM subscriptions s
                JOIN users u ON s.user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.name LIKE :s_name OR u.email LIKE :s_email OR s.transaction_code LIKE :s_code)";
            $params['s_name'] = '%' . $search . '%';
            $params['s_email'] = '%' . $search . '%';
            $params['s_code'] = '%' . $search . '%';
        }

        if (!empty($tier) && in_array($tier, ['plus', 'pro', 'premium'], true)) {
            $sql .= " AND s.plan_tier = :tier";
            $params['tier'] = $tier;
        }

        if (!empty($status) && in_array($status, ['completed', 'pending', 'cancelled'], true)) {
            $sql .= " AND s.status = :status";
            $params['status'] = $status;
        }

        if (!empty($cycle) && in_array($cycle, ['weekly', 'monthly', 'yearly'], true)) {
            $sql .= " AND s.billing_cycle = :cycle";
            $params['cycle'] = $cycle;
        }

        switch ($sort) {
            case 'created_at_asc':
                $sql .= " ORDER BY s.created_at ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY s.price DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY s.price ASC";
                break;
            case 'tokens_desc':
                $sql .= " ORDER BY s.tokens_granted DESC";
                break;
            case 'created_at_desc':
            default:
                $sql .= " ORDER BY s.created_at DESC";
                break;
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Thống kê tổng hợp số liệu doanh thu & gói đăng ký
     */
    public static function getStats(): array
    {
        $totalRev = Database::fetchOne("SELECT SUM(price) as total FROM subscriptions WHERE status = 'completed'");
        $totalSubs = Database::fetchOne("SELECT COUNT(*) as total FROM subscriptions");
        $activeSubs = Database::fetchOne("SELECT COUNT(*) as total FROM subscriptions WHERE status = 'completed' AND (expires_at IS NULL OR expires_at > NOW())");
        $totalTokens = Database::fetchOne("SELECT SUM(tokens_granted) as total FROM subscriptions WHERE status = 'completed'");

        $tierStats = Database::fetchAll("
            SELECT plan_tier, COUNT(*) as count, SUM(price) as revenue 
            FROM subscriptions 
            WHERE status = 'completed' 
            GROUP BY plan_tier
        ");

        $byTier = [
            'plus' => ['count' => 0, 'revenue' => 0],
            'pro' => ['count' => 0, 'revenue' => 0],
            'premium' => ['count' => 0, 'revenue' => 0],
        ];

        foreach ($tierStats as $t) {
            $tierKey = strtolower($t['plan_tier'] ?? '');
            if (isset($byTier[$tierKey])) {
                $byTier[$tierKey]['count'] = (int)$t['count'];
                $byTier[$tierKey]['revenue'] = (int)$t['revenue'];
            }
        }

        return [
            'total_revenue' => (int)($totalRev['total'] ?? 0),
            'total_subscriptions' => (int)($totalSubs['total'] ?? 0),
            'active_subscriptions' => (int)($activeSubs['total'] ?? 0),
            'total_tokens_granted' => (int)($totalTokens['total'] ?? 0),
            'by_tier' => $byTier,
        ];
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne("
            SELECT s.*, u.name AS user_name, u.email AS user_email, u.tokens AS user_current_tokens, u.plan_tier AS user_current_plan
            FROM subscriptions s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = :id
        ", ['id' => $id]);
    }

    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['completed', 'pending', 'cancelled'], true)) {
            return false;
        }

        Database::query("UPDATE subscriptions SET status = :status WHERE id = :id", [
            'status' => $status,
            'id' => $id,
        ]);
        return true;
    }

    /**
     * Lấy toàn bộ biến động Token toàn hệ thống
     */
    public static function getGlobalTokenTransactions(int $limit = 30): array
    {
        return Database::fetchAll("
            SELECT t.*, u.name AS user_name, u.email AS user_email
            FROM token_transactions t
            JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
            LIMIT {$limit}
        ");
    }
}
