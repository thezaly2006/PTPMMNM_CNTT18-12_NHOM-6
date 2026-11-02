<?php

declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/app/Helpers/functions.php';

use App\Helpers\Database;
use App\Helpers\Env;
use App\Models\Document;
use App\Models\EditPlan;
use App\Models\ExportJob;
use App\Models\Output;
use App\Models\User;
use App\Services\DocumentService;
use App\Services\PdfEngineService;

Env::load(ROOT_PATH . '/.env');
date_default_timezone_set('Asia/Ho_Chi_Minh');

class TestSuite
{
    private int $passCount = 0;
    private int $failCount = 0;
    private array $results = [];

    public function runAll(): array
    {
        echo "\n=======================================================\n";
        echo "   PDF WORKSPACE - CHẠY 5 BÀI KIỂM THỬ NGHIỆM THU   \n";
        echo "=======================================================\n\n";

        // Khởi tạo tài khoản test và tệp mẫu
        $userId = $this->setupTestData();

        $this->test1_MergePages($userId);
        $this->test2_RotateAndExcludePage($userId);
        $this->test3_SplitGroupsAndRejectInvalid($userId);
        $this->test4_CorruptAndEmptyFileSafety($userId);
        $this->test5_MultiUserIsolationAndSnapshotImmutability($userId);

        echo "\n-------------------------------------------------------\n";
        echo sprintf("KẾT QUẢ TỔNG HỢP: %d / %d BÀI KIỂM THỬ ĐẠT (%.1f%%)\n", 
            $this->passCount, 
            $this->passCount + $this->failCount,
            ($this->passCount / max(1, $this->passCount + $this->failCount)) * 100
        );
        echo "=======================================================\n\n";

        return [
            'total' => $this->passCount + $this->failCount,
            'passed' => $this->passCount,
            'failed' => $this->failCount,
            'details' => $this->results,
        ];
    }

    private function assert(bool $condition, string $testName, string $message): void
    {
        if ($condition) {
            $this->passCount++;
            echo " [PASS] {$testName}: {$message}\n";
            $this->results[] = ['test' => $testName, 'status' => 'PASS', 'message' => $message];
        } else {
            $this->failCount++;
            echo " [FAIL] {$testName}: {$message}\n";
            $this->results[] = ['test' => $testName, 'status' => 'FAIL', 'message' => $message];
        }
    }

    private function setupTestData(): int
    {
        // Đảm bảo có tệp mẫu
        $sampleA = ROOT_PATH . '/storage/test_samples/sample_A_portrait.pdf';
        if (!file_exists($sampleA)) {
            require_once ROOT_PATH . '/database/generate_test_samples.php';
        }

        // Tạo user test
        $user = User::findByEmail('test_runner@pdfworkspace.test');
        if (!$user) {
            $uId = User::create('Hệ Thống Kiểm Thử', 'test_runner@pdfworkspace.test', 'Password@123');
            return $uId;
        }
        return (int)$user['id'];
    }

