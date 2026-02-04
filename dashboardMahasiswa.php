<?php 
session_start();
require_once 'koneksi.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek role agar hanya mahasiswa yang bisa mengakses
if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: unauthorized.php");
    exit;
}

// Menentukan halaman aktif untuk class 'active' di sidebar
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Dashboard Mahasiswa - SIMAKE</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        
        <style>
            .sb-nav-fixed #layoutSidenav_content { background-color: #f8f9fa; }
            /* Mempercantik Card Selamat Datang */
            .welcome-card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                background: linear-gradient(135deg, #ffffff 0%, #f1f4f9 100%);
                padding: 3rem;
            }
            /* Konsistensi Sidebar */
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
                color: #fff !important;
                background-color: rgba(255, 255, 255, 0.1);
            }
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active .sb-nav-link-icon {
                color: #fff !important;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="dashboardMahasiswa.php">Aplikasi SIMAKE</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

            <ul class="navbar-nav ms-2 me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-fw"></i> 
                        <?php 
                            $nama = $_SESSION['nama'] ?? 'Guest';
                            $npm  = $_SESSION['username'] ?? '';
                            echo htmlspecialchars($nama) . " (" . htmlspecialchars($npm) . ")"; 
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
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
                            
                            <a class="nav-link <?= ($current_page == 'dashboardMahasiswa.php') ? 'active' : ''; ?>" href="dashboardMahasiswa.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>

                            <div class="sb-sidenav-menu-heading">Layanan</div>
                            
                            <a class="nav-link <?= ($current_page == 'buktiPembayaran.php') ? 'active' : ''; ?>" href="buktiPembayaran.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                Bukti Pembayaran
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>

                        <div class="row justify-content-center mt-5">
                            <div class="col-lg-8">
                                <div class="card welcome-card text-center">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <i class="fas fa-user-graduate fa-5x text-primary opacity-25"></i>
                                        </div>
                                        <h2 class="display-6">Selamat datang, <span class="text-primary"><?= htmlspecialchars($_SESSION['nama']); ?></span>!</h2>
                                        <p class="lead mt-3">Sistem Informasi Manajemen Keuangan (SIMAKE)</p>
                                        <div class="mt-4 pt-3 border-top">
                                            <p class="text-muted">Gunakan menu di samping kiri untuk mengelola <strong>Bukti Pembayaran</strong> Anda.</p>
                                            <a href="buktiPembayaran.php" class="btn btn-primary px-4 py-2 mt-2">
                                                <i class="fas fa-upload me-2"></i>Upload Bukti Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4 text-center">
                        <small class="text-muted small">Copyright &copy; SIMAKE 2025</small>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>