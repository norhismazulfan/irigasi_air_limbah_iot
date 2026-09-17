<?php
include '../db.php'; // koneksi db

$q = mysqli_query($conn, "SELECT moisture_limit, temperature_limit FROM sensor_limits WHERE id=1");
$data = mysqli_fetch_assoc($q);
echo json_encode($data);
?>
