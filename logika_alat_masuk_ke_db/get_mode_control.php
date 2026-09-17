<?php

include '../db.php'; // koneksi db

$q = mysqli_query($conn, "SELECT mode_control FROM tb_control WHERE id=1");
$data = mysqli_fetch_assoc($q);
echo $data['mode_control'];
?>
