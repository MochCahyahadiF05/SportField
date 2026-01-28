<?php
require_once '../../config/config.php';
require_once '../../config/Auth.php';

// Set timezone ke Indonesia
date_default_timezone_set('Asia/Jakarta');

// Check authentication
if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
    echo "Unauthorized";
    exit();
}

// Include FPDF library
require_once '../../config/fpdf186/fpdf.php';

// Database connection
global $pdo;
require_once '../../config/db.php';

// Get date range from GET parameters
$tanggal_dari = $_GET['tanggal_dari'] ?? date('Y-m-01');
$tanggal_sampai = $_GET['tanggal_sampai'] ?? date('Y-m-d');
$tipe_laporan = $_GET['tipe_laporan'] ?? 'harian';

// Validate dates
if (!strtotime($tanggal_dari) || !strtotime($tanggal_sampai)) {
    $tanggal_dari = date('Y-m-01');
    $tanggal_sampai = date('Y-m-d');
}

// Ensure dari date is not after sampai date
if (strtotime($tanggal_dari) > strtotime($tanggal_sampai)) {
    $temp = $tanggal_dari;
    $tanggal_dari = $tanggal_sampai;
    $tanggal_sampai = $temp;
}

// Create PDF object
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Title
$pdf->Cell(0, 10, 'LAPORAN PENDAPATAN SPORTFIELD', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Periode: ' . date('d M Y', strtotime($tanggal_dari)) . ' - ' . date('d M Y', strtotime($tanggal_sampai)), 0, 1, 'C');
$pdf->Cell(0, 5, 'Generated: ' . date('d M Y H:i'), 0, 1, 'C');
$pdf->Ln(5);

// Get statistics for date range (only completed and confirmed)
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM booking WHERE status IN ('confirmed', 'completed') AND DATE(created_at) >= '$tanggal_dari' AND DATE(created_at) <= '$tanggal_sampai'")->fetch()['total'];
$total_bookings = $pdo->query("SELECT COUNT(*) as total FROM booking WHERE status IN ('confirmed', 'completed') AND DATE(created_at) >= '$tanggal_dari' AND DATE(created_at) <= '$tanggal_sampai'")->fetch()['total'];
$avg_revenue = $total_bookings > 0 ? $total_revenue / $total_bookings : 0;

// Summary Section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'RINGKASAN', 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(80, 6, 'Total Pendapatan', 0, 0, 'L', true);
$pdf->Cell(0, 6, 'Rp ' . number_format($total_revenue, 0, ',', '.'), 0, 1, 'R', true);

$pdf->SetFillColor(220, 255, 220);
$pdf->Cell(80, 6, 'Total Booking', 0, 0, 'L', true);
$pdf->Cell(0, 6, number_format($total_bookings), 0, 1, 'R', true);

$pdf->SetFillColor(255, 240, 200);
$pdf->Cell(80, 6, 'Rata-rata per Booking', 0, 0, 'L', true);
$pdf->Cell(0, 6, 'Rp ' . number_format($avg_revenue, 0, ',', '.'), 0, 1, 'R', true);

$pdf->Ln(5);

// Conditional rendering based on report type
if ($tipe_laporan === 'harian') {
    // Detail Harian Report Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'DETAIL PENDAPATAN HARIAN', 0, 1);

    // Table Header untuk Detail Harian
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(41, 128, 185);
    $pdf->SetTextColor(255, 255, 255);

    $pdf->Cell(25, 6, 'Tgl Booking', 1, 0, 'C', true);
    $pdf->Cell(25, 6, 'Tgl Dibuat', 1, 0, 'C', true);
    $pdf->Cell(30, 6, 'Lapangan', 1, 0, 'L', true);
    $pdf->Cell(20, 6, 'Jam Mulai', 1, 0, 'C', true);
    $pdf->Cell(20, 6, 'Jam Selesai', 1, 0, 'C', true);
    $pdf->Cell(30, 6, 'Total Harga', 1, 1, 'R', true);

    // Table Data Detail
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 0);

    // Get detailed daily report
    $detailed_query = "
        SELECT 
            b.tanggal,
            b.created_at,
            DATE_FORMAT(b.tanggal, '%d/%m/%y') as tanggal_format,
            DATE_FORMAT(b.created_at, '%d/%m/%y') as created_format,
            l.nama as lapangan,
            b.jam_mulai,
            b.jam_selesai,
            b.total_harga
        FROM booking b
        JOIN lapangan l ON b.lapangan_id = l.id
        WHERE b.status IN ('confirmed', 'completed')
        AND DATE(b.tanggal) >= '$tanggal_dari' 
        AND DATE(b.tanggal) <= '$tanggal_sampai'
        ORDER BY b.tanggal DESC
    ";

    $stmt = $pdo->query($detailed_query);
    $detailed_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fill = false;
    $daily_subtotal = 0;
    $last_date = null;

    if (empty($detailed_data)) {
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(0, 6, 'Tidak ada data detail', 1, 1, 'C', true);
    } else {
        foreach ($detailed_data as $row) {
            // Jika tanggal berubah, tampilkan subtotal hari sebelumnya
            if ($last_date && $last_date != $row['tanggal']) {
                if ($fill) {
                    $pdf->SetFillColor(200, 200, 200);
                } else {
                    $pdf->SetFillColor(220, 220, 220);
                }
                $pdf->Cell(115, 6, 'SUBTOTAL HARIAN', 1, 0, 'R', true);
                $pdf->Cell(35, 6, 'Rp ' . number_format($daily_subtotal, 0, ',', '.'), 1, 1, 'R', true);
                $daily_subtotal = 0;
                $fill = !$fill;
            }
            
            if ($fill) {
                $pdf->SetFillColor(240, 240, 240);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $lapangan_display = substr($row['lapangan'], 0, 12);
            $pdf->Cell(25, 6, $row['tanggal_format'], 1, 0, 'C', true);
            $pdf->Cell(25, 6, $row['created_format'], 1, 0, 'C', true);
            $pdf->Cell(30, 6, $lapangan_display, 1, 0, 'L', true);
            $pdf->Cell(20, 6, $row['jam_mulai'], 1, 0, 'C', true);
            $pdf->Cell(20, 6, $row['jam_selesai'], 1, 0, 'C', true);
            $pdf->Cell(30, 6, 'Rp ' . number_format($row['total_harga'], 0, ',', '.'), 1, 1, 'R', true);
            
            $daily_subtotal += $row['total_harga'];
            $last_date = $row['tanggal'];
        }
        
        // Tampilkan subtotal hari terakhir
        if ($last_date) {
            if ($fill) {
                $pdf->SetFillColor(200, 200, 200);
            } else {
                $pdf->SetFillColor(220, 220, 220);
            }
            $pdf->Cell(115, 6, 'SUBTOTAL HARIAN', 1, 0, 'R', true);
            $pdf->Cell(35, 6, 'Rp ' . number_format($daily_subtotal, 0, ',', '.'), 1, 1, 'R', true);
        }
    }
}

$pdf->Ln(8);

// Monthly Report Section - ONLY untuk laporan bulanan
if ($tipe_laporan === 'bulanan') {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'LAPORAN PENDAPATAN BULANAN', 0, 1);

    // Table Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(100, 100, 100);
    $pdf->SetTextColor(255, 255, 255);

    $pdf->Cell(45, 7, 'Bulan', 1, 0, 'C', true);
    $pdf->Cell(35, 7, 'Booking', 1, 0, 'C', true);
    $pdf->Cell(50, 7, 'Pendapatan', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Rata-rata', 1, 1, 'C', true);

    // Table Data
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);

    $monthly_report_query = "
        SELECT 
            DATE_FORMAT(tanggal, '%Y-%m') as bulan,
            DATE_FORMAT(tanggal, '%M %Y') as bulan_format,
            COUNT(*) as jumlah_booking,
            SUM(total_harga) as pendapatan
        FROM booking 
        WHERE status IN ('confirmed', 'completed')
        AND DATE(tanggal) >= '$tanggal_dari' 
        AND DATE(tanggal) <= '$tanggal_sampai'
        GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
        ORDER BY bulan DESC
    ";

    $stmt = $pdo->query($monthly_report_query);
    $monthly_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fill = false;
    if (empty($monthly_report)) {
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(0, 6, 'Tidak ada data laporan', 1, 1, 'C', true);
    } else {
        foreach ($monthly_report as $report) {
            $avg = $report['jumlah_booking'] > 0 ? $report['pendapatan'] / $report['jumlah_booking'] : 0;
            
            if ($fill) {
                $pdf->SetFillColor(240, 240, 240);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $fill = !$fill;
            
            $pdf->Cell(45, 6, $report['bulan_format'], 1, 0, 'L', true);
            $pdf->Cell(35, 6, number_format($report['jumlah_booking']), 1, 0, 'C', true);
            $pdf->Cell(50, 6, 'Rp ' . number_format($report['pendapatan'], 0, ',', '.'), 1, 0, 'R', true);
            $pdf->Cell(40, 6, 'Rp ' . number_format($avg, 0, ',', '.'), 1, 1, 'R', true);
        }
    }
}

// Footer
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, 'SportField Admin Report - Generated on ' . date('d M Y H:i'), 0, 1, 'C');

// Output PDF
$pdf->Output('D', 'Laporan_Pendapatan_' . date('d-m-Y') . '.pdf');
?>
