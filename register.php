<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username       = trim($_POST['username']);
    $password       = trim($_POST['password']);
    $confirm_pw     = trim($_POST['confirm_password']);

    // Validasi sederhana
    if (empty($username) || empty($password) || empty($confirm_pw)) {
        $error = "Semua kolom wajib diisi.";
    } elseif ($password !== $confirm_pw) {
        $error = "Password dan Konfirmasi Password tidak cocok.";
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            // Masukkan user baru (role = 'user')
            $stmt_insert = $conn->prepare("
                INSERT INTO users (username, password, role) 
                VALUES (?, ?, 'user')
            ");
            $stmt_insert->bind_param("ss", $username, $password);
            if ($stmt_insert->execute()) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>Login di sini</a>.";
            } else {
                $error = "Terjadi masalah saat menyimpan ke database. Coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register – Irrigation System</title>

  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
  />

  <!-- Font Awesome (untuk ikon) -->
  <link 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" 
    rel="stylesheet" 
  />

  <style>
    body {
      background: #f0f2f5;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    /* Hanya satu ruleset untuk .register-card:
       - Memulai dalam keadaan tersembunyi (opacity: 0 + translateY(20px))
       - Sekaligus menjalankan animasi fadeInUp ke opacity:1 + translateY(0)
    */
    .register-card {
      width: 100%;
      max-width: 480px;
      border: none;
      border-radius: 0.75rem;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);

      /* Initial hidden + start position */
      opacity: 0;
      transform: translateY(20px);

      /* Jalankan animasi fadeInUp selama 0.6 detik */
      animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Keyframes untuk fadeInUp */
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .register-card .card-header {
      background-color: #007bff;
      border-bottom: none;
      color: #fff;
      font-weight: 600;
      text-align: center;
      border-top-left-radius: 0.75rem;
      border-top-right-radius: 0.75rem;
      padding: 1rem;
    }

    .register-card .card-body {
      padding: 2rem;
    }

    .register-card .input-group-text {
      background-color: #e9ecef;
      border: none;
      border-radius: 0.375rem 0 0 0.375rem;
      color: #495057;
    }

    .register-card .form-control:focus {
      box-shadow: none;
      border-color: #007bff;
    }

    .register-card .btn-register {
      background-color: #007bff;
      border: none;
      box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }

    .register-card .btn-register:hover {
      background-color: #0056b3;
      box-shadow: 0 3px 10px rgba(0, 86, 179, 0.4);
    }

    .register-card .card-footer {
      background-color: #f8f9fa;
      border-top: none;
      border-bottom-left-radius: 0.75rem;
      border-bottom-right-radius: 0.75rem;
      padding: 0.75rem;
      text-align: center;
      font-size: 0.9rem;
    }

    .toggle-password {
      cursor: pointer;
      color: #6c757d;
    }
  </style>
</head>
<body>

  <div class="card register-card shadow-sm">
    <div class="card-header">
      <i class="fas fa-user-plus fa-lg me-2"></i>Register New Account
    </div>
    <div class="card-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 d-flex align-items-center" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>
          <div><?php echo $error; ?></div>
        </div>
      <?php elseif (!empty($success)): ?>
        <div class="alert alert-success py-2 d-flex align-items-center" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          <div><?php echo $success; ?></div>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <!-- Username -->
        <div class="mb-3">
          <label for="username" class="form-label text-secondary">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input
              type="text"
              id="username"
              name="username"
              class="form-control"
              placeholder="Masukkan username"
              required
            />
          </div>
        </div>

        <!-- Password -->
        <div class="mb-3 position-relative">
          <label for="password" class="form-label text-secondary">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Masukkan password"
              required
            />
            <span class="input-group-text toggle-password" id="togglePassword">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4 position-relative">
          <label for="confirm_password" class="form-label text-secondary">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input
              type="password"
              id="confirm_password"
              name="confirm_password"
              class="form-control"
              placeholder="Ulangi password"
              required
            />
            <span class="input-group-text toggle-password" id="toggleConfirm">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-register btn-lg text-white">
            <i class="fas fa-user-check me-1"></i> Register
          </button>
        </div>

        <!-- Link to Login -->
        <div class="text-center text-muted small mb-0">
          Sudah punya akun? <a href="login.php" class="link-primary">Login di sini</a>
        </div>
      </form>
    </div>
    <div class="card-footer">
      &copy; <?php echo date('Y'); ?> Irrigation System
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Show/Hide Password Script -->
  <script>
    // Toggle first password field
    document.getElementById('togglePassword').addEventListener('click', function () {
      const pwField = document.getElementById('password');
      const type = pwField.getAttribute('type') === 'password' ? 'text' : 'password';
      pwField.setAttribute('type', type);
      this.querySelector('i').classList.toggle('fa-eye');
      this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Toggle second (confirm) password field
    document.getElementById('toggleConfirm').addEventListener('click', function () {
      const confirmField = document.getElementById('confirm_password');
      const type = confirmField.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmField.setAttribute('type', type);
      this.querySelector('i').classList.toggle('fa-eye');
      this.querySelector('i').classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>
