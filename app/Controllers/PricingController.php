<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class PricingController extends BaseController
{
    /**
     * Màn hình Bảng giá & Nâng cấp gói cước
     */
    public function index(): void
    {
        $userId = auth_id();
        $user = $userId ? User::findById($userId) : null;
        $transactions = $userId ? User::getTokenTransactions($userId, 10) : [];

        $selectedTier = trim($_GET['tier'] ?? '');
        $selectedCycle = trim($_GET['cycle'] ?? 'monthly');

        $this->render('pricing.index', [
            'pageTitle' => 'Nâng cấp Gói & Mua Token - PDF Workspace',
            'activeNav' => 'pricing',
            'user' => $user,
            'transactions' => $transactions,
            'selectedTier' => in_array($selectedTier, ['plus', 'pro', 'premium'], true) ? $selectedTier : '',
            'selectedCycle' => in_array($selectedCycle, ['weekly', 'monthly', 'yearly'], true) ? $selectedCycle : 'monthly',
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    /**
     * API thực thi mua / nâng cấp gói và nạp token
     */
    public function subscribe(): void
    {
        if (!auth_check()) {
            $this->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Vui lòng đăng nhập để nâng cấp gói cước và nạp token.'
            ], 401);
            return;
        }

        $userId = auth_id();
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        if (!$data) {
            $data = $_POST;
        }

        $tier = strtolower(trim($data['plan_tier'] ?? 'pro'));
        $cycle = strtolower(trim($data['billing_cycle'] ?? 'monthly'));
        $paymentMethod = trim($data['payment_method'] ?? 'vietqr');

        if (!in_array($tier, ['plus', 'pro', 'premium'], true)) {
            $this->json(['success' => false, 'message' => 'Gói cước không hợp lệ.'], 400);
            return;
        }

        if (!in_array($cycle, ['weekly', 'monthly', 'yearly'], true)) {
            $this->json(['success' => false, 'message' => 'Chu kỳ thanh toán không hợp lệ.'], 400);
            return;
        }

        // Bảng giá và số token tương ứng
        $pricingMatrix = [
            'plus' => [
                'name' => 'Plus',
                'weekly' => ['price' => 29000, 'tokens' => 500],
                'monthly' => ['price' => 79000, 'tokens' => 500],
                'yearly' => ['price' => 699000, 'tokens' => 6000],
            ],
            'pro' => [
                'name' => 'Pro',
                'weekly' => ['price' => 59000, 'tokens' => 2000],
                'monthly' => ['price' => 169000, 'tokens' => 2000],
                'yearly' => ['price' => 1490000, 'tokens' => 24000],
            ],
            'premium' => [
                'name' => 'Premium',
                'weekly' => ['price' => 99000, 'tokens' => 10000],
                'monthly' => ['price' => 299000, 'tokens' => 10000],
                'yearly' => ['price' => 2590000, 'tokens' => 120000],
            ],
        ];

        $package = $pricingMatrix[$tier][$cycle];
        $price = $package['price'];
        $tokens = $package['tokens'];

        try {
            $result = User::upgradePlan($userId, $tier, $cycle, $price, $tokens, $paymentMethod);

            $cycleName = match($cycle) {
                'weekly' => '1 Tuần',
                'yearly' => '1 Năm',
                default => '1 Tháng'
            };

            $this->json([
                'success' => true,
                'message' => "Chúc mừng! Bạn đã kích hoạt thành công Vô Hạn Token gói " . strtoupper($tier) . " ({$cycleName}) đến mốc 00:00 ngày {$result['expires_formatted']}!",
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Lỗi khi kích hoạt gói: ' . $e->getMessage()
            ], 500);
        }
    }
}
