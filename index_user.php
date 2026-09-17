<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header('Location: index_admin.php');
    exit();
}

include 'db.php';

// Fetch latest sensor data
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

// Fetch latest single sensor for card display
$latest_sensor = $conn->query("SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 1")->fetch_assoc();

// Fetch current control mode
$mode_row = $conn->query("SELECT mode_control FROM tb_control WHERE id=1")->fetch_assoc();
$current_mode = ($mode_row) ? $mode_row['mode_control'] : 0;

// Ambil 5 riwayat pompa terakhir
$riwayat_pompa = $conn->query("SELECT * FROM tb_pompa ORDER BY timestamp DESC LIMIT 5");

// Ambil semua riwayat untuk modal
$semua_riwayat_pompa = $conn->query("SELECT * FROM tb_pompa ORDER BY timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User Dashboard - Irrigation System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .card { margin-top: 20px; transition: transform 0.3s ease-in-out; }
    .card:hover { transform: scale(1.05); }
    .navbar { margin-bottom: 20px; }
    .card-header { font-weight: bold; font-size: 1.2rem; }
    .welcome-text { font-size: 1.5rem; margin-top: 15px; }
    .chart-grid { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .chart-item { flex: 1 1 30%; min-width: 280px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Irrigation System</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="#">User Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="text-center text-primary welcome-text">Welcome, User!</h2>

    <!-- Latest Sensor Data Cards -->
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light text-center">
                    <i class="fas fa-tint"></i> Latest pH Level
                </div>
                <div class="card-body text-center">
                    <h3><?php echo htmlspecialchars($latest_sensor['ph']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light text-center">
                    <i class="fas fa-thermometer-half"></i> Latest Temperature (°C)
                </div>
                <div class="card-body text-center">
                    <h3><?php echo htmlspecialchars($latest_sensor['temperature']); ?>°</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light text-center">
                    <i class="fas fa-seedling"></i> Latest Soil Moisture (%)
                </div>
                <div class="card-body text-center">
                    <h3><?php echo htmlspecialchars($latest_sensor['soil_moisture']); ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row chart-grid mt-4">
        <div class="chart-item">
            <div class="card">
                <div class="card-header">pH Level Over Time</div>
                <div class="card-body">
                    <canvas id="phChart"></canvas>
                </div>
            </div>
        </div>
        <div class="chart-item">
            <div class="card">
                <div class="card-header">Temperature Over Time</div>
                <div class="card-body">
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>
        </div>
        <div class="chart-item">
            <div class="card">
                <div class="card-header">Soil Moisture Over Time</div>
                <div class="card-body">
                    <canvas id="soilMoistureChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pompa -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">
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
new Chart(phCtx, { 
    type: 'line', 
    data: { 
        labels: <?php echo json_encode($labels); ?>, 
        datasets: [{ 
            label: 'pH', 
            data: <?php echo json_encode($ph_values); ?>, 
            borderColor: 'rgba(75, 192, 192, 1)', 
            backgroundColor: 'rgba(75, 192, 192, 0.2)', 
            fill: true, 
            tension: 0.4 
        }] 
    }, 
    options: { responsive: true, scales: { y: { beginAtZero: true } } } 
});

var tempCtx = document.getElementById('temperatureChart').getContext('2d');
new Chart(tempCtx, { 
    type: 'line', 
    data: { 
        labels: <?php echo json_encode($labels); ?>, 
        datasets: [{ 
            label: 'Temperature (°C)', 
            data: <?php echo json_encode($temperature_values); ?>, 
            borderColor: 'rgba(255, 99, 132, 1)', 
            backgroundColor: 'rgba(255, 99, 132, 0.2)', 
            fill: true, 
            tension: 0.4 
        }] 
    }, 
    options: { responsive: true, scales: { y: { beginAtZero: true } } } 
});

var moistureCtx = document.getElementById('soilMoistureChart').getContext('2d');
new Chart(moistureCtx, { 
    type: 'line', 
    data: { 
        labels: <?php echo json_encode($labels); ?>, 
        datasets: [{ 
            label: 'Soil Moisture (%)', 
            data: <?php echo json_encode($moisture_values); ?>, 
            borderColor: 'rgba(153, 102, 255, 1)', 
            backgroundColor: 'rgba(153, 102, 255, 0.2)', 
            fill: true, 
            tension: 0.4 
        }] 
    }, 
    options: { responsive: true, scales: { y: { beginAtZero: true } } } 
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
