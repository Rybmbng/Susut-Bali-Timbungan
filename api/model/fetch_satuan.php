<?php
include('../config/db.php');

$pcode = $_GET['pcode'];

$result = $conn->query("SELECT SatuanSt FROM masterbarang WHERE PCode = '$pcode'");
$row = $result->fetch_assoc();

echo json_encode(['satuan' => $row['SatuanSt']]);
?>
