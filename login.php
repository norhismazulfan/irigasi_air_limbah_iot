<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Jika sudah login, redirect ke halaman dashboard
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query untuk mengecek user di database (gunakan prepared statements)
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika username dan password valid
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role']; // 'admin' or 'user'

        // Redirect ke halaman index
        header('Location: index.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login – Irrigation System</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome (untuk ikon) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background: #f0f2f5;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .login-card {
      width: 100%;
      max-width: 440px;
      border: none;
      border-radius: 0.75rem;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
      animation: fadeInUp 0.6s ease-out;
    }
    .login-card .card-header {
      background-color: #007bff;
      border-bottom: none;
      color: #fff;
      font-weight: 600;
      text-align: center;
      border-top-left-radius: 0.75rem;
      border-top-right-radius: 0.75rem;
      position: relative;
    }
    .login-card .card-body {
      padding: 2rem;
    }
    .login-card .input-group-text {
      background-color: #e9ecef;
      border: none;
      border-radius: 0.375rem 0 0 0.375rem;
      color: #495057;
    }
    .login-card .form-control:focus {
      box-shadow: none;
      border-color: #007bff;
    }
    .login-card .btn-login {
      background-color: #007bff;
      border: none;
      box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }
    .login-card .btn-login:hover {
      background-color: #0056b3;
      box-shadow: 0 3px 10px rgba(0, 86, 179, 0.4);
    }
    .login-card .form-check-label {
      user-select: none;
    }
    .login-card .card-footer {
      background-color: #f8f9fa;
      border-top: none;
      border-bottom-left-radius: 0.75rem;
      border-bottom-right-radius: 0.75rem;
    }
    .fadeInUp {
      opacity: 0;
      transform: translateY(20px);
      animation-name: fadeInUp;
      animation-fill-mode: forwards;
    }
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    /* Tombol show/hide password kecil */
    .toggle-password {
      cursor: pointer;
      color: #6c757d;
    }
  </style>
</head>
<body>

  <div class="card login-card shadow-sm fadeInUp">
    <div class="card-header py-3">
      <i class="fas fa-water fa-lg me-2"></i> Irrigation System
    </div>
    <div class="card-body">
      <?php if (isset($error)): ?>
        <div class="alert alert-danger d-flex align-items-center py-2" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>
          <div><?php echo $error; ?></div>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <!-- Username -->
        <div class="mb-3">
          <label for="username" class="form-label text-secondary">Username</label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="fas fa-user"></i>
            </span>
            <input
              type="text"
              id="username"
              name="username"
              class="form-control"
              placeholder="Masukkan username"
              required
              autofocus
            >
          </div>
        </div>

        <!-- Password -->
        <div class="mb-3 position-relative">
          <label for="password" class="form-label text-secondary">Password</label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="fas fa-lock"></i>
            </span>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Masukkan password"
              required
            >
            <span class="input-group-text toggle-password" id="togglePassword">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>


        <!-- Submit Button -->
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-login btn-lg text-white">
            <i class="fas fa-sign-in-alt me-1"></i> Login
          </button>
        </div>

        <!-- Divider -->
        <div class="text-center text-muted small mb-0">
          &mdash; or &mdash;
        </div>
        <div class="text-center mt-3">
          <a href="register.php" class="link-primary">Create an account</a>
        </div>
      </form>
    </div>
    <div class="card-footer text-center small text-muted py-2">
      &copy; <?php echo date('Y'); ?> Irrigation System
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Script Show/Hide Password -->
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput  = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.querySelector('i').classList.toggle('fa-eye');
      this.querySelector('i').classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>
