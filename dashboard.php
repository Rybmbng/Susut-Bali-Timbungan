<?php
// dashboard.php
include('./config/db.php');
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    die();
}

// Query untuk mengambil data pengeluaran terbanyak
$sql_top_items = "SELECT s.pcode, mb.namalengkap, SUM(s.qtybersih) as total_qty_bersih
                    FROM susut s
                    JOIN masterbarang mb ON s.pcode = mb.PCode
                    GROUP BY s.pcode, mb.namalengkap
                    ORDER BY total_qty_bersih DESC
                    LIMIT 5"; 
$result_top_items = $conn->query($sql_top_items);


if ($result_top_items->num_rows > 0) {
    while ($row = $result_top_items->fetch_assoc()) {
        $top_items_labels[] = $row['namalengkap']; // Gunakan nama lengkap untuk label grafik
        $top_items_data[] = $row['total_qty_bersih'];
    }
} 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include './_partial/sidebar.php'; ?>

<div class="flex-1 p-10">
    <h1 class="text-3xl font-semibold mb-6">Welcome to the Admin Dashboard</h1>

    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center"> 
            <li class="inline-flex items-center">
                <span class="text-gray-700 hover:text-blue-600 text-sm font-medium align-middle"> 
                    Dashboard
                </span> 
            </li>
        </ol>
    </nav>

    <div class="flex justify-center"> 
        <canvas id="chart-container" width="400" height="400"></canvas> 
    </div>

</div>
<?php 
if (isset($_SESSION['login_user'])) {
    $username = $_SESSION['login_user'];
    // Update last_active di database
    $sql_update = "UPDATE user SET last_active = NOW() WHERE username = '$username'";
    $conn->query($sql_update);

    // Query untuk menghitung jumlah user online
    $sql_online_user = "SELECT COUNT(*) as online_user FROM user WHERE last_active > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
    $result_online_user = $conn->query($sql_online_user);
    $online_user_count = 0;

    if ($result_online_user->num_rows > 0) {
        $row = $result_online_user->fetch_assoc();
        $online_user_count = $row['online_user'];
    }
}
?>
<p class="text-white text-center mb-4">User Online: <?php echo $online_user_count; ?></p>
<script>
// Data untuk grafik (dari PHP)
const data = {
    labels: <?php echo json_encode($top_items_labels); ?>,
    datasets: [{
        label: 'Total Qty Susut',
        data: <?php echo json_encode($top_items_data); ?>,
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
    }]
};

// Konfigurasi grafik
const config = {
    type: 'pie', 
    data: data,
    options: {
        responsive: true, 
        maintainAspectRatio: false 
    }
};

// Membuat grafik
const myChart = new Chart(
    document.getElementById('chart-container'),
    config
);
</script>

</body>
</html>