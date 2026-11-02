<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use Exception;
use RuntimeException;
use setasign\Fpdi\Fpdi;

class DocumentService
{
    private string $storageDir;
    private int $maxFileSize;

    public function __construct()
    {
        $appConfig = require ROOT_PATH . '/config/app.php';
        $this->storageDir = $appConfig['paths']['sources'];
        $this->maxFileSize = $appConfig['max_file_size'];

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
    }

    /**
     * Xử lý tải lên một tệp PDF
     */
    public function handleUpload(int $userId, array $file): array
    {
        // 1. Kiểm tra lỗi upload của PHP
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'filename' => $file['name'] ?? 'Không xác định',
                'error' => $this->getUploadErrorMessage($file['error']),
            ];
        }

        $origName = basename($file['name']);
        $tmpPath = $file['tmp_name'];
        $fileSize = (int)$file['size'];

        // 2. Kiểm tra tệp rỗng (0 bytes)
        if ($fileSize <= 0 || filesize($tmpPath) === 0) {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => 'Tệp PDF rỗng (dung lượng 0 bytes).',
            ];
        }

        // 3. Kiểm tra dung lượng tối đa
        if ($fileSize > $this->maxFileSize) {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => sprintf('Dung lượng tệp (%s) vượt quá giới hạn tối đa (%s).', format_bytes($fileSize), format_bytes($this->maxFileSize)),
            ];
        }

        // 4. Kiểm tra phần mở rộng
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => 'Định dạng tệp không được hỗ trợ. Chỉ chấp nhận tệp có đuôi .pdf.',
            ];
        }

        // 5. Kiểm tra MIME type thực tế bằng finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if ($mime !== 'application/pdf' && $mime !== 'application/x-pdf') {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => "Nội dung tệp không phải định dạng PDF hợp lệ (MIME type: {$mime}).",
            ];
        }

        // 6. Kiểm tra khả năng đọc, tính toàn vẹn và đếm số trang
        $pageAnalysis = $this->analyzePdf($tmpPath);
        if (!$pageAnalysis['valid']) {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => $pageAnalysis['error'],
            ];
        }

        $totalPages = $pageAnalysis['pages'];
        if ($totalPages <= 0) {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => 'Tệp PDF không chứa trang nào hoặc cấu trúc trang bị hỏng.',
            ];
        }

        // 7. Tạo tên lưu trữ an toàn (UUID / Hash ngẫu nhiên) để tránh Directory Traversal
        $storedFilename = 'doc_' . bin2hex(random_bytes(16)) . '.pdf';
        $destPath = $this->storageDir . '/' . $storedFilename;

        $moved = is_uploaded_file($tmpPath)
            ? move_uploaded_file($tmpPath, $destPath)
            : (copy($tmpPath, $destPath) && @unlink($tmpPath));

        if (!$moved) {
            return [
                'success' => false,
                'filename' => $origName,
                'error' => 'Không thể lưu tệp vào thư mục lưu trữ hệ thống.',
            ];
        }

        // 8. Lưu thông tin vào database
        try {
            $docId = Document::create(
                $userId,
                $origName,
                $storedFilename,
                $destPath,
                $fileSize,
                $totalPages,
                'application/pdf'
            );

            // Nạp thông tin từng trang vào source_pages
            for ($page = 1; $page <= $totalPages; $page++) {
                Document::createSourcePage($docId, $page, null, null, 'portrait');
            }

            return [
                'success' => true,
                'id' => $docId,
                'filename' => $origName,
                'stored_filename' => $storedFilename,
                'size' => $fileSize,
                'pages' => $totalPages,
            ];
        } catch (Exception $e) {
            // Xóa file nếu lỗi database
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            return [
                'success' => false,
                'filename' => $origName,
                'error' => 'Lỗi lưu thông tin vào cơ sở dữ liệu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Phân tích, kiểm tra mật khẩu, tệp hỏng và đếm số trang PDF
     */
    public function analyzePdf(string $filePath): array
    {
        // 1. Kiểm tra header chữ ký %PDF-
        $header = @file_get_contents($filePath, false, null, 0, 1024);
        if ($header === false || strlen($header) < 8 || !str_starts_with($header, '%PDF-')) {
            return [
                'valid' => false,
                'error' => 'Tệp không có chữ ký tiêu đề PDF hợp lệ (%PDF-).',
            ];
        }

        // Kiểm tra xem có mã hóa mật khẩu không
        $fullContent = @file_get_contents($filePath);
        if ($fullContent !== false && stripos($fullContent, '/Encrypt') !== false) {
            return [
                'valid' => false,
                'error' => 'Tệp PDF có mã hóa bảo vệ (Password-protected).',
            ];
        }

        // 2. Thử đếm số trang chuẩn xác qua FPDI trước (chuẩn chuẩn nhất của PDF)
        try {
            $fpdi = new Fpdi();
            $pageCount = (int)$fpdi->setSourceFile($filePath);
            if ($pageCount > 0) {
                return [
                    'valid' => true,
                    'pages' => $pageCount,
                ];
            }
        } catch (\Throwable $e) {
            // Nếu lỗi do nén v1.5+, tiếp tục thử qpdf bên dưới
        }

        // 3. Sử dụng qpdf nếu có trên Linux (lấy số trang ở dòng số hợp lệ cuối cùng)
        $qpdfPath = exec('which qpdf');
        if ($qpdfPath && function_exists('exec')) {
            $cmd = sprintf('qpdf --show-npages %s 2>&1', escapeshellarg($filePath));
            $output = [];
            $retCode = 0;
            exec($cmd, $output, $retCode);

            for ($k = count($output) - 1; $k >= 0; $k--) {
                $val = trim($output[$k]);
                if ($val !== '' && is_numeric($val) && (int)$val > 0) {
                    return [
                        'valid' => true,
                        'pages' => (int)$val,
                    ];
                }
            }

            $outStr = implode("\n", $output);
            if (stripos($outStr, 'password') !== false || stripos($outStr, 'encrypted') !== false) {
                return [
                    'valid' => false,
                    'error' => 'Tệp PDF có mật khẩu bảo vệ. Vui lòng gỡ mật khẩu trước khi tải lên.',
                ];
            }
        }

        // 4. Dự phòng bằng Regex đếm /Count hoặc /Type /Page
        if ($fullContent !== false) {
            if (preg_match('/\/Count\s+(\d+)/', $fullContent, $matches)) {
                return [
                    'valid' => true,
                    'pages' => (int)$matches[1],
                ];
            }
            preg_match_all("/\/Type\s*\/Page(?![a-zA-Z])/", $fullContent, $matches);
            $count = count($matches[0]);
            if ($count > 0) {
                return [
                    'valid' => true,
                    'pages' => $count,
                ];
            }
        }

        return [
            'valid' => false,
            'error' => 'Không thể xác định số trang hoặc cấu trúc tệp PDF bị lỗi.',
        ];
    }

    private function getUploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'Tệp vượt quá cấu hình upload_max_filesize trong php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'Tệp vượt quá MAX_FILE_SIZE cho phép của biểu mẫu.',
            UPLOAD_ERR_PARTIAL => 'Tệp chỉ mới được tải lên một phần.',
            UPLOAD_ERR_NO_FILE => 'Không có tệp nào được chọn.',
            UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm trên server.',
            UPLOAD_ERR_CANT_WRITE => 'Không thể ghi tệp vào đĩa cứng server.',
            UPLOAD_ERR_EXTENSION => 'Quá trình upload bị chặn bởi một extension của PHP.',
            default => 'Lỗi tải lên không xác định.',
        };
    }
}
