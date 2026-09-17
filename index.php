<?php
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    // Redirect ke halaman yang sesuai berdasarkan role
    if ($_SESSION['role'] == 'admin') {
        header('Location: index_admin.php');
    } else {
        header('Location: index_user.php');
    }
    exit();
} else {
    // Jika belum login, arahkan ke halaman login
    header('Location: login.php');
    exit();
}
?>
