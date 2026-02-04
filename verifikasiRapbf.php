<?php 
session_start();
require_once 'koneksi.php'; 
include 'function.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Proteksi Role: Hanya wakil_dekan_dua yang bisa mengakses
if ($_SESSION['role'] !== 'wakil_dekan_dua') {
    header("Location: unauthorized.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Daftar Pengajuan RAPBF</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous"/>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            .table td { vertical-align: middle !important; }
            .text-nowrap { white-space: nowrap; }
            .truncate-text {
                max-width: 150px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            /* Memperbaiki jarak search bar dan entries */
            .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
                margin-bottom: 1rem;
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
                        <h1 class="mt-4">Data Pengajuan RAPBF</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Rekapitulasi Seluruh Prodi</li>
                        </ol>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-light">
                                <i class="fas fa-table me-1"></i>
                                <strong>Tabel Pengajuan RAPBF</strong>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover" id="datatablesSimple" width="100%" cellspacing="0">
                                        <!-- <thead class="table-dark"> -->
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Prodi</th>
                                                <th>Nama Kegiatan</th>
                                                <th>Periode</th>
                                                <th>Anggaran (Rp)</th>
                                                <th>Tanggal</th>
                                                <th>Keterangan</th>
                                                <th>Lampiran</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $get = mysqli_query($c, "
                                                SELECT r.*, u.nama as nama_prodi 
                                                FROM rapbf r 
                                                JOIN users u ON r.id_prodi = u.id
                                                ORDER BY r.tanggal DESC
                                            ");
                                            $i = 1;
                                            while($r = mysqli_fetch_array($get)){
                                                $id_rapbf = $r['id_rapbf'];
                                                $statuz = $r['statuz'];
                                                $komentar_rapbf = $r['komentar_rapbf']; 
                                            ?>
                                            <tr>
                                                <td><?=$i++;?></td>
                                                <td><strong><?=$r['nama_prodi'];?></strong></td>
                                                <td><?=$r['nama'];?></td>
                                                <td><?=$r['periode'];?></td>
                                                <td class="fw-bold text-success"><?=number_format($r['total_anggaran'], 0, ',', '.');?></td>
                                                <td><?=date('d/m/Y', strtotime($r['tanggal']));?></td>
                                                <td><small><?= $r['keterangan']; ?></small></td>
                                                <td class="text-center">
                                                    <?php if(!empty($r['lampiran'])): ?>
                                                        <a href="file_rapbf/<?=$r['lampiran'];?>" class="btn btn-outline-danger btn-sm" target="_blank">
                                                            <i class="fas fa-file-pdf"></i> PDF
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($statuz == 'Diajukan') {
                                                        echo '<span class="badge bg-warning text-dark">Diajukan</span>';
                                                    } elseif ($statuz == 'Disetujui') {
                                                        echo '<span class="badge bg-success">Disetujui</span>';
                                                    } elseif ($statuz == 'Revisi') {
                                                        echo '<span class="badge bg-danger mb-1">Revisi</span><br>';
                                                        echo '<a href="#" data-bs-toggle="modal" data-bs-target="#komentar'.$id_rapbf.'" class="link-alasan small text-decoration-none">
                                                                <i class="fas fa-comment-dots me-1"></i>Komentar
                                                            </a>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <?php if ($statuz == 'Disetujui'): ?>
                                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#revisi<?= $id_rapbf; ?>">
                                                                <i class="fas fa-undo"></i> Batalkan & Revisi
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#verif<?= $id_rapbf; ?>">
                                                                <i class="fas fa-check"></i> Setujui
                                                            </button>
                                                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#revisi<?= $id_rapbf; ?>">
                                                                <i class="fas fa-edit"></i> Revisi
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="verif<?= $id_rapbf; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="post">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title">Konfirmasi Setuju</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Setujui pengajuan RAPBF dari prodi <strong><?=$r['nama_prodi'];?></strong>?
                                                                <input type="hidden" name="id_rapbf" value="<?= $id_rapbf; ?>">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" name="verifikasi_setuju" class="btn btn-success">Ya, Setujui</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="revisi<?= $id_rapbf; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="post">
                                                            <div class="modal-header bg-dark text-white">
                                                                <h5 class="modal-title">Instruksi Revisi</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label class="mb-2">Alasan Revisi/Pembatalan:</label>
                                                                <textarea name="komentar_rapbf" class="form-control" rows="3" required placeholder="Tuliskan catatan revisi di sini..."></textarea>
                                                                <input type="hidden" name="id_rapbf" value="<?= $id_rapbf; ?>">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" name="verifikasi_revisi" class="btn btn-danger">Kirim Revisi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="komentar<?=$id_rapbf;?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Alasan Revisi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?= $komentar_rapbf ?: '<i>Tidak ada komentar</i>'; ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        
        <script> $(document).ready(function() { $('#datatablesSimple').DataTable(); }); </script>
    </body>
</html>