<?php 
session_start();
require_once 'koneksi.php'; 

// --- PERUBAHAN: Logika pendeteksi halaman aktif ---
$current_page = basename($_SERVER['PHP_SELF']); 

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek role agar hanya admin yang bisa mengakses
if ($_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Dashboard Admin - SIMAKE</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        
        <style>
            /* CSS agar menu aktif berwarna putih terang */
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
                color: #fff !important;
                font-weight: bold;
                background-color: rgba(255, 255, 255, 0.1);
            }
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active .sb-nav-link-icon {
                color: #fff !important;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.php">Aplikasi SIMAKE</a>

            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

            <ul class="navbar-nav ms-2 me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user fa-fw"></i> 
                        <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?>
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
                            
                            <a class="nav-link <?= ($current_page == 'index.php' || $current_page == 'dashboardAdmin.php') ? 'active' : ''; ?>" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>

                            <div class="sb-sidenav-menu-heading">Manajemen Kontrol</div>
                            
                            <a class="nav-link <?= ($current_page == 'kelolaUser.php') ? 'active' : ''; ?>" href="kelolaUser.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                                Kelola User
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Admin SIMAKE
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Sistem Manajemen Keuangan</h1>
                        <hr style="border: none; height: 3px; background-color: #343a40; margin-top: 0.5rem; margin-bottom: 2rem;">

                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Total User</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="kelolaUser.php">Lihat Detail</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center" style="height: 40vh;">
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-user-shield fa-5x text-secondary"></i>
                                </div>
                                <h2>Selamat datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
                                <p class="lead mt-3">Anda memiliki akses penuh sebagai <strong>Administrator Sistem</strong></p>
                            </div>
                        </div>
                    </div>
                </main>
                
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; SIMAKE 2025</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
    </body>
</html>