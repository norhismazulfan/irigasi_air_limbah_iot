<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index_user.php');
    exit();
}

include 'db.php';

// Fetch data for charts
$query = "SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 10";
$sensor_data = $conn->query($query);

$ph_values = [];
$temperature_values = [];
$moisture_values = [];
$labels = [];

while ($sensor = $sensor_data->fetch_assoc()) {
    $ph_values[] = $sensor['ph'];
    $temperature_values[] = $sensor['temperature'];
    $moisture_values[] = $sensor['soil_moisture'];
    $labels[] = $sensor['timestamp'];
}

// Fetch the latest sensor data
$latest_sensor = $conn->query("SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 1")->fetch_assoc();

// Fetch current mode status
$mode_row = $conn->query("SELECT mode_control FROM tb_control WHERE id=1")->fetch_assoc();
$current_mode = ($mode_row) ? $mode_row['mode_control'] : 0;

// Handle toggle button
if (isset($_POST['toggle_mode'])) {
    $new_mode = ($current_mode == 0) ? 1 : 0;

    // Update mode_control
    $conn->query("UPDATE tb_control SET mode_control='$new_mode' WHERE id=1");

    // Jika mode jadi Automatic, langsung matikan pompa
    if ($new_mode == 1) {
        $stmt = $conn->prepare("INSERT INTO tb_pompa (pump_status, timestamp) VALUES (0, NOW())");
        $stmt->execute();
    }

    header("Location: index_admin.php");
    exit();
}

// Ambil 5 riwayat pompa terakhir
$riwayat_pompa = $conn->query("SELECT * FROM tb_pompa ORDER BY timestamp DESC LIMIT 5");

// Ambil semua riwayat untuk modal
$semua_riwayat_pompa = $conn->query("SELECT * FROM tb_pompa ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Irrigation System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    .card { margin-top: 20px; transition: transform 0.3s ease-in-out; }
    .card:hover { transform: scale(1.05); }
    .navbar { margin-bottom: 20px; }
    .chart-grid { display: flex; justify-content: space-between; }
    .chart-item { flex: 1; margin-right: 10px; }
    .chart-item:last-child { margin-right: 0; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Irrigation System</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="index_admin.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="data_sensor.php">Data Sensor</a></li>
        <li class="nav-item"><a class="nav-link" href="control_pompa.php">Manual Pump Control</a></li>
        <li class="nav-item"><a class="nav-link" href="sensor_limits.php">Sensor Limits</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <!-- Latest Sensor Data -->
    <div class="row">
        <div class="col-md-4"><div class="card"><div class="card-header">Latest pH Level</div><div class="card-body"><h3><?php echo $latest_sensor['ph']; ?></h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-header">Latest Temperature (°C)</div><div class="card-body"><h3><?php echo $latest_sensor['temperature']; ?>°</h3></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-header">Latest Soil Moisture (%)</div><div class="card-body"><h3><?php echo $latest_sensor['soil_moisture']; ?>%</h3></div></div></div>
    </div>

    <!-- Charts -->
    <div class="row chart-grid mt-4">
        <div class="chart-item">
            <div class="card"><div class="card-header">pH Level Over Time</div><div class="card-body"><canvas id="phChart"></canvas></div></div>
        </div>
        <div class="chart-item">
            <div class="card"><div class="card-header">Temperature Over Time</div><div class="card-body"><canvas id="temperatureChart"></canvas></div></div>
        </div>
        <div class="chart-item">
            <div class="card"><div class="card-header">Soil Moisture Over Time</div><div class="card-body"><canvas id="soilMoistureChart"></canvas></div></div>
        </div>
    </div>

    <!-- Toggle Mode Button -->
    <div class="row mt-4">
        <div class="col text-center">
            <form method="post">
                <button type="submit" name="toggle_mode" class="btn <?php echo ($current_mode == 0) ? 'btn-primary' : 'btn-warning'; ?>">
                    <?php echo ($current_mode == 0) ? 'Switch to Automatic Mode' : 'Switch to Manual Mode'; ?>
                </button>
            </form>
            <p class="mt-2">Current Mode: <?php echo ($current_mode == 0) ? '<span class="text-warning fw-bold">Manual</span>' : '<span class="text-primary fw-bold">Automatic</span>'; ?></p>
        </div>
    </div>

    <!-- Riwayat Pompa -->
    <div class="card mt-4">
        <div class="card-header fw-bold">
            <i class="fas fa-history"></i> 5 Riwayat Status Pompa Terakhir
        </div>
        <div class="list-group list-group-flush">
            <?php while($row = $riwayat_pompa->fetch_assoc()): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold">
                            <?php
                                if ($row['pump_status'] == 1) {
                                    echo 'Pompa Menyala';
                                } else {
                                    echo 'Pompa Mati';
                                }
                            ?>
                        </span>
                        <br>
                        <small class="text-muted"><?php echo date("d M Y, H:i:s", strtotime($row['timestamp'])); ?></small>
                    </div>
                    <?php if ($row['pump_status'] == 1): ?>
                        <span class="badge bg-success">ON</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">OFF</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="card-footer text-center">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalRiwayatPompa">
                Lihat Semua Riwayat
            </button>
        </div>
    </div>
</div>

<!-- Modal Riwayat Pompa -->
<div class="modal fade" id="modalRiwayatPompa" tabindex="-1" aria-labelledby="modalRiwayatPompaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRiwayatPompaLabel">Semua Riwayat Status Pompa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height:400px; overflow-y:auto;">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no=1;
                while($row = $semua_riwayat_pompa->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td>
                        <?php if ($row['pump_status'] == 1): ?>
                            <span class="badge bg-success">Pompa Menyala (ON)</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Pompa Mati (OFF)</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date("d M Y, H:i:s", strtotime($row['timestamp'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Chart scripts -->
<script>
var phCtx = document.getElementById('phChart').getContext('2d');
new Chart(phCtx, { type: 'line', data: { labels: <?php echo json_encode($labels); ?>, datasets: [{ label: 'pH', data: <?php echo json_encode($ph_values); ?>, borderColor: 'rgba(75, 192, 192, 1)', backgroundColor: 'rgba(75, 192, 192, 0.2)', fill: true, tension: 0.4 }] }, options: { responsive: true, scales: { y: { beginAtZero: true } } } });

var tempCtx = document.getElementById('temperatureChart').getContext('2d');
new Chart(tempCtx, { type: 'line', data: { labels: <?php echo json_encode($labels); ?>, datasets: [{ label: 'Temp (°C)', data: <?php echo json_encode($temperature_values); ?>, borderColor: 'rgba(255, 99, 132, 1)', backgroundColor: 'rgba(255, 99, 132, 0.2)', fill: true, tension: 0.4 }] }, options: { responsive: true, scales: { y: { beginAtZero: true } } } });

var moistureCtx = document.getElementById('soilMoistureChart').getContext('2d');
new Chart(moistureCtx, { type: 'line', data: { labels: <?php echo json_encode($labels); ?>, datasets: [{ label: 'Soil Moisture (%)', data: <?php echo json_encode($moisture_values); ?>, borderColor: 'rgba(153, 102, 255, 1)', backgroundColor: 'rgba(153, 102, 255, 0.2)', fill: true, tension: 0.4 }] }, options: { responsive: true, scales: { y: { beginAtZero: true } } } });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
