<?php
// export_excel.php
include './config/db.php';
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

// Periksa apakah sesi login user tersedia
if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    exit(); // Pastikan script berhenti setelah redirect
}

try {
    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the spreadsheet headers
    $sheet->setCellValue('A1', 'No Dokumen')
          ->setCellValue('B1', 'Tanggal')
          ->setCellValue('C1', 'KdSupplier')
          ->setCellValue('D1', 'PCode')
          ->setCellValue('E1', 'Nama Barang')
          ->setCellValue('F1', 'Qty')
          ->setCellValue('G1', 'QtyPcs')
          ->setCellValue('H1', 'Harga')
          ->setCellValue('I1', 'Satuan')
          ->setCellValue('J1', 'Jumlah')
          ->setCellValue('K1', 'Total')
          ->setCellValue('L1', 'Edit User');

    // Prepare SQL query to fetch all trans_terima_header data
    $sql = "SELECT 
                th.NoDokumen,
                th.TglDokumen,
                th.KdSupplier,
                td.PCode,
                mb.NamaLengkap AS NamaBarang,
                td.Qty,
                td.QtyPcs,
                td.Harga,
                td.Satuan,
                td.Jumlah,
                td.Total,
                td.EditUser
            FROM 
                trans_terima_header th
            JOIN 
                trans_terima_detail td ON th.NoDokumen = td.NoDokumen
            LEFT JOIN 
                masterbarang mb ON td.PCode = mb.PCode
            ORDER BY 
                th.NoDokumen, td.PCode";

    // Execute SQL query with error handling
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }

    // Populate the spreadsheet with data
    $rowNumber = 2;

    while ($row = $result->fetch_assoc()) {
        // Populate the spreadsheet
        $sheet->setCellValue('A' . $rowNumber, $row['NoDokumen'])
              ->setCellValue('B' . $rowNumber, $row['TglDokumen'])
              ->setCellValue('C' . $rowNumber, $row['KdSupplier'])
              ->setCellValue('D' . $rowNumber, $row['PCode'])
              ->setCellValue('E' . $rowNumber, $row['NamaBarang'])
              ->setCellValue('F' . $rowNumber, $row['Qty'])
              ->setCellValue('G' . $rowNumber, $row['QtyPcs'])
              ->setCellValue('H' . $rowNumber, $row['Harga'])
              ->setCellValue('I' . $rowNumber, $row['Satuan'])
              ->setCellValue('J' . $rowNumber, $row['Jumlah'])
              ->setCellValue('K' . $rowNumber, $row['Total'])
              ->setCellValue('L' . $rowNumber, $row['EditUser']);

        $rowNumber++;
    }

    // Auto size columns
    foreach(range('A', 'L') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Set the filename and output the file
    $filename = "Data_Trans_Terima_" . date('YmdHis') . ".xlsx";

    // Ensure there's no output before the headers
    if (ob_get_length()) {
        ob_end_clean(); // Hanya jalankan ob_end_clean() jika ada buffer
    }

    // Set correct headers 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);

    // Output file to browser
    $writer->save('php://output');

} catch (Exception $e) {
    // Tangkap exception dan cetak pesan error
    error_log("Error saat membuat file Excel: " . $e->getMessage());
    echo "Terjadi kesalahan saat membuat file Excel. Silakan hubungi administrator.";
}

exit;
?>
