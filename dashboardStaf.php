<?php 
session_start();
require_once 'koneksi.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek role agar hanya staf_keuangan yang bisa mengakses
if ($_SESSION['role'] !== 'staf_keuangan') {
    header("Location: unauthorized.php");
    exit;
}

// Ambil data ringkasan untuk dashboard
$count_pending = mysqli_fetch_array(mysqli_query($c, "SELECT COUNT(*) as total FROM bukti_pembayaran WHERE statuz = 'Diajukan'"))['total'];
$total_masuk = mysqli_fetch_array(mysqli_query($c, "SELECT SUM(jumlah) as total FROM transaksi_masuk"))['total'];
$total_keluar = mysqli_fetch_array(mysqli_query($c, "SELECT SUM(jumlah) as total FROM transaksi_keluar"))['total'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard Staf Keuangan - SIMAKE</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .card-custom { border-radius: 15px; border: none; transition: transform 0.2s; }
        .card-custom:hover { transform: translateY(-5px); }
        .bg-gradient-primary { background: linear-gradient(45deg, #4e73df 10%, #224abe 100%); }
        .bg-gradient-success { background: linear-gradient(45deg, #1cc88a 10%, #13855c 100%); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e 10%, #dda20a 100%); }
        .bg-gradient-danger { background: linear-gradient(45deg, #e74a3b 10%, #be2617 100%); }
    </style>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark shadow">
        <a class="navbar-brand ps-3" href="dashboardStaf.php">Aplikasi SIMAKE</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

        <ul class="navbar-nav ms-2 me-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-shield fa-fw"></i> 
                    <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?> 
                </a>
                <ul class="dropdown-menu dropdown-menu-start shadow" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link <?= ($current_page == 'dashboardStaf.php') ? 'active' : ''; ?>" href="dashboardStaf.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Manajemen Kas</div>
                        <a class="nav-link" href="transaksiMasuk.php">
                            <div class="sb-nav-link-icon text-success"><i class="fas fa-arrow-circle-down"></i></div>
                            Transaksi Masuk
                        </a>
                        <a class="nav-link" href="transaksiKeluar.php">
                            <div class="sb-nav-link-icon text-danger"><i class="fas fa-arrow-circle-up"></i></div>
                            Transaksi Keluar
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Verifikasi & Laporan</div>
                        <a class="nav-link" href="verifikasiBuktiPembayaran.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-check"></i></div>
                            Verifikasi Transaksi Mahasiswa
                            <?php if($count_pending > 0): ?>
                                <span class="badge bg-danger ms-2"><?= $count_pending; ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="laporanKeuangan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            Laporan Keuangan
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content" class="bg-light">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Ringkasan Keuangan</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Sistem Informasi Manajemen Keuangan (SIMAKE)</li>
                    </ol>

                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom bg-gradient-warning text-white shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Antrean Verifikasi</div>
                                            <div class="h5 mb-0 font-weight-bold"><?= $count_pending; ?> Mahasiswa</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between small">
                                    <a class="text-white stretched-link" href="verifikasiBuktiPembayaran.php">Lihat Detail</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom bg-gradient-success text-white shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Kas Masuk</div>
                                            <div class="h5 mb-0 font-weight-bold">Rp<?= number_format($total_masuk, 0, ',', '.'); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-wallet fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between small">
                                    <a class="text-white stretched-link" href="transaksiMasuk.php">Rincian Transaksi</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom bg-gradient-danger text-white shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Kas Keluar</div>
                                            <div class="h5 mb-0 font-weight-bold">Rp<?= number_format($total_keluar, 0, ',', '.'); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hand-holding-usd fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between small">
                                    <a class="text-white stretched-link" href="transaksiKeluar.php">Rincian Transaksi</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom bg-gradient-primary text-white shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Saldo Kas Saat Ini</div>
                                            <div class="h5 mb-0 font-weight-bold">Rp<?= number_format($total_masuk - $total_keluar, 0, ',', '.'); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-university fa-2x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between small">
                                    <a class="text-white stretched-link" href="laporanKeuangan.php">Buat Laporan Keuangan</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4 card-custom border-start border-primary border-5">
                        <div class="card-body p-5">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h2 class="text-primary">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?>!</h2>
                                    <p class="lead text-muted">Akses cepat manajemen keuangan SIMAKE tersedia di menu samping atau melalui panel ringkasan di atas.</p>
                                </div>
                                <div class="col-lg-4 text-center d-none d-lg-block">
                                    <i class="fas fa-user-shield fa-8x text-light"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>