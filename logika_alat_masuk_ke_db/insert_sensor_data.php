<?php
include '../db.php'; // koneksi db
date_default_timezone_set('Asia/Jakarta');

$ph = $_POST['ph'];
$temperature = $_POST['temperature'];
$soil_moisture = $_POST['soil_moisture'];
$timestamp = date("Y-m-d H:i:s");

// Jika ingin filter input, tambahkan di sini

$sql = "INSERT INTO sensor_data (ph, temperature, soil_moisture, timestamp)
        VALUES ('$ph', '$temperature', '$soil_moisture', '$timestamp')";

if(mysqli_query($conn, $sql)){
    echo "OK";
} else {
    echo "ERROR: " . mysqli_error($conn);
}
?>
