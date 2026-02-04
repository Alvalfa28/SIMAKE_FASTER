<?php
session_start();

// Jika belum login, arahkan ke login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
    <div class="text-center">
        <h1 class="display-5 text-danger fw-bold">⚠️ Akses Ditolak</h1>
        <p class="lead mt-3">Halo <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>,</p>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="logout.php" class="btn btn-secondary mt-3">Kembali ke Login</a>
    </div>
</body>
</html>
