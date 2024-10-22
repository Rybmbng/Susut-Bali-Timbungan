<?php
include('./config/db.php');
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    die();
}

$login_user = $_SESSION['login_user']; // Get the logged-in user's name
$nodokumen = isset($_GET['nodokumen']) ? $conn->real_escape_string($_GET['nodokumen']) : '';

// Fetch existing items from the database
$susut_items = [];
$nama_barang_array = [];

// Fetch data for the given nodokumen
$sql = "SELECT * FROM susut WHERE nodokumen = '$nodokumen'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sql_nama_barang = "SELECT NamaLengkap, SatuanSt FROM masterbarang WHERE PCode = '$pcode'"; 
        $result_nama_barang = $conn->query($sql_nama_barang);
        $row_nama_barang = $result_nama_barang->fetch_assoc();
        $row['NamaLengkap'] = $row_nama_barang['NamaLengkap'];
        $row['SatuanSt'] = $row_nama_barang['SatuanSt']; 
        $susut_items[] = $row;
}
}

// Handle form submissions for adding new items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_item'])) {
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
    } elseif (isset($_POST['add_item'])) {
        $pcode = $conn->real_escape_string($_POST['pcode']);
        $qtyawal = $conn->real_escape_string($_POST['qtyawal']);
        $qtybersih = $conn->real_escape_string($_POST['qtybersih']);
        $satuan = $conn->real_escape_string($_POST['satuan']);
        $keterangan = $conn->real_escape_string($_POST['keterangan']);
        
        // Insert the new item into the database
        $insert_sql = "INSERT INTO susut (nodokumen, pcode, qtyawal, qtybersih, satuan, keterangan)
                       VALUES ('$nodokumen', '$pcode', '$qtyawal', '$qtybersih', '$satuan', '$keterangan')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "<script>alert('Item added successfully.'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Error adding item.');</script>";
        }
    }
}

// Fetch PCode and NamaBarang for dropdown
$pcode_sql = "SELECT PCode, NamaLengkap FROM masterbarang";
$pcode_result = $conn->query($pcode_sql);

$pcode_options = [];
if ($pcode_result->num_rows > 0) {
    while ($row = $pcode_result->fetch_assoc()) {
        $pcode_options[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
    <script>
        function searchPCode() {
            var input = document.getElementById('pcode_search').value.toLowerCase();
            var options = document.getElementById('pcode_dropdown').options;
            for (var i = 0; i < options.length; i++) {
                var option = options[i];
                if (option.text.toLowerCase().indexOf(input) !== -1) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        }
    </script>
</head>
<body>

<div class="popup-form">
    <h1>Add/Edit Item</h1>

    <form method="POST" action="">
        <div class="form-group">
            <label for="pcode">PCode:</label>
            <input type="text" id="pcode_search" onkeyup="searchPCode()" placeholder="Search PCode" class="form-control">
            <select id="pcode_dropdown" name="pcode" class="form-control">
                <?php foreach ($pcode_options as $option) { ?>
                    <option value="<?php echo $option['PCode']; ?>">
                        <?php echo $option['PCode'] . ' - ' . $option['NamaLengkap']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="qtyawal">Qty Awal:</label>
            <input type="number" id="qtyawal" name="qtyawal" step="0.01" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="qtybersih">Qty Bersih:</label>
            <input type="number" id="qtybersih" name="qtybersih" step="0.01" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="satuan">Satuan:</label>
            <input type="text" id="satuan" name="satuan" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan:</label>
            <textarea id="keterangan" name="keterangan" class="form-control"></textarea>
        </div>

        <input type="hidden" name="id" value="">
        <input type="hidden" name="nodokumen" value="<?php echo $nodokumen; ?>">

        <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
        <button type="submit" name="update_item" class="btn btn-warning">Update Item</button>
    </form>

    <button onclick="window.close()" class="btn btn-secondary">Cancel</button>
</div>

</body>
</html>
