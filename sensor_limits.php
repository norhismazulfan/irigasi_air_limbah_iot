<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: index_user.php');
    exit();
}

include 'db.php'; // Make sure the database connection is correct

// Fetch current sensor limits from the database
$query = "SELECT * FROM sensor_limits WHERE id = 1"; // Assuming a single record for the limits
$limit_data = $conn->query($query)->fetch_assoc();

// Update the limits when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ph_limit'])) {
        $ph_limit = $_POST['ph_limit'];
        $update_query = "UPDATE sensor_limits SET ph_limit = ? WHERE id = 1";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("d", $ph_limit);
        $stmt->execute();
        $message = "pH limit updated successfully!";
    }
    if (isset($_POST['moisture_limit'])) {
        $moisture_limit = $_POST['moisture_limit'];
        $update_query = "UPDATE sensor_limits SET moisture_limit = ? WHERE id = 1";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("d", $moisture_limit);
        $stmt->execute();
        $message = "Moisture limit updated successfully!";
    }
    if (isset($_POST['temperature_limit'])) {
        $temperature_limit = $_POST['temperature_limit'];
        $update_query = "UPDATE sensor_limits SET temperature_limit = ? WHERE id = 1";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("d", $temperature_limit);
        $stmt->execute();
        $message = "Temperature limit updated successfully!";
    }

    // Fetch updated limits
    $limit_data = $conn->query($query)->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Limits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .navbar {
            margin-bottom: 20px;
        }

        .icon-edit {
            cursor: pointer;
            color: #007bff;
            font-size: 1.5rem;
            margin-top: 10px;
        }

        .icon-edit:hover {
            color: #0056b3;
        }

        .card-header {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .card-body {
            text-align: center;
            font-size: 1.5rem;
            padding: 30px;
        }

        .icon-text {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .modal-header {
            background-color: #f0f0f0;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-body {
            padding: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
            border-radius: 5px;
            font-size: 1rem;
        }

        .row {
            margin-top: 30px;
        }

        .card-footer {
            text-align: center;
            font-size: 1rem;
            padding: 10px;
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
          <a class="nav-link" href="control_pompa.php">Manual Pump Control</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="sensor_limits.php">Sensor Limits</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="text-center">Sensor Limit Configuration</h2>

    <!-- Success Message -->
    <?php if (isset($message)) : ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Current Sensor Limits 3x1 Layout -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Minimum pH Limit
                </div>
                <div class="card-body">
                    <p class="icon-text"><?php echo $limit_data['ph_limit']; ?></p>
                </div>
                <div class="card-footer">
                    <i class="fas fa-pencil-alt icon-edit" data-bs-toggle="modal" data-bs-target="#phModal" title="Ubah batas"></i>
                    <span>Ubah Batas</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Minimum Soil Moisture Limit
                </div>
                <div class="card-body">
                    <p class="icon-text"><?php echo $limit_data['moisture_limit']; ?>%</p>
                </div>
                <div class="card-footer">
                    <i class="fas fa-pencil-alt icon-edit" data-bs-toggle="modal" data-bs-target="#moistureModal" title="Ubah batas"></i>
                    <span>Ubah Batas</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Minimum Temperature Limit
                </div>
                <div class="card-body">
                    <p class="icon-text"><?php echo $limit_data['temperature_limit']; ?>°C</p>
                </div>
                <div class="card-footer">
                    <i class="fas fa-pencil-alt icon-edit" data-bs-toggle="modal" data-bs-target="#temperatureModal" title="Ubah batas"></i>
                    <span>Ubah Batas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for Editing Sensor Limits -->

    <!-- pH Modal -->
    <div class="modal fade" id="phModal" tabindex="-1" aria-labelledby="phModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="phModalLabel">Edit Minimum pH Limit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="sensor_limits.php">
                        <div class="mb-3">
                            <label for="ph_limit" class="form-label">Minimum pH Limit</label>
                            <input type="number" class="form-control" id="ph_limit" name="ph_limit" value="<?php echo $limit_data['ph_limit']; ?>" step="0.1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Moisture Modal -->
    <div class="modal fade" id="moistureModal" tabindex="-1" aria-labelledby="moistureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moistureModalLabel">Edit Minimum Soil Moisture Limit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="sensor_limits.php">
                        <div class="mb-3">
                            <label for="moisture_limit" class="form-label">Minimum Soil Moisture Limit (%)</label>
                            <input type="number" class="form-control" id="moisture_limit" name="moisture_limit" value="<?php echo $limit_data['moisture_limit']; ?>" step="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Temperature Modal -->
    <div class="modal fade" id="temperatureModal" tabindex="-1" aria-labelledby="temperatureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="temperatureModalLabel">Edit Minimum Temperature Limit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="sensor_limits.php">
                        <div class="mb-3">
                            <label for="temperature_limit" class="form-label">Minimum Temperature Limit (°C)</label>
                            <input type="number" class="form-control" id="temperature_limit" name="temperature_limit" value="<?php echo $limit_data['temperature_limit']; ?>" step="0.1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
