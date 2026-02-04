<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php'; 
// PENTING: Pastikan di dalam function.php tidak ada session_start() lagi 
// agar tidak bentrok, atau gunakan pengecekan session_status yang sama.
include 'function.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek role agar hanya prodi yang bisa mengakses
if ($_SESSION['role'] !== 'prodi') {
    header("Location: unauthorized.php");
    exit();
}

// Variabel untuk digunakan di query dalam halaman ini
$id_prodi_saya = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>RAPBF - Prodi</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous"/>
        
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        <style>
            /* --- PERUBAHAN 2: CSS khusus agar menu aktif berwarna putih terang --- */
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
                color: #fff !important;
                font-weight: bold;
                background-color: rgba(255, 255, 255, 0.1); /* Background tipis untuk penanda */
            }
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active .sb-nav-link-icon {
                color: #fff !important;
            }
            .table td { vertical-align: middle !important; }
        </style>
    </head>
    
    <body class="sb-nav-fixed">
        
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="dashboardProdi.php">Aplikasi SIMAKE</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

            <ul class="navbar-nav ms-2 me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-fw"></i> 
                        <?php 
                            $nama = $_SESSION['nama'] ?? 'Guest';
                            $npm  = $_SESSION['username'] ?? '';
                            echo htmlspecialchars($nama); 
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
                            
                            <a class="nav-link <?= ($current_page == 'dashboardProdi.php') ? 'active' : ''; ?>" href="dashboardProdi.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>

                            <div class="sb-sidenav-menu-heading">Layanan</div>
                            
                            <a class="nav-link <?= ($current_page == 'rapbf.php') ? 'active' : ''; ?>" href="rapbf.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                Ajukan RAPBF
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Data RAPBF</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Selamat Datang Unit Kerja/Prodi</li>
                        </ol>

                        <button type="button" class="btn btn-info mb-4 text-white" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fas fa-plus"></i> Tambah RAPBF
                        </button>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i> Data Pengajuan RAPBF Anda
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="datatablesSimple" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Anggaran</th> 
                                                <th>Periode</th>      
                                                <th>Total Anggaran</th>
                                                <th>Tanggal Unggah</th>
                                                <th>Keterangan</th>
                                                <th>Lampiran</th>
                                                <th>Status</th>
                                                <th>Komentar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $id_prodi_saya = $_SESSION['user_id'];
                                            $get = mysqli_query($c, "SELECT * FROM rapbf WHERE id_prodi = '$id_prodi_saya' ORDER BY tanggal DESC");
                                            $i = 1;

                                            while($r = mysqli_fetch_array($get)){
                                                // --- DEFINISIKAN SEMUA VARIABEL DARI DATABASE ---
                                                $id_rapbf = $r['id_rapbf'];
                                                $nama = $r['nama'];
                                                $periode = $r['periode'];
                                                $total_anggaran = $r['total_anggaran'];
                                                $keterangan = $r['keterangan'];
                                                $statuz = $r['statuz'];
                                                $komentar_rapbf = $r['komentar_rapbf'];
                                            ?>
                                                <tr>
                                                    <td><?=$i++;?></td>
                                                    <td><strong><?=$nama;?></strong></td>
                                                    <td><?=$periode;?></td>
                                                    <td class="fw-bold text-success">Rp <?=number_format($total_anggaran, 0, ',', '.');?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($r['tanggal'])); ?></td>
                                                    <td><?=$keterangan ?: '-';?></td>
                                                    <td>
                                                        <?php if(!empty($r['lampiran'])): ?>
                                                            <a href="file_rapbf/<?=$r['lampiran'];?>" target="_blank" class="btn btn-outline-info btn-sm">
                                                                <i class="fas fa-file-pdf"></i> Lihat File
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">No File</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if ($statuz == 'Diajukan') echo '<span class="badge bg-warning text-dark">Diajukan</span>';
                                                            elseif ($statuz == 'Disetujui') echo '<span class="badge bg-success">Disetujui</span>';
                                                            elseif ($statuz == 'Revisi') echo '<span class="badge bg-danger">Revisi</span>';
                                                            else echo '<span class="badge bg-secondary">Draft</span>';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if($statuz == 'Revisi' || !empty($komentar_rapbf)): ?>
                                                            <button class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#komentar<?=$id_rapbf;?>">
                                                                <i class="fas fa-comment-dots"></i> Lihat Alasan
                                                            </button>
                                                        <?php else: ?> 
                                                            - 
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?=$id_rapbf;?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete<?=$id_rapbf;?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="komentar<?=$id_rapbf;?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info text-white">
                                                                <h5 class="modal-title">Komentar Revisi</h5>
                                                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="mb-0 text-muted">Pesan dari Admin:</p>
                                                                <div class="alert alert-secondary mt-2">
                                                                    <?= $komentar_rapbf ?: '<i>Tidak ada rincian komentar.</i>'; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="edit<?=$id_rapbf;?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Edit Data</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="post" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id_rap" value="<?=$id_rapbf;?>">
                                                                    
                                                                    <label class="form-label">Nama Anggaran</label>
                                                                    <input type="text" name="nama" class="form-control mb-2" value="<?=$nama;?>" required>
                                                                    
                                                                    <label class="form-label">Periode</label>
                                                                    <select name="periode" class="form-control mb-2">
                                                                        <?php 
                                                                        for($p=2025; $p<=2035; $p++) {
                                                                            $selected = ($periode == $p) ? 'selected' : '';
                                                                            echo "<option value='$p' $selected>$p</option>"; 
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    
                                                                    <label class="form-label">Total Anggaran</label>
                                                                    <input type="number" name="total_anggaran" class="form-control mb-2" value="<?=$total_anggaran;?>" required>
                                                                    
                                                                    <label class="form-label">Keterangan</label>
                                                                    <textarea name="keterangan" class="form-control mb-2"><?= htmlspecialchars($keterangan); ?></textarea>
                                                                    
                                                                    <label class="form-label">Ganti File (PDF)</label>
                                                                    <input type="file" name="lampiran" class="form-control" accept="application/pdf">
                                                                    <small class="text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success" name="edit_rapbf">Simpan Perubahan</button>
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>


                                            <div class="modal fade" id="delete<?=$id_rapbf;?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Hapus Data</h4>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                Apakah Anda yakin ingin menghapus <strong><?=$nama;?></strong>?
                                                                <input type="hidden" name="id_rap" value="<?=$id_rapbf;?>"> 
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary" name="hapus_rapbf">Hapus</button>
                                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
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
                    </div>
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>

    <div class="modal fade" id="myModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah RAPBF Baru</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <label>Nama Anggaran</label>
                        <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Kegiatan" required>
                        <label>Periode</label>
                        <select name="periode" class="form-select mb-2" required>
                            <option value="" disabled selected>Pilih Tahun</option>
                            <?php for($y=2025; $y<=2035; $y++) echo "<option value='$y'>$y</option>"; ?>
                        </select>
                        <label>Total Anggaran (Rp)</label>
                        <input type="number" name="total_anggaran" class="form-control mb-2" required>
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control mb-2"></textarea>
                        <label>Upload File (PDF)</label>
                        <input type="file" name="lampiran" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="tambah_rapbf">Simpan</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</html>