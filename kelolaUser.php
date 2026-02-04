<?php
session_start();
require_once 'koneksi.php';
include 'function.php'; 

// Logika pendeteksi halaman aktif
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
        <title>Kelola User - SIMAKE</title>
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
            .badge-role {
                min-width: 100px;
            }
            .dataTables_length, .dataTables_filter {
                margin-bottom: 20px;
            }
            .dataTables_paginate {
                margin-top: 15px;
            }
            /* Menghilangkan panah sorting pada kolom Aksi */
            #datatablesSimple thead th:last-child::after, 
            #datatablesSimple thead th:last-child::before {
                display: none !important;
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
                        <h1 class="mt-4">Manajemen User</h1>
                        <hr style="border: none; height: 3px; background-color: black; margin-bottom: 20px;">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="fas fa-user-plus me-2"></i>Tambah User Baru
                                </button>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <i class="fas fa-table me-1"></i>
                                Daftar Akun Pengguna
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple" class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $get = mysqli_query($c, "SELECT * FROM users ORDER BY id DESC");
                                    $i = 1;
                                    while($us = mysqli_fetch_array($get)){
                                        $id_user = $us['id']; 
                                        $username = $us['username'];    
                                        $nama = $us['nama'];
                                        $role = $us['role'];       
                                    ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=htmlspecialchars($nama);?></td>
                                            <td><?=htmlspecialchars($username);?></td>
                                            <td>
                                                <span class="badge bg-secondary badge-role">
                                                    <?=str_replace('_', ' ', strtoupper($role));?>
                                                </span>
                                            </td>
                                            <td>
                                                <button title="Edit" type="button" class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#edit<?=$id_user;?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button title="Hapus" type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete<?=$id_user;?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="edit<?= $id_user; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Ubah User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_user" value="<?= $id_user; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Nama Lengkap</label>
                                                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    Username 
                                                                    <span class="text-muted fw-normal" style="font-size: 0.85rem;">
                                                                        (cth: admin2 atau NPM jika mahasiswa)
                                                                    </span>
                                                                </label>
                                                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Password Baru</label>
                                                                <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Role</label>
                                                                <select name="role" class="form-select" required>
                                                                    <option value="admin" <?= $role === "admin" ? "selected" : ""; ?>>Admin</option>
                                                                    <option value="staf_keuangan" <?= $role === "staf_keuangan" ? "selected" : ""; ?>>Staf Keuangan</option>
                                                                    <option value="wakil_dekan_dua" <?= $role === "wakil_dekan_dua" ? "selected" : ""; ?>>Wakil Dekan II</option>
                                                                    <option value="prodi" <?= $role === "prodi" ? "selected" : ""; ?>>Prodi</option>
                                                                    <option value="mahasiswa" <?= $role === "mahasiswa" ? "selected" : ""; ?>>Mahasiswa</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="edit_user" class="btn btn-warning text-white">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="delete<?=$id_user;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h4 class="modal-title">Konfirmasi Hapus</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body text-center">
                                                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                                            <p>Apakah Anda yakin ingin menghapus user <strong><?= htmlspecialchars($nama); ?></strong>?</p>
                                                            <input type="hidden" name="id_user" value="<?=$id_user;?>"> 
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger" name="hapus_user">Ya, Hapus</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <div>Copyright &copy; SIMAKE 2025</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <div class="modal fade" id="modalTambah">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h4 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama_baru" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Username</label>
                                <input type="text" name="username_baru" class="form-control" placeholder="Contoh: admin2 atau NPM (jika mahasiswa)" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Password</label>
                                <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Role Access</label>
                                <select name="role_baru" class="form-select" required> 
                                    <option value="" disabled selected>-- Pilih Role --</option>
                                    <option value="admin">Admin</option>
                                    <option value="staf_keuangan">Staf Keuangan</option>
                                    <option value="wakil_dekan_dua">Wakil Dekan II</option>
                                    <option value="prodi">Prodi</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success" name="tambah_user">Simpan User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

        <script>
            $(document).ready(function() {
                $('#datatablesSimple').DataTable({
                    // Mengubah bahasa ke Indonesia (Opsional)
                    "language": {
                        "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                        "zeroRecords": "Data tidak ditemukan",
                        "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                        "infoEmpty": "Tidak ada data tersedia",
                        "infoFiltered": "(disaring dari _MAX_ total data)",
                        "search": "Cari:",
                        "paginate": {
                            "first": "Pertama",
                            "last": "Terakhir",
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        },
                    },
                    // Mengatur default sorting pada kolom ke-0 (No) secara Descending
                    "order": [[ 0, "desc" ]] 
                });
            });
        </script>
    </body>
</html>