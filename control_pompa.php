<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index_user.php');
    exit();
}

include 'db.php';

// Fetch mode_control value terlebih dahulu
$mode_row = $conn->query("SELECT mode_control FROM tb_control WHERE id=1")->fetch_assoc();
$current_mode = ($mode_row) ? $mode_row['mode_control'] : 0; // default manual

// Fetch the latest pump status
$query = "SELECT pump_status FROM tb_pompa ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($query);
$pump_status = ($result->num_rows > 0) ? $result->fetch_assoc()['pump_status'] : 0;

// Handle pump control actions
if (isset($_POST['action'])) {
    // Only allow manual control if mode is manual
    if ($current_mode == 0) {
        $status = ($_POST['action'] === 'toggle') ? ($_POST['current_status'] == 1 ? 0 : 1) : 0;
        // Insert a new row to tb_pompa with updated status
        $stmt = $conn->prepare("INSERT INTO tb_pompa (pump_status, timestamp) VALUES (?, NOW())");
        $stmt->bind_param("i", $status);
        $stmt->execute();

        // Update $pump_status variable for immediate feedback
        $pump_status = $status;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manual Pump Control</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    .container {
        margin-top: 30px;
    }
    .toggle-btn {
        width: 200px;
        height: 60px;
        font-size: 1.5rem;
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Irrigation System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index_admin.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="data_sensor.php">Data Sensor</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="control_pompa.php">Manual Pump Control</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sensor_limits.php">Sensor Limits</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container text-center">
    <h1 class="mb-4">Manual Pump Control</h1>

    <div class="mb-3">
        <h5>Control Mode: 
            <?php echo ($current_mode == 1) ? '<span class="badge bg-primary">Automatic</span>' : '<span class="badge bg-secondary">Manual</span>'; ?>
        </h5>
    </div>

    <form method="post">
        <input type="hidden" name="current_status" value="<?php echo $pump_status; ?>">
        <button name="action" value="toggle"
            class="btn toggle-btn <?php echo ($pump_status == 1) ? 'btn-danger' : 'btn-success'; ?>"
            <?php echo ($current_mode == 1) ? 'disabled' : ''; ?>>
            <?php echo ($pump_status == 1) ? '<i class="fas fa-power-off"></i> Turn OFF' : '<i class="fas fa-play"></i> Turn ON'; ?>
        </button>
    </form>

    <?php if ($current_mode == 1): ?>
        <div class="alert alert-warning mt-3">Manual control disabled. Switch to <b>Manual</b> mode first.</div>
    <?php endif; ?>

    <div class="mt-4">
        <h3>Current Pump Status:</h3>
        <?php if ($pump_status == 1): ?>
            <p class="text-success fw-bold">ON</p>
        <?php else: ?>
            <p class="text-danger fw-bold">OFF</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
