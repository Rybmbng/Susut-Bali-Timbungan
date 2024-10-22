<?php
include('./config/db.php'); 
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php"); 
    die();
}

// Fetch gudang options
$sql = "SELECT Keterangan FROM gudang";
$result_gudang = $conn->query($sql);

// Fetch all pcode options from masterbarang table
$sql_pcode = "SELECT PCode, NamaLengkap, SatuanSt FROM masterbarang"; 
$result_pcode = $conn->query($sql_pcode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    </head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal" x-data="{ sidebarOpen: false, susutMenuOpen: false }">
    

    <div class="flex">
        <?php include './_partial/sidebar.php'; ?> 

        <div class="flex-1 p-10">
            <h1 class="text-3xl font-semibold mb-6">Input Data</h1>
            <form action="./model/insert_susut.php" method="POST">
                <nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center"> 
        <li class="inline-flex items-center">
            <span class="text-gray-700 hover:text-blue-600 text-sm font-medium align-middle"> 
                Dashboard
            </span> 
        </li>
        <span class="mx-2 text-gray-500 align-middle">></span> 
        <li>
            <div class="flex items-center">
                <span class="mx-2 text-gray-500 align-middle">></span> 
                <span class="text-gray-700 hover:text-blue-600 text-sm font-medium align-middle">Pengeluaran</span> 
            </div>
        </li>
        <span class="mx-2 text-gray-500 align-middle">></span> 
        <li aria-current="page">
            <div class="flex items-center">
                <span class="mx-2 text-gray-500 align-middle">></span> 
                <span class="text-gray-500 text-sm font-medium align-middle">Input Pengeluaran Lain</span>
            </div>
        </li>
    </ol>
</nav>
        <div class="mb-4">
          <label for="tanggal_susut" class="block text-gray-700">Tanggal Pengeluaran:</label>
          <input type="date" name="tanggal_susut" id="tanggal_susut" class="border rounded w-full py-2 px-3" required max="<?php echo date('Y-m-d'); ?>">
          <p class="text-xs text-red-500 mt-1">Perhatikan! Format pengisian sesuai dd ( hari )- mm (bulan) -- yy (tahun). , Kalau bingung bisa klik tombol kalender disamping</p>
        </div>

                <div class="mb-4">
                    <label for="gudang" class="block text-gray-700">Gudang:</label>
                    <select name="gudang" id="gudang" class="border rounded w-full py-2 px-3" required>
                        <option value="">Pilih Gudang</option>
                        <?php
                        while ($row = $result_gudang->fetch_assoc()) {
                            echo "<option value='" . $row['Keterangan'] . "'>" . $row['Keterangan'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <table class="min-w-full bg-white border border-gray-300" id="itemsTable">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">PCode</th>
                            <th class="py-2 px-4 border-b">Qty Awal</th>
                            <th class="py-2 px-4 border-b">Qty Bersih</th>
                            <th class="py-2 px-4 border-b">Satuan</th> 
                            <th class="py-2 px-4 border-b">Keterangan</th>
                            <th class="py-2 px-4 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b flex">
                                <input type="text" class="searchPCode border rounded py-2 px-2 mr-2 w-1/4" placeholder="Search PCode" onkeyup="filterPCode(this)">
                                <select name="pcode[]" class="pcodeDropdown border rounded w-3/4 py-2 px-3" id="pcodeDropdown" required>
                                    <option value="">Select PCode</option>
                                    <?php
                                    while ($row = $result_pcode->fetch_assoc()) {
                                        echo "<option value='" . $row['PCode'] . "' data-satuan='" . $row['SatuanSt'] . "'>" . $row['PCode'] . " - " . $row['NamaLengkap'] . "</option>"; 
                                    }
                                    ?>
                                </select>
                            </td>
                        <td class="py-2 px-4 border-b">
                            <input type="number" name="qtyawal[]" step="0.01" class="border rounded w-full py-2 px-3" placeholder="0.00" required>
                        </td>
                        <td class="py-2 px-4 border-b">
                            <input type="number" name="qtybersih[]" step="0.01" class="border rounded w-full py-2 px-3" placeholder="0.00" required>
                        </td>
                        <td class="py-2 px-4 border-b"> 
                            <input type="text" name="satuan[]" class="border rounded w-full py-2 px-3" readonly> 
                        </td>
                        <td class="py-2 px-4 border-b">
                            <input type="text" name="keterangan[]" class="border rounded w-full py-2 px-3" required>
                        </td>
                        <td class="py-2 px-4 border-b">
                            <button type="button" class="bg-red-500 text-white px-3 py-1 rounded removeRow">Remove</button>
                        </td>

                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <button type="button" id="addRow" class="bg-green-500 text-white px-4 py-2 rounded">Tambahkan Item Lain</button>
                </div>

                <div class="mt-6">
                    <input type="submit" value="Submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                </div>
            </form>
        </div>
    </div>
<script>
    document.getElementById('addRow').addEventListener('click', function () {
        let table = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
        let newRow = table.rows[0].cloneNode(true);

        // Reset input values
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelector('.pcodeDropdown').selectedIndex = 0; 

        // Reset searchPCode input field
        newRow.querySelector('.searchPCode').value = '';

        // Attach change event listener to the new dropdown
        attachPCodeDropdownListener(newRow.querySelector('.pcodeDropdown'));

        table.appendChild(newRow);
    });

    document.querySelector('tbody').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            let row = e.target.closest('tr');
            if (document.querySelectorAll('tbody tr').length > 1) {
                row.remove();
            } else {
                alert('You need to have at least one row.');
            }
        }
    });

    function filterPCode(input) {
        var row = input.closest('tr'); 
        var filter = input.value.toUpperCase();
        var dropdown = row.querySelector('.pcodeDropdown'); 
        var options = dropdown.getElementsByTagName('option');

        for (var i = 1; i < options.length; i++) { 
            var txtValue = options[i].textContent || options[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
    }

    // Attach change event listener to existing dropdowns on page load
    document.querySelectorAll('.pcodeDropdown').forEach(dropdown => {
        attachPCodeDropdownListener(dropdown);
    });

    function attachPCodeDropdownListener(dropdown) {
        var row = dropdown.closest('tr');
        var satuanInput = row.querySelector('input[name="satuan[]"]');

        dropdown.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var satuan = selectedOption.dataset.satuan; 
            satuanInput.value = satuan; 
        });
    }

    // Membatasi Tanggal Input
    document.getElementById('tanggal_susut').addEventListener('change', function () {
        let selectedDate = new Date(this.value);
        let today = new Date();

        if (selectedDate > today) {
            alert('Tanggal tidak boleh melebihi tanggal hari ini.');
            this.value = ''; // Reset the input value
        }
    });
</script>



</body>
</html>