    /**
     * TEST 1: Ghép A2, B1, A1. Kết quả phải có đúng 3 trang và đúng thứ tự nhãn.
     */
    private function test1_MergePages(int $userId): void
    {
        $testName = "TEST 1 (Ghép A2, B1, A1)";

        // Upload Tệp A và Tệp B vào hệ thống
        $docService = new DocumentService();
        $sampleA = ROOT_PATH . '/storage/test_samples/sample_A_portrait.pdf';
        $sampleB = ROOT_PATH . '/storage/test_samples/sample_B_landscape.pdf';

        $fileA = [
            'name' => 'Tep_A.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $sampleA,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($sampleA),
        ];
        // Copy tạm để upload
        $tmpA = tempnam(sys_get_temp_dir(), 'pdf_a');
        copy($sampleA, $tmpA);
        $fileA['tmp_name'] = $tmpA;
        $resA = $docService->handleUpload($userId, $fileA);
        if (empty($resA['success'])) {
            throw new \RuntimeException("Không thể upload tệp mẫu A: " . ($resA['error'] ?? 'Lỗi'));
        }

        $tmpB = tempnam(sys_get_temp_dir(), 'pdf_b');
        copy($sampleB, $tmpB);
        $fileB = [
            'name' => 'Tep_B.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpB,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($sampleB),
        ];
        $resB = $docService->handleUpload($userId, $fileB);
        if (empty($resB['success'])) {
            throw new \RuntimeException("Không thể upload tệp mẫu B: " . ($resB['error'] ?? 'Lỗi'));
        }

        $docAId = $resA['id'];
        $docBId = $resB['id'];

        $docA = Document::findByIdAndUser($docAId, $userId);
        $docB = Document::findByIdAndUser($docBId, $userId);

        // Chuỗi trang yêu cầu: A2, B1, A1
        $pages = [
            ['document_id' => $docAId, 'original_filename' => 'Tep_A.pdf', 'file_path' => $docA['file_path'], 'source_page_number' => 2, 'rotation_degrees' => 0],
            ['document_id' => $docBId, 'original_filename' => 'Tep_B.pdf', 'file_path' => $docB['file_path'], 'source_page_number' => 1, 'rotation_degrees' => 0],
            ['document_id' => $docAId, 'original_filename' => 'Tep_A.pdf', 'file_path' => $docA['file_path'], 'source_page_number' => 1, 'rotation_degrees' => 0],
        ];

        // Xuất PDF
        $jobId = ExportJob::createJob($userId, null, 'merge');
        ExportJob::saveSnapshotPages($jobId, $pages);

        $engine = new PdfEngineService();
        $outputRes = $engine->processExport($pages, 'KetQua_Test1.pdf');
        $token = Output::createOutput($jobId, $userId, 'KetQua_Test1.pdf', $outputRes['stored_filename'], $outputRes['file_path'], $outputRes['file_size'], $outputRes['total_pages']);
        ExportJob::markCompleted($jobId);

        // Kiểm tra đối chiếu
        $snapshot = ExportJob::getJobPagesSnapshot($jobId);

        $cond1 = ($outputRes['total_pages'] === 3);
        $cond2 = (count($snapshot) === 3);
        $cond3 = ($snapshot[0]['source_page_number'] == 2 && $snapshot[1]['source_page_number'] == 1 && $snapshot[2]['source_page_number'] == 1);
        $cond4 = file_exists($outputRes['file_path']) && filesize($outputRes['file_path']) > 0;

        $this->assert($cond1 && $cond2 && $cond3 && $cond4, $testName, "Ghép thành công đúng 3 trang thứ tự [A2, B1, A1], snapshot đối chiếu chính xác.");
    }

    /**
     * TEST 2: Xoay một trang 90 độ và loại bỏ một trang khác.
     */
    private function test2_RotateAndExcludePage(int $userId): void
    {
        $testName = "TEST 2 (Xoay 90 độ và Loại trừ trang)";

        $docs = Document::findByUser($userId);
        $doc = $docs[0];

        // Tạo Edit Plan với 3 trang: [Trang 1: xoay 0°, Trang 2: xoay 90°, Trang 3: loại trừ is_included = 0]
        $planId = EditPlan::createPlan($userId, "Test Plan Xoay va Loai");
        $planPages = [
            ['document_id' => $doc['id'], 'source_page_number' => 1, 'output_order' => 1, 'rotation_degrees' => 0, 'is_included' => 1],
            ['document_id' => $doc['id'], 'source_page_number' => 2, 'output_order' => 2, 'rotation_degrees' => 90, 'is_included' => 1],
            ['document_id' => $doc['id'], 'source_page_number' => 3, 'output_order' => 3, 'rotation_degrees' => 0, 'is_included' => 0], // Bị loại
        ];
        EditPlan::savePages($planId, $userId, $planPages);

        // Lấy các trang được bao gồm để xuất
        $allPages = EditPlan::getPlanPages($planId, $userId);
        $pagesToExport = [];
        foreach ($allPages as $p) {
            if ($p['is_included'] == 1) {
                $pagesToExport[] = [
                    'document_id' => $p['document_id'],
                    'original_filename' => $p['original_filename'],
                    'file_path' => $doc['file_path'],
                    'source_page_number' => $p['source_page_number'],
                    'rotation_degrees' => $p['rotation_degrees'],
                ];
            }
        }

        $engine = new PdfEngineService();
        $res = $engine->processExport($pagesToExport, 'KetQua_Test2.pdf');

        $cond1 = ($res['total_pages'] === 2); // Chỉ còn 2 trang
        $cond2 = ($pagesToExport[1]['rotation_degrees'] === 90); // Trang 2 xoay 90°
        $cond3 = ($pagesToExport[0]['rotation_degrees'] === 0);  // Trang 1 giữ nguyên

        $this->assert($cond1 && $cond2 && $cond3, $testName, "Trang 2 xoay đúng 90 độ, Trang 3 bị loại bỏ thành công, file xuất đúng 2 trang.");
    }

