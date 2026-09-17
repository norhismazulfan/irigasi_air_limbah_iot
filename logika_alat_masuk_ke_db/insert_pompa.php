<?php
include '../db.php'; // koneksi db

date_default_timezone_set('Asia/Jakarta'); // WIB

$id = $_POST['id']; // id pompa (jika 1 pompa saja, isi 1 terus)
$pump_status = $_POST['pump_status']; // 1 = ON, 0 = OFF
$timestamp = date("Y-m-d H:i:s");

$sql = "INSERT INTO tb_pompa (id, pump_status, timestamp)
        VALUES ('$id', '$pump_status', '$timestamp')";

if(mysqli_query($conn, $sql)){
    echo "OK";
} else {
    echo "ERROR: " . mysqli_error($conn);
}
?>
