<?php

declare(strict_types=1);

namespace App\Controllers;

class TestController extends BaseController
{
    public function index(): void
    {
        $this->render('tests.index', [
            'pageTitle' => 'Bộ kiểm thử nghiệm thu - PDF Workspace',
            'activeNav' => 'tests',
        ]);
    }

    public function run(): void
    {
        // Làm sạch toàn bộ buffer trước đó nếu có
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        ob_start();

        try {
            require_once ROOT_PATH . '/tests/run_tests.php';
            $runner = new \TestSuite();
            $summary = $runner->runAll();
            $outputLogs = ob_get_clean();

            $this->json([
                'success' => true,
                'summary' => $summary,
                'logs' => $outputLogs,
            ]);
        } catch (\Throwable $e) {
            $outputLogs = ob_get_clean();
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'logs' => $outputLogs,
            ], 500);
        }
    }
}
