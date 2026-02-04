<?php 
session_start();
require_once 'koneksi.php'; 

// --- Logika pendeteksi halaman aktif ---
$current_page = basename($_SERVER['PHP_SELF']); 

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek role agar hanya wakil dekan 2 yang bisa mengakses
if ($_SESSION['role'] !== 'wakil_dekan_dua') {
    header("Location: unauthorized.php");
    exit;
}

// --- AMBIL DATA UNTUK STATISTIK (Sesuai Logika Akurat Dashboard) ---

// 1. Menghitung RAPBF yang perlu diverifikasi (Kondisi: Diajukan ATAU Revisi)
// Pastikan nama kolom 'status' sesuai dengan yang ada di tabel rapbf Anda
$query_rapbf = mysqli_query($c, "SELECT COUNT(*) as total FROM rapbf WHERE statuz='Diajukan' OR statuz='Revisi'");
$total_rapbf_pending = mysqli_fetch_array($query_rapbf)['total'] ?? 0;

// 2. Menghitung Laporan Keuangan yang perlu diverifikasi (Kondisi: Diajukan ATAU Revisi)
$query_pending = mysqli_query($c, "SELECT COUNT(*) as total FROM laporan_keuangan WHERE statuz='Diajukan' OR statuz='Revisi'");
$total_pending = mysqli_fetch_array($query_pending)['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Dashboard Wakil Dekan II</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous"/>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        
        <style>
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
                color: #fff !important;
                font-weight: bold;
                background-color: rgba(255, 255, 255, 0.1);
            }
            .welcome-card {
                background: linear-gradient(45deg, #212529, #343a40);
                color: white;
                border-radius: 15px;
                padding: 30px;
                margin-bottom: 30px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            .stat-card {
                border: none;
                border-radius: 10px;
                transition: transform 0.3s;
            }
            .stat-card:hover {
                transform: translateY(-5px);
            }
            .icon-circle {
                height: 50px;
                width: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255,255,255,0.2);
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="dashboardWdDua.php">Aplikasi SIMAKE</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <ul class="navbar-nav ms-2 me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user fa-fw"></i> <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
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
                            <a class="nav-link <?= ($current_page == 'dashboardWdDua.php') ? 'active' : ''; ?>" href="dashboardWdDua.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Manajemen Laporan</div>
                            <a class="nav-link <?= ($current_page == 'verifikasiLaporanKeuangan.php') ? 'active' : ''; ?>" href="verifikasiLaporanKeuangan.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-double"></i></div>
                                Verifikasi Laporan Keuangan
                            </a>
                            <a class="nav-link <?= ($current_page == 'verifikasiRapbf.php') ? 'active' : ''; ?>" href="verifikasiRapbf.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-folder-open"></i></div>
                                Verifikasi RAPBF
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Ringkasan Sistem SIMAKE</li>
                        </ol>

                        <div class="welcome-card shadow">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="display-6">Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?>!</h2>
                                    <p>Sistem Informasi Manajemen Keuangan (SIMAKE) siap membantu Anda mengontrol anggaran dan memverifikasi laporan keuangan secara efisien.</p>
                                </div>
                                <div class="col-md-4 text-center d-none d-md-block">
                                    <i class="fas fa-user-shield fa-5x opacity-50"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4 stat-card shadow">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="icon-circle me-3"><i class="fas fa-file-invoice"></i></div>
                                        <div>
                                            <div class="small text-white-50">RAPBF Perlu Verifikasi</div>
                                            <div class="h4 mb-0"><?= $total_rapbf_pending; ?> Dokumen</div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between small">
                                        <a class="text-white stretched-link" href="verifikasiRapbf.php">Verifikasi Sekarang</a>
                                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-white mb-4 stat-card shadow">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="icon-circle me-3"><i class="fas fa-clock"></i></div>
                                        <div>
                                            <div class="small text-white-50">Laporan Keuangan Perlu Verifikasi</div>
                                            <div class="h4 mb-0"><?= $total_pending; ?> Laporan</div> 
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between small">
                                        <a class="text-white stretched-link" href="verifikasiLaporanKeuangan.php">Verifikasi Sekarang</a>
                                        <div class="text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-body bg-light rounded text-center py-5">
                                <i class="fas fa-info-circle text-primary mb-3 fa-2x"></i>
                                <h5>Informasi Login</h5>
                                <p class="text-muted mb-0">Anda saat ini masuk sebagai otoritas <strong>Wakil Dekan II (Bidang Administrasi Umum & Keuangan)</strong>.</p>
                                <p class="text-muted small">Waktu Server: <?= date('d F Y | H:i'); ?></p>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; SIMAKE <?= date('Y'); ?></div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>