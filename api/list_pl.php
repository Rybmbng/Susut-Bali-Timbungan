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
$itemsPerPage = 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Modify SQL for search functionality
$sql = "SELECT nodokumen, COUNT(*) as item_count, status 
        FROM susut 
        WHERE nodokumen LIKE '%$searchKeyword%'";

if ($startDate != '' && $endDate != '') {
    $sql .= " AND DATE(tanggal_susut) BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate != '') {
    $sql .= " AND DATE(tanggal_susut) >= '$startDate'";
} elseif ($endDate != '') {
    $sql .= " AND DATE(tanggal_susut) <= '$endDate'";
}

$sql .= " GROUP BY nodokumen 
        LIMIT $offset, $itemsPerPage";

$result = $conn->query($sql);

// Get the total number of unique No Dokumen for pagination
$totalSql = "SELECT COUNT(DISTINCT nodokumen) AS total 
            FROM susut 
            WHERE nodokumen LIKE '%$searchKeyword%'";

if ($startDate != '' && $endDate != '') {
    $totalSql .= " AND DATE(tanggal_susut) BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate != '') {
    $totalSql .= " AND DATE(tanggal_susut) >= '$startDate'";
} elseif ($endDate != '') {
    $totalSql .= " AND DATE(tanggal_susut) <= '$endDate'";
}

$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<?php include './_partial/sidebar.php'; ?>
 <div class="flex-1 p-4">
    <h1 class="text-3xl font-semibold mb-6">Data Pengeluaran Lain</h1>
   <!-- Breadcrumb -->
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
                <span class="text-gray-500 text-sm font-medium align-middle">Data Pengeluaran Lain</span>
            </div>
        </li>
    </ol>
</nav>



<div class="mb-4 flex items-center"> 
    <form method="GET" action="list_pl.php" class="flex flex-wrap"> 
        <div class="mr-4">
            <input type="text" name="search" placeholder="Cari No Dokumen" value="<?php echo htmlspecialchars($searchKeyword); ?>" class="border border-gray-300 p-2 rounded">
        </div>
      <div class="mr-4 flex items-center">
    <label for="start_date" class="mr-2">Tanggal:</label>
    <input type="date" name="start_date" id="start_date" placeholder="Mulai" 
           value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>" 
           class="border border-gray-300 p-2">
    <span class="mx-2">s/d</span>
    <input type="date" name="end_date" id="end_date" placeholder="Akhir" 
           value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>" 
           class="border border-gray-300 p-2">
</div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Cari
        </button>
    </form>
</div>


    <div class="mb-4">
        <a href="export_excel.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Download Report PL
        </a>
    </div>

    <table class="min-w-full bg-white rounded-md shadow overflow-hidden">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b-2 text-center">No Dokumen</th>
                <th class="py-2 px-4 border-b-2 text-center">Tanggal</th>
                <th class="py-2 px-4 border-b-2 text-center">Gudang</th>
                <th class="py-2 px-4 border-b-2 text-center">Status</th> 
                <th class="py-2 px-4 border-b-2 text-center">Jumlah Item</th> 
                <th class="py-2 px-4 border-b-2 text-center"></th> 
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td class="py-2 px-4 border-b text-center align-middle"><?php echo $row['nodokumen']; ?></td> 
                <td class="py-2 px-4 border-b text-center align-middle">
                    <?php 
                    $tanggalSql = "SELECT tanggal_susut FROM susut WHERE nodokumen = '{$row['nodokumen']}' LIMIT 1"; 
                    $tanggalResult = $conn->query($tanggalSql);
                    $tanggalRow = $tanggalResult->fetch_assoc();
                    echo $tanggalRow['tanggal_susut']; 
                    ?>
                </td>
                <td class="py-2 px-4 border-b text-center align-middle">
                    <?php 
                    $gudangSql = "SELECT gudang FROM susut WHERE nodokumen = '{$row['nodokumen']}' LIMIT 1"; 
                    $gudangResult = $conn->query($gudangSql);
                    $gudangRow = $gudangResult->fetch_assoc();
                    echo $gudangRow['gudang']; 
                    ?>
                </td>
                <td class="py-2 px-4 border-b text-center align-middle">
                    <?php
                    $status = $row['status'];
                    if ($status == 1) {
                        echo "<span class='bg-green-200 text-green-800 py-1 px-2 rounded-full text-xs'>Open</span>";
                    } elseif ($status == 2) {
                        echo "<span class='bg-red-200 text-red-800 py-1 px-2 rounded-full text-xs'>Void</span>";
                    } else {
                        echo "<span class='bg-blue-200 text-blue-800 py-1 px-2 rounded-full text-xs'>Pending</span>";
                    }
                    ?>
                </td> 
                <td class="py-2 px-4 border-b text-center align-middle"><?php echo $row['item_count']; ?></td> 
                <td class="py-2 px-4 border-b text-center align-middle"> 
                    <a href="detail_list_pl.php?nodokumen=<?php echo $row['nodokumen']; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">
                        Detail
                    </a>
                </td> 
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="mt-6 flex justify-between">
    <a href="?page=<?php echo $page > 1 ? $page - 1 : 1; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Sebelumnya
    </a>
    <span class="self-center">Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $itemsPerPage, $totalItems); ?> dari <?php echo $totalItems; ?> data pada halaman <?php echo $page; ?> dari <?php echo $totalPages; ?></span>
    <a href="?page=<?php echo $page < $totalPages ? $page + 1 : $totalPages; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Selanjutnya
    </a>
</div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('flashMessage', () => ({
        show: false,
        type: '',
        title: '',
        text: '',

        init() {
            <?php if (isset($_SESSION['flash_message'])) : ?>
                this.show = true;
                this.type = '<?php echo $_SESSION['flash_message']['type']; ?>';
                this.title = '<?php echo $_SESSION['flash_message']['title']; ?>';
                this.text = '<?php echo $_SESSION['flash_message']['text']; ?>';

                // Clear the flash message from the session after displaying it
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>
        },

        close() {
            this.show = false;
        }
    }));
});
function updateDateRangeDisplay() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    const startDate = startDateInput.value;
    const endDate = endDateInput.value; 


    if (startDate && endDate) {
        const formattedStartDate = formatDate(startDate);
        const formattedEndDate = formatDate(endDate);
        startDateInput.value = formattedStartDate;
        endDateInput.value = formattedEndDate;
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2,  
 '0'); // Months are 0-indexed
    const year = date.getFullYear();
    return `${day}/${month}/${year}`; 

}

// Initial formatting on page load
updateDateRangeDisplay();
</script>

<div x-data="flashMessage" x-show="show" x-cloak>
    <div x-init="Swal.fire({
        icon: type,
        title: title,
        text: text,
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        close(); 
    })"></div>
</div>

</body>
</html>
