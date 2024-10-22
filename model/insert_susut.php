<?php
include('../config/db.php');
session_start();

// Function to generate nodokumen
function generateNoDokumen($conn, $tanggal_susut) {
    // Extract year and month from tanggal_susut
    $tanggal_susut_date = new DateTime($tanggal_susut);
    $currentYear = $tanggal_susut_date->format('y'); 
    $currentMonth = $tanggal_susut_date->format('m'); 

    // Get the last number from the table for the specific month and year
    $sql = "SELECT nodokumen FROM susut 
            WHERE nodokumen LIKE 'PL_____-$currentMonth-$currentYear' 
            ORDER BY nodokumen DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    
    if ($result->num_rows > 0) {
        $lastRow = $result->fetch_assoc();
        $lastNoDokumen = $lastRow['nodokumen'];
        $lastNumber = intval(substr($lastNoDokumen, 2, 5)); // Extracting xxxx part
        $newNumber = $lastNumber + 1; // Increment the last number
    } else {
        $newNumber = 1; // Start from 1 if no rows exist for this month and year
    }

    $formattedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    $noDokumen = "PL" . $formattedNumber . "-" . $currentMonth . "-" . $currentYear;

    return $noDokumen;
}

// Get POST data with checks and default values
$tanggal_susut = isset($_POST['tanggal_susut']) ? $_POST['tanggal_susut'] : '';
$gudang = isset($_POST['gudang']) ? $_POST['gudang'] : '';
$pcode = isset($_POST['pcode']) ? $_POST['pcode'] : []; 
$qtyawal = isset($_POST['qtyawal']) ? $_POST['qtyawal'] : []; 
$qtybersih = isset($_POST['qtybersih']) ? $_POST['qtybersih'] : []; 
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : [];

// Generate nodokumen dengan menyertakan tanggal_susut
$nodokumen = generateNoDokumen($conn, $tanggal_susut);

// Dapatkan username dari session atau set default jika tidak ada
$username = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 'User'; 

// Check if pcode data exists before proceeding
if (!empty($pcode)) {
    // Begin transaction
    $conn->begin_transaction();

    try {
        // Prepare statement for insertion
        $stmt = $conn->prepare("INSERT INTO susut (nodokumen, tanggal_susut, gudang, pcode, qtyawal, qtybersih, satuan, keterangan, status, dibuat_oleh) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Iterate through data and insert
        for ($i = 0; $i < count($pcode); $i++) {
            $pc = $pcode[$i];
            $qty_a = $qtyawal[$i]; 
            $qty_b = $qtybersih[$i]; 
            $ket = $keterangan[$i];

            // Fetch satuan for the pcode
            $result = $conn->query("SELECT SatuanSt FROM masterbarang WHERE PCode='$pc'");
            if (!$result) {
                throw new Exception("Query failed: " . $conn->error);
            }
            $row = $result->fetch_assoc();
            $satuan = $row['SatuanSt'];

            // Bind parameters and execute
            $stmt->bind_param('ssssddssss', $nodokumen, $tanggal_susut, $gudang, $pc, $qty_a, $qty_b, $satuan, $ket, $status, $username); 
            $status = '1'; // Status 'Open'
            if (!$stmt->execute()) {
                throw new Exception("Error: " . $stmt->error);
            }
        }

        // Commit transaction
        $conn->commit();

        // Set a flash message in the session
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil ditambahkan!'
        ];

        header("location: ../list_pl.php"); 
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();

        // Set a flash message in the session
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'title' => 'Oops...',
            'text' => 'Gagal menambahkan data: ' . addslashes($e->getMessage())
        ];

        header("location: ../list_pl.php"); 
        exit(); 
    }
} else {
    // Handle case where no pcode data is submitted
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'title' => 'Oops...',
        'text' => 'Tidak ada data pcode yang dikirimkan.'
    ];

    header("location: ../list_pl.php"); 
    exit();
}

// Close connection
$conn->close();
?>