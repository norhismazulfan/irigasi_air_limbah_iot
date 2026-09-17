<?php
include 'db.php'; // Pastikan koneksi ke database sudah benar

// Ambil data sensor terbaru dari database
$query = "SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 1";
$sensor = $conn->query($query)->fetch_assoc();

// Kirim data dalam format JSON
echo json_encode([
    'ph' => $sensor['ph'],
    'temperature' => $sensor['temperature'],
    'soil_moisture' => $sensor['soil_moisture'],
    'pump_status' => $sensor['pump_status'],
    'timestamp' => $sensor['timestamp'] // Mengambil timestamp untuk label grafik
]);
?>
