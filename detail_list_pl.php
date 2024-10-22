<?php
// detail_list_susut.php
include('./config/db.php');
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    die();
}

$login_user = $_SESSION['login_user']; // Get the logged-in user's name
$nodokumen = isset($_GET['nodokumen']) ? $conn->real_escape_string($_GET['nodokumen']) : '';

// Fetch detailed Susut data from the database, including pcode, qtyawal, qtybersih, satuan, keterangan, and dibuat_oleh
$sql = "SELECT * FROM susut WHERE nodokumen = '$nodokumen'";
$result = $conn->query($sql);

$susut_items = [];
$nama_barang_array = [];

// Fetch Nama Lengkap from Master Barang table based on PCode
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pcode = $row['pcode'];
        $sql_nama_barang = "SELECT namalengkap FROM masterbarang WHERE pcode = '$pcode'";
        $result_nama_barang = $conn->query($sql_nama_barang);
        $row_nama_barang = $result_nama_barang->fetch_assoc();
        $nama_barang_array[$pcode] = $row_nama_barang['namalengkap'];
        
        $susut_items[] = $row; // Store each item in the array
    }
}

$susut = isset($susut_items[0]) ? $susut_items[0] : null; // Use the first item for tanggal_susut, gudang, and dibuat_oleh

// Check if the document status is "Pending"
$canEdit = $susut && ($susut['status'] == 0) && ($login_user == 'windy' || $login_user == 'syahrul'); // status 0 indicates "Pending"

// Handle form submissions for editing or deleting items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_item'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $qtyawal = $conn->real_escape_string($_POST['qtyawal']);
        $qtybersih = $conn->real_escape_string($_POST['qtybersih']);
        $satuan = $conn->real_escape_string($_POST['satuan']);
        
        // Update the item in the database
        $update_sql = "UPDATE susut SET qtyawal='$qtyawal', qtybersih='$qtybersih', satuan='$satuan' WHERE id='$id'";
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('Item updated successfully.'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Error updating item.');</script>";
        }
    } elseif (isset($_POST['delete_item'])) {
        $id = $conn->real_escape_string($_POST['id']);
        
        // Delete the item from the database
        $delete_sql = "DELETE FROM susut WHERE id='$id'";
        if ($conn->query($delete_sql) === TRUE) {
            echo "<script>alert('Item deleted successfully.'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Error deleting item.');</script>";
        }
    }
}


