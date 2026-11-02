<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use RuntimeException;
use setasign\Fpdi\Fpdi;

class PdfEngineService
{
    private string $tempDir;
    private string $outputDir;

    public function __construct()
    {
        $appConfig = require ROOT_PATH . '/config/app.php';
        $this->tempDir = $appConfig['paths']['temp'];
        $this->outputDir = $appConfig['paths']['outputs'];

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }
    }

    /**
     * Tìm đường dẫn chính xác của binary qpdf trên hệ thống
     */
    public function getQpdfBinary(): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        $candidates = ['/usr/bin/qpdf', '/usr/local/bin/qpdf', '/bin/qpdf', 'qpdf'];
        foreach ($candidates as $bin) {
            $output = [];
            $retCode = 1;
            @exec("{$bin} --version 2>&1", $output, $retCode);
            if ($retCode === 0) {
                return $bin;
            }
        }

        return null;
    }

    /**
     * Tự động giải nén file PDF v1.5+ qua QPDF để FPDI đọc mượt mà không bị lỗi nén
     */
    public function decompressPdfIfQpdfAvailable(string $filePath): string
    {
        $qpdfBin = $this->getQpdfBinary();
        if (!$qpdfBin) {
            return $filePath;
        }

        $decompPath = $this->tempDir . '/decomp_' . md5($filePath) . '.pdf';
        if (file_exists($decompPath) && filesize($decompPath) > 0) {
            return $decompPath;
        }

        $cmd = sprintf(
            '%s --warning-exit-0 --qdf --object-streams=disable %s %s 2>&1',
            $qpdfBin,
            escapeshellarg($filePath),
            escapeshellarg($decompPath)
        );

        $output = [];
        $code = 0;
        exec($cmd, $output, $code);

        if (($code === 0 || $code === 3) && file_exists($decompPath) && filesize($decompPath) > 0) {
            return $decompPath;
        }

        return $filePath;
    }

    /**
     * Lấy số trang thực tế của một tệp PDF an toàn
     */
    public function getActualPageCount(string $filePath): int
    {
        $qpdfBin = $this->getQpdfBinary();
        if ($qpdfBin) {
            $cmd = sprintf('%s --show-npages %s 2>&1', $qpdfBin, escapeshellarg($filePath));
            $output = [];
            $retCode = 0;
            exec($cmd, $output, $retCode);
            for ($k = count($output) - 1; $k >= 0; $k--) {
                $val = trim($output[$k]);
                if ($val !== '' && is_numeric($val)) {
                    return (int)$val;
                }
            }
        }

        try {
            $decomp = $this->decompressPdfIfQpdfAvailable($filePath);
            $fpdi = new Fpdi();
            $count = (int)$fpdi->setSourceFile($decomp);
            if ($count > 0) {
                return $count;
            }
        } catch (\Throwable $e) {}

        return 999999;
    }

    /**
     * Thực hiện quy trình xuất an toàn 7 bước
     */
    public function processExport(array $pagesToProcess, string $suggestedFilename): array
    {
        if (empty($pagesToProcess)) {
            throw new RuntimeException("Danh sách trang cần xuất đang trống.");
        }

        // 1. Lọc và chuẩn hóa số trang
        $sanitizedPages = [];
        $fileCounts = [];
        $hasFitPortrait = false;

        foreach ($pagesToProcess as $p) {
            $filePath = $p['file_path'];
            if (!file_exists($filePath)) {
                throw new RuntimeException("Tệp nguồn không tồn tại: " . ($p['original_filename'] ?? 'PDF'));
            }

            if (!isset($fileCounts[$filePath])) {
                $fileCounts[$filePath] = $this->getActualPageCount($filePath);
            }

            $pageNum = (int)$p['source_page_number'];
            $maxPages = $fileCounts[$filePath];

            if ($pageNum > $maxPages) {
                continue;
            }

            if (!empty($p['fit_portrait'])) {
                $hasFitPortrait = true;
            }

            $sanitizedPages[] = $p;
        }

        if (empty($sanitizedPages)) {
            throw new RuntimeException("Không có trang hợp lệ nào nằm trong phạm vi của tài liệu nguồn.");
        }

        // Tạo tên tệp lưu trữ và tệp tạm thời
        $tempFilename = 'temp_' . bin2hex(random_bytes(16)) . '.pdf';
        $tempPath = $this->tempDir . '/' . $tempFilename;

        $storedFilename = 'out_' . bin2hex(random_bytes(16)) . '.pdf';
        $finalPath = $this->outputDir . '/' . $storedFilename;

        try {
            $qpdfBin = $this->getQpdfBinary();
            $success = false;

            // Nếu CÓ yêu cầu thu vào A4 dọc (Fit to Portrait) -> dùng FPDI (đã tự động giải nén qua QPDF)
            if ($hasFitPortrait) {
                $this->processWithFpdi($sanitizedPages, $tempPath);
                $success = true;
            } else {
                // Nếu không có fit_portrait, ưu tiên QPDF CLI trực tiếp
                if ($qpdfBin) {
                    $success = $this->processWithQpdf($qpdfBin, $sanitizedPages, $tempPath);
                }

                // Nếu QPDF không khả dụng, dùng FPDI
                if (!$success) {
                    $this->processWithFpdi($sanitizedPages, $tempPath);
                    $success = true;
                }
            }

            // Kiểm tra tệp tạm sau khi tạo
            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                throw new RuntimeException("Không thể tạo tệp PDF hoặc tệp đầu ra bị rỗng (0 bytes).");
            }

            $fileSize = filesize($tempPath);
            $totalPages = count($sanitizedPages);

            // Di chuyển từ thư mục tạm sang outputs hoàn chỉnh
            if (!rename($tempPath, $finalPath)) {
                if (!copy($tempPath, $finalPath)) {
                    throw new RuntimeException("Không thể di chuyển tệp kết quả vào thư mục outputs.");
                }
                @unlink($tempPath);
            }

            return [
                'success' => true,
                'stored_filename' => $storedFilename,
                'file_path' => $finalPath,
                'file_size' => $fileSize,
                'total_pages' => $totalPages,
            ];
        } catch (\Throwable $e) {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            if (file_exists($finalPath)) {
                @unlink($finalPath);
            }

            throw new RuntimeException("Lỗi trong quá trình xuất PDF: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Xử lý bằng qpdf CLI trên Linux
     */
    private function processWithQpdf(string $qpdfBin, array $pages, string $outputPath): bool
    {
        $pagesArgs = [];
        $rotateArgs = [];
        $outOrder = 1;

        foreach ($pages as $p) {
            $filePath = $p['file_path'];
            $pageNum = (int)$p['source_page_number'];
            $pagesArgs[] = escapeshellarg($filePath);
            $pagesArgs[] = (string)$pageNum;

            $rotation = ((int)($p['rotation_degrees'] ?? 0)) % 360;
            if ($rotation < 0) $rotation += 360;

            if ($rotation > 0) {
                $rotateArgs[] = sprintf('--rotate=+%d:%d', $rotation, $outOrder);
            }
            $outOrder++;
        }

        $rotateStr = !empty($rotateArgs) ? implode(' ', $rotateArgs) : '';
        $cmd = sprintf(
            '%s --warning-exit-0 --empty %s --pages %s -- %s 2>&1',
            $qpdfBin,
            $rotateStr,
            implode(' ', $pagesArgs),
            escapeshellarg($outputPath)
        );

        $output = [];
        $retCode = 0;
        exec($cmd, $output, $retCode);

        // Ghi log chẩn đoán
        $logDir = ROOT_PATH . '/storage/logs';
        file_put_contents(
            $logDir . '/pdf_engine.log',
            sprintf("[%s] CMD: %s\nRetCode: %d\nOutput: %s\n\n", date('Y-m-d H:i:s'), $cmd, $retCode, implode("\n", $output)),
            FILE_APPEND
        );

        if (($retCode === 0 || $retCode === 3) && file_exists($outputPath) && filesize($outputPath) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Xử lý bằng FPDI (kết hợp tự động giải nén QPDF & Thu vào A4 dọc)
     */
    private function processWithFpdi(array $pages, string $outputPath): void
    {
        $pdf = new Fpdi();
        $tempRotatedFiles = [];

        try {
            foreach ($pages as $p) {
                $rawPath = $p['file_path'];
                // Tự động giải nén file qua QPDF nếu cần để đọc được 100% PDF v1.5+
                $filePath = $this->decompressPdfIfQpdfAvailable($rawPath);

                $pageNum = (int)$p['source_page_number'];
                $rotation = ((int)($p['rotation_degrees'] ?? 0)) % 360;
                if ($rotation < 0) $rotation += 360;

                $importFilePath = $filePath;
                $importPageNum = $pageNum;

                // Nếu có XOAY và CẦN ÉP VÀO A4 DỌC: Xoay trang nội dung vật lý trước qua QPDF
                if ($rotation > 0 && !empty($p['fit_portrait'])) {
                    $qpdfBin = $this->getQpdfBinary();
                    if ($qpdfBin) {
                        $tempRot = $this->tempDir . '/rot_' . bin2hex(random_bytes(8)) . '.pdf';
                        $cmd = sprintf(
                            '%s --warning-exit-0 --qdf --object-streams=disable --empty --rotate=+%d:1 --pages %s %d -- %s 2>&1',
                            $qpdfBin,
                            $rotation,
                            escapeshellarg($filePath),
                            $pageNum,
                            escapeshellarg($tempRot)
                        );
                        $out = [];
                        $code = 0;
                        exec($cmd, $out, $code);
                        if (file_exists($tempRot) && filesize($tempRot) > 0) {
                            $importFilePath = $tempRot;
                            $importPageNum = 1;
                            $tempRotatedFiles[] = $tempRot;
                        }
                    }
                }

                try {
                    $maxPages = $pdf->setSourceFile($importFilePath);
                } catch (\Throwable $e) {
                    if (stripos($e->getMessage(), 'compression technique') !== false) {
                        throw new RuntimeException("Tệp PDF này sử dụng nén luồng đối tượng (PDF v1.5+). Vui lòng cài đặt qpdf trên Ubuntu bằng lệnh: sudo apt install -y qpdf", 0, $e);
                    }
                    throw $e;
                }

                if ($importPageNum > $maxPages) {
                    continue;
                }

                $templateId = $pdf->importPage($importPageNum);
                $size = $pdf->getTemplateSize($templateId);

                // CÁCH 2: THU VÀO KHỔ DỌC A4 (FIT TO PORTRAIT)
                if (!empty($p['fit_portrait'])) {
                    // Tạo một trang A4 dọc tiêu chuẩn (210 x 297 mm)
                    $pdf->AddPage('P', 'A4');
                    $pageW = 210.0;
                    $pageH = 297.0;

                    // Lề an toàn mỗi bên 12mm
                    $margin = 12.0;
                    $availW = $pageW - ($margin * 2); // 186mm
                    $availH = $pageH - ($margin * 2); // 273mm

                    $srcW = $size['width'];
                    $srcH = $size['height'];

                    // Tỷ lệ thu nhỏ vừa vặn trang A4 dọc mà không méo hình
                    $scale = min($availW / $srcW, $availH / $srcH);
                    $targetW = $srcW * $scale;
                    $targetH = $srcH * $scale;

                    // Căn giữa trang dọc
                    $x = ($pageW - $targetW) / 2.0;
                    $y = ($pageH - $targetH) / 2.0;

                    $pdf->useTemplate($templateId, $x, $y, $targetW, $targetH);
                } else {
                    // Xử lý thông thường: Giữ nguyên khổ gốc và góc xoay
                    $orientation = $size['orientation'];
                    if ($rotation === 90 || $rotation === 270) {
                        $orientation = ($orientation === 'P') ? 'L' : 'P';
                        $pdf->AddPage($orientation, [$size['height'], $size['width']], $rotation);
                    } else {
                        $pdf->AddPage($orientation, [$size['width'], $size['height']], $rotation);
                    }

                    $pdf->useTemplate($templateId);
                }
            }

            $pdf->Output('F', $outputPath);
        } finally {
            // Dọn dẹp các tệp tạm đã xoay
            foreach ($tempRotatedFiles as $tmp) {
                if (file_exists($tmp)) {
                    @unlink($tmp);
                }
            }
        }
    }
}