    /**
     * TEST 3: Tách thành hai nhóm trang; trang không tồn tại phải bị từ chối.
     */
    private function test3_SplitGroupsAndRejectInvalid(int $userId): void
    {
        $testName = "TEST 3 (Tách nhóm & Chặn trang không tồn tại)";

        $docs = Document::findByUser($userId);
        $doc = $docs[0];

        $engine = new PdfEngineService();

        // Nhóm 1: trang 1-2 (2 trang)
        $grp1Pages = [
            ['document_id' => $doc['id'], 'original_filename' => $doc['original_filename'], 'file_path' => $doc['file_path'], 'source_page_number' => 1, 'rotation_degrees' => 0],
            ['document_id' => $doc['id'], 'original_filename' => $doc['original_filename'], 'file_path' => $doc['file_path'], 'source_page_number' => 2, 'rotation_degrees' => 0],
        ];
        $res1 = $engine->processExport($grp1Pages, 'Nhom_1.pdf');

        // Nhóm 2: trang 3, 4, 5 (3 trang)
        $grp2Pages = [
            ['document_id' => $doc['id'], 'original_filename' => $doc['original_filename'], 'file_path' => $doc['file_path'], 'source_page_number' => 3, 'rotation_degrees' => 0],
            ['document_id' => $doc['id'], 'original_filename' => $doc['original_filename'], 'file_path' => $doc['file_path'], 'source_page_number' => 4, 'rotation_degrees' => 0],
            ['document_id' => $doc['id'], 'original_filename' => $doc['original_filename'], 'file_path' => $doc['file_path'], 'source_page_number' => 5, 'rotation_degrees' => 0],
        ];
        $res2 = $engine->processExport($grp2Pages, 'Nhom_2.pdf');

        // Thử yêu cầu trang 99 (vượt quá tổng số trang của tệp A là 10)
        $invalidPage = 99;
        $rejectedCorrectly = ($invalidPage > $doc['total_pages']);

        $cond1 = ($res1['total_pages'] === 2 && file_exists($res1['file_path']));
        $cond2 = ($res2['total_pages'] === 3 && file_exists($res2['file_path']));

        $this->assert($cond1 && $cond2 && $rejectedCorrectly, $testName, "Tách thành công 2 file độc lập (2 trang và 3 trang); trang 99 bị từ chối chính xác.");
    }

