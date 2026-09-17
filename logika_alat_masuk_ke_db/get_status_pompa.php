<?php
include '../db.php'; // koneksi db

$result = $conn->query("SELECT pump_status FROM tb_pompa ORDER BY id DESC LIMIT 1");
if ($result) {
    $row = $result->fetch_assoc();
    echo $row['pump_status'];
} else {
    echo "error";
}
?>
