<?php
// export_excel.php
include('./config/db.php');
require 'vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    die();
}

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the spreadsheet headers
$sheet->setCellValue('A1', 'No Dokumen')
      ->setCellValue('B1', 'Tanggal')
      ->setCellValue('C1', 'Gudang')
      ->setCellValue('D1', 'PCode')
      ->setCellValue('E1', 'Nama Barang')
      ->setCellValue('F1', 'Qty Awal')
      ->setCellValue('G1', 'Qty Bersih')
      ->setCellValue('H1', 'Satuan')
      ->setCellValue('I1', 'Keterangan')
      ->setCellValue('J1', 'Status')
      ->setCellValue('K1', 'Dibuat Oleh');

// Fetch all Susut data from the database
$sql = "SELECT s.*, mb.namalengkap 
        FROM susut s 
        LEFT JOIN masterbarang mb ON s.pcode = mb.pcode 
        ORDER BY s.nodokumen, s.pcode";
$result = $conn->query($sql);

// Populate the spreadsheet with data
$rowNumber = 2;
$prevDocNumber = '';

while ($row = $result->fetch_assoc()) {
    if ($row['nodokumen'] !== $prevDocNumber) {
        $sheet->setCellValue('A' . $rowNumber, $row['nodokumen'])
              ->setCellValue('B' . $rowNumber, $row['tanggal_susut'])
              ->setCellValue('C' . $rowNumber, $row['gudang']);
        $prevDocNumber = $row['nodokumen'];
    }

    // Determine the status text based on the status value
    $statusText = '';
    switch ($row['status']) {
        case '1':
            $statusText = 'Open';
            break;
        case '2':
            $statusText = 'Void';
            break;
        case '0':
        default:
            $statusText = 'Pending';
            break;
    }

    $sheet->setCellValue('D' . $rowNumber, $row['pcode'])
          ->setCellValue('E' . $rowNumber, $row['namalengkap'])
          ->setCellValue('F' . $rowNumber, $row['qtyawal'])
          ->setCellValue('G' . $rowNumber, $row['qtybersih'])
          ->setCellValue('H' . $rowNumber, $row['satuan'])
          ->setCellValue('I' . $rowNumber, $row['keterangan'])
          ->setCellValue('J' . $rowNumber, $statusText)
          ->setCellValue('K' . $rowNumber, $row['dibuat_oleh']);

    $rowNumber++;
}

// Auto size columns
foreach(range('A', 'K') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Set the filename and output the file
$filename = "Data_Pengeluaran_Lain_" . date('YmdHis') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
?>