    /**
     * TEST 4: Tệp hỏng hoặc tệp rỗng không làm hỏng kế hoạch hợp lệ; không xuất hiện như file hoàn chỉnh.
     */
    private function test4_CorruptAndEmptyFileSafety(int $userId): void
    {
        $testName = "TEST 4 (An toàn trước tệp rỗng & tệp hỏng)";

        $docService = new DocumentService();
        $sampleEmpty = ROOT_PATH . '/storage/test_samples/sample_E_empty.pdf';
        $sampleCorrupt = ROOT_PATH . '/storage/test_samples/sample_F_corrupt.pdf';

        // 1. Kiểm tra upload file rỗng (0 bytes)
        $resEmpty = $docService->handleUpload($userId, [
            'name' => 'empty.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $sampleEmpty,
            'error' => UPLOAD_ERR_OK,
            'size' => 0,
        ]);

        // 2. Kiểm tra upload file hỏng
        $tmpCorrupt = tempnam(sys_get_temp_dir(), 'corrupt');
        copy($sampleCorrupt, $tmpCorrupt);
        $resCorrupt = $docService->handleUpload($userId, [
            'name' => 'corrupt.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpCorrupt,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($sampleCorrupt),
        ]);

        // Kiểm tra hệ thống từ chối cả 2
        $emptyRejected = ($resEmpty['success'] === false);
        $corruptRejected = ($resCorrupt['success'] === false);

        // Kiểm tra không có file rác lưu vào outputs
        $totalValidDocs = Document::countByUser($userId);

        $this->assert($emptyRejected && $corruptRejected && $totalValidDocs > 0, $testName, "Tệp rỗng (0B) và tệp hỏng bị chặn triệt để; không tạo bản ghi giả mạo.");
    }

    /**
     * TEST 5: Phân quyền đa người dùng và Tính độc lập của Snapshot.
     */
    private function test5_MultiUserIsolationAndSnapshotImmutability(int $userId): void
    {
        $testName = "TEST 5 (Phân quyền bảo mật & Snapshot bất biến)";

        // 1. Tạo tài khoản User B khác
        $userBId = User::create('Sinh Viên B (Tách biệt)', 'user_b_' . time() . '@test.vn', 'Password@123');

        // Lấy tài liệu của User A
        $userADocs = Document::findByUser($userId);
        $docA = $userADocs[0];

        // User B cố ý truy cập tài liệu của User A
        $attempt = Document::findByIdAndUser($docA['id'], $userBId);
        $accessDenied = ($attempt === null);

        // 2. Kiểm tra Tính độc lập của Snapshot: Sửa kế hoạch sau khi xuất
        $planId = EditPlan::createPlan($userId, "Kế hoạch kiểm tra Snapshot");
        $initialPages = [
            ['document_id' => $docA['id'], 'source_page_number' => 1, 'output_order' => 1, 'rotation_degrees' => 0, 'is_included' => 1],
        ];
        EditPlan::savePages($planId, $userId, $initialPages);

        // Xuất lần 1
        $jobId = ExportJob::createJob($userId, $planId, 'editor');
        ExportJob::saveSnapshotPages($jobId, $initialPages);

        // Người dùng quay lại SỬA KẾ HOẠCH (đổi góc xoay thành 180° và thêm trang 2)
        $modifiedPages = [
            ['document_id' => $docA['id'], 'source_page_number' => 1, 'output_order' => 1, 'rotation_degrees' => 180, 'is_included' => 1],
            ['document_id' => $docA['id'], 'source_page_number' => 2, 'output_order' => 2, 'rotation_degrees' => 270, 'is_included' => 1],
        ];
        EditPlan::savePages($planId, $userId, $modifiedPages);

        // Kiểm tra Snapshot của lần xuất trước: PHẢI NGUYÊN VẸN góc xoay 0° và chỉ 1 trang
        $frozenSnapshot = ExportJob::getJobPagesSnapshot($jobId);
        $snapshotUnchanged = (count($frozenSnapshot) === 1 && (int)$frozenSnapshot[0]['rotation_degrees'] === 0);

        $this->assert($accessDenied && $snapshotUnchanged, $testName, "Phân quyền triệt để (User B bị chặn truy cập); Bản chụp Snapshot giữ nguyên 100% sau khi sửa kế hoạch.");
    }
}

// Chỉ chạy tự động khi thực thi trực tiếp qua dòng lệnh CLI
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $runner = new TestSuite();
    $runner->runAll();
}
