<?php
include('./config/db.php');
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    die();
}

// Handle search
$searchKeyword = '';
$startDate = '';
$endDate = '';

if (isset($_GET['search'])) {
    $searchKeyword = $_GET['search'];
}

if (isset($_GET['start_date']) && $_GET['start_date'] != '') {
    $startDate = $_GET['start_date'];
}

if (isset($_GET['end_date']) && $_GET['end_date'] != '') {
    $endDate = $_GET['end_date'];
}

// Pagination settings
$itemsPerPage = 19;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;

// SQL query to fetch data
$sql = "SELECT 
            th.NoDokumen,
            th.TglDokumen,
            th.PoNo,
            (SELECT Nama FROM supplier WHERE KdSupplier = th.KdSupplier LIMIT 1) as SupplierName,
            (SELECT Keterangan FROM gudang WHERE KdGudang = th.KdGudang LIMIT 1) as GudangName,
            td.PCode,
            mb.NamaLengkap AS NamaBarang,
            td.Qty,
            td.QtyPcs,
            td.Harga,
            td.Satuan,
            td.Jumlah,
            td.Total,
            td.EditUser,
            COUNT(*) OVER (PARTITION BY th.NoDokumen) as item_count 
        FROM 
            trans_terima_header th
        JOIN 
            trans_terima_detail td ON th.NoDokumen = td.NoDokumen
        LEFT JOIN 
            masterbarang mb ON td.PCode = mb.PCode
        WHERE th.NoDokumen LIKE '%$searchKeyword%'";

if ($startDate != '' && $endDate != '') {
    $sql .= " AND DATE(th.TglDokumen) BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate != '') {
    $sql .= " AND DATE(th.TglDokumen) >= '$startDate'";
} elseif ($endDate != '') {
    $sql .= " AND DATE(th.TglDokumen) <= '$endDate'";
}

$sql .= " ORDER BY th.NoDokumen, td.PCode 
         LIMIT $offset, $itemsPerPage";

$result = $conn->query($sql);

// Get the total number of rows for pagination
$totalSql = "SELECT COUNT(*) AS total 
            FROM trans_terima_header th
            JOIN trans_terima_detail td ON th.NoDokumen = td.NoDokumen
            WHERE th.NoDokumen LIKE '%$searchKeyword%'";

if ($startDate != '' && $endDate != '') {
    $totalSql .= " AND DATE(th.TglDokumen) BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate != '') {
    $totalSql .= " AND DATE(th.TglDokumen) >= '$startDate'";
} elseif ($endDate != '') {
    $totalSql .= " AND DATE(th.TglDokumen) <= '$endDate'";
}

$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<?php include './_partial/sidebar.php'; ?>

<div class="flex-1 p-10">
    <h1 class="text-3xl font-semibold mb-6">Data Konfirm Terima</h1>

    <!-- Search Form -->
    <form method="GET" class="mb-4">
        <div class="flex space-x-4">
            <input type="text" name="search" placeholder="Cari No Dokumen" value="<?php echo htmlspecialchars($searchKeyword); ?>" class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Cari</button>
        </div>
    </form>

    <div class="mb-4">
        <a href="export_terimabarang.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Export to Excel (Perbaiki masalah ini nanti)
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-md shadow border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b border-gray-300 text-center">No Dokumen</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Tanggal</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">PONo</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Supplier</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Gudang</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">PCode</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Nama Barang</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Qty</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">QtyPcs</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Harga</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Satuan</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Jumlah</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Total</th>
                    <th class="py-2 px-4 border-b border-gray-300 text-center">Edit User</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // $displayedDocNumbers = [];
                while ($row = $result->fetch_assoc()) {
                    if (!in_array($row['NoDokumen'], $displayedDocNumbers)) {
                        $displayedDocNumbers[] = $row['NoDokumen'];
                        echo '<tr>';
                        echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle" rowspan="' . $row['item_count'] . '">' . htmlspecialchars($row['NoDokumen']) . '</td>';
                        echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle" rowspan="' . $row['item_count'] . '">' . htmlspecialchars($row['TglDokumen']) . '</td>';
                        echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle" rowspan="' . $row['item_count'] . '">' . htmlspecialchars($row['PoNo']) . '</td>';
                        echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle" rowspan="' . $row['item_count'] . '">' . htmlspecialchars($row['SupplierName']) . '</td>';
                        echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle" rowspan="' . $row['item_count'] . '">' . htmlspecialchars($row['GudangName']) . '</td>';
                        $displayedDocNumbers[] = $row['NoDokumen'];
                    } else {
                        echo '<tr>';
                    }

                    // Display detail rows
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['PCode']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['NamaBarang']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['Qty']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['QtyPcs']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['Harga']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['Satuan']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['Jumlah']) . '</td>';
                    echo '<td class="py-2 px-4 border-b border-gray-300 text-center align-middle">' . htmlspecialchars($row['Total']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center">
        <?php if ($totalPages > 1): ?>
            <nav class="flex space-x-2">
                <!-- Previous Page Link -->
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Previous</a>
                <?php endif; ?>

                <!-- Next Page Link -->
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Next</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function updateDateRangeDisplay() {
    let startDate = document.getElementById('start_date').value;
    let endDate = document.getElementById('end_date').value;
    if (startDate && endDate) {
        document.querySelector('[name="search"]').value = startDate + ' s/d ' + endDate;
    }
}
</script>

</body>
</html>
