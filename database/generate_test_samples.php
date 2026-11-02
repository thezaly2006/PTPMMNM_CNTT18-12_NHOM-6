<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (!class_exists('Fpdf\Fpdf') && class_exists('FPDF')) {
    class_alias('FPDF', 'Fpdf\Fpdf');
}

use Fpdf\Fpdf;

$sampleDir = dirname(__DIR__) . '/storage/test_samples';
if (!is_dir($sampleDir)) {
    mkdir($sampleDir, 0777, true);
}

echo "=== KHỞI TẠO BỘ DỮ LIỆU 6 FILE PDF KIỂM THỬ MẪU ===\n";

// 1. Tệp A: 10 trang dọc (Portrait - A4)
$pdfA = new Fpdf('P', 'mm', 'A4');
for ($i = 1; $i <= 10; $i++) {
    $pdfA->AddPage('P');
    $pdfA->SetFont('Arial', 'B', 24);
    $pdfA->SetTextColor(79, 70, 229); // Màu Indigo
    $pdfA->Rect(10, 10, 190, 277);
    $pdfA->SetXY(20, 40);
    $pdfA->Cell(170, 20, "TEP A - TRANG {$i} / 10", 0, 1, 'C');
    $pdfA->SetFont('Arial', '', 14);
    $pdfA->SetTextColor(100, 116, 139);
    $pdfA->SetXY(20, 70);
    $pdfA->MultiCell(170, 10, "Day la noi dung kiem thu cua Tep A (Trang doc Portrait).\nNhan xac dinh: [NHAN_A_{$i}]\nKich thuoc: A4 (210 x 297 mm)", 0, 'C');
}
$pathA = $sampleDir . '/sample_A_portrait.pdf';
$pdfA->Output('F', $pathA);
echo "[+] Da tao Tep A (10 trang doc): {$pathA}\n";

// 2. Tệp B: 10 trang ngang (Landscape - A4)
$pdfB = new Fpdf('L', 'mm', 'A4');
for ($i = 1; $i <= 10; $i++) {
    $pdfB->AddPage('L');
    $pdfB->SetFont('Arial', 'B', 24);
    $pdfB->SetTextColor(16, 185, 129); // Màu Emerald
    $pdfB->Rect(10, 10, 277, 190);
    $pdfB->SetXY(20, 40);
    $pdfB->Cell(257, 20, "TEP B - TRANG {$i} / 10", 0, 1, 'C');
    $pdfB->SetFont('Arial', '', 14);
    $pdfB->SetTextColor(100, 116, 139);
    $pdfB->SetXY(20, 70);
    $pdfB->MultiCell(257, 10, "Day la noi dung kiem thu cua Tep B (Trang ngang Landscape).\nNhan xac dinh: [NHAN_B_{$i}]\nKich thuoc: A4 Landscape (297 x 210 mm)", 0, 'C');
}
$pathB = $sampleDir . '/sample_B_landscape.pdf';
$pdfB->Output('F', $pathB);
echo "[+] Da tao Tep B (10 trang ngang): {$pathB}\n";

// 3. Tệp C: 10 trang kich thuoc hon hop (A4, Letter, Legal, xen ke doc/ngang)
$pdfC = new Fpdf();
for ($i = 1; $i <= 10; $i++) {
    $orientation = ($i % 2 === 0) ? 'L' : 'P';
    $size = ($i % 3 === 0) ? 'Letter' : 'A4';
    $pdfC->AddPage($orientation, $size);
    $pdfC->SetFont('Arial', 'B', 22);
    $pdfC->SetTextColor(245, 158, 11); // Màu Amber
    $pdfC->SetXY(15, 35);
    $pdfC->Cell(0, 15, "TEP C (HON HOP) - TRANG {$i} / 10", 0, 1, 'C');
    $pdfC->SetFont('Arial', '', 13);
    $pdfC->SetTextColor(71, 85, 105);
    $pdfC->SetXY(15, 60);
    $pdfC->MultiCell(0, 10, "Kieu trang: {$orientation} | Kho giay: {$size}\nNhan xac dinh: [NHAN_C_{$i}]", 0, 'C');
}
$pathC = $sampleDir . '/sample_C_mixed.pdf';
$pdfC->Output('F', $pathC);
echo "[+] Da tao Tep C (10 trang hon hop): {$pathC}\n";

// 4. Tệp D: 10 trang tieu chuan
$pdfD = new Fpdf('P', 'mm', 'A4');
for ($i = 1; $i <= 10; $i++) {
    $pdfD->AddPage('P');
    $pdfD->SetFont('Arial', 'B', 24);
    $pdfD->SetTextColor(14, 165, 233); // Màu Sky
    $pdfD->Rect(10, 10, 190, 277);
    $pdfD->SetXY(20, 40);
    $pdfD->Cell(170, 20, "TEP D - TRANG {$i} / 10", 0, 1, 'C');
    $pdfD->SetFont('Arial', '', 14);
    $pdfD->SetTextColor(100, 116, 139);
    $pdfD->SetXY(20, 70);
    $pdfD->MultiCell(170, 10, "Noi dung Tep D tieu chuan.\nNhan xac dinh: [NHAN_D_{$i}]", 0, 'C');
}
$pathD = $sampleDir . '/sample_D_short.pdf';
$pdfD->Output('F', $pathD);
echo "[+] Da tao Tep D (10 trang): {$pathD}\n";

// 5. Tệp E: Tệp rỗng (0 bytes)
$pathE = $sampleDir . '/sample_E_empty.pdf';
file_put_contents($pathE, '');
echo "[+] Da tao Tep E (Tep rong 0 bytes): {$pathE}\n";

// 6. Tệp F: Tệp hỏng (Corrupted PDF)
$pathF = $sampleDir . '/sample_F_corrupt.pdf';
file_put_contents($pathF, "THIS_IS_NOT_A_VALID_PDF_HEADER_AND_CORRUPTED_STREAM_CONTENT_ERR_100");
echo "[+] Da tao Tep F (Tep hong corrupt): {$pathF}\n";

echo "=== HOÀN TẤT TẠO 6 TỆP MẪU KIỂM THỬ (TỔNG 40 TRANG CHUẨN + 2 TỆP ĐẶC BIỆT) ===\n";
