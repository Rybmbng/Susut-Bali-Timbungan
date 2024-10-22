<?php
// db.php
$servername = "222.165.237.166";
$username = "sgv";
$password = "Secr3t@2016";
$dbname = "db_balitimbungan";
$port = 33661;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
