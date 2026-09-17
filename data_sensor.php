<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index_user.php');
    exit();
}

include 'db.php';
$sensor_data = $conn->query("SELECT * FROM sensor_data ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Data Sensor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Irrigation System</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index_admin.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="data_sensor.php">Data Sensor</a></li>
        <li class="nav-item"><a class="nav-link" href="control_pompa.php">Manual Pump Control</a></li>
        <li class="nav-item"><a class="nav-link" href="sensor_limits.php">Sensor Limits</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h1>Data Sensor</h1>
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>pH </th>
                <th>Temperature (Celcius°)</th>
                <th>Soil Moisture %(dalam persen) </th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $sensor_data->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['ph']; ?></td>
                <td><?php echo $row['temperature']; ?></td>
                <td><?php echo $row['soil_moisture']; ?></td>
                <td><?php echo $row['timestamp']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
