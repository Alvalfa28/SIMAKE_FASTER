<?php
session_start();
require_once 'koneksi.php'; 
include 'function.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'staf_keuangan') {
    header("Location: unauthorized.php");
    exit;
}

// Ambil data ringkasan untuk dashboard
$count_pending_query = mysqli_query($c, "SELECT COUNT(*) as total FROM bukti_pembayaran WHERE statuz = 'Diajukan'");
$count_pending = mysqli_fetch_array($count_pending_query)['total'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Verifikasi Bukti Pembayaran - SIMAKE</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .sb-sidenav-dark .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .badge { padding: 8px; border-radius: 6px; }
        /* Perbaikan kontras untuk link alasan */
        .link-alasan {
            color: #0d6efd !important; /* Biru yang lebih kuat */
            font-weight: 600;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .link-alasan:hover {
            text-decoration: underline;
            color: #0a58ca !important;
        }
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
                <ul class="dropdown-menu dropdown-menu-start shadow">
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
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div> Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Manajemen Kas</div>
                        <a class="nav-link <?= ($current_page == 'transaksiMasuk.php') ? 'active' : ''; ?>" href="transaksiMasuk.php">
                            <div class="sb-nav-link-icon text-success"><i class="fas fa-arrow-circle-down"></i></div> Transaksi Masuk
                        </a>
                        <a class="nav-link <?= ($current_page == 'transaksiKeluar.php') ? 'active' : ''; ?>" href="transaksiKeluar.php">
                            <div class="sb-nav-link-icon text-danger"><i class="fas fa-arrow-circle-up"></i></div> Transaksi Keluar
                        </a>
                        <div class="sb-sidenav-menu-heading">Verifikasi & Laporan</div>
                        <a class="nav-link <?= ($current_page == 'verifikasiBuktiPembayaran.php') ? 'active' : ''; ?>" href="verifikasiBuktiPembayaran.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-check"></i></div> Verifikasi Transaksi Mahasiswa
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
                    <h1 class="mt-4">Verifikasi Pembayaran</h1>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <i class="fas fa-table me-1"></i> Daftar Bukti Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Mahasiswa</th>
                                            <th>Jumlah (Rp)</th>
                                            <th>Tanggal</th>
                                            <th>Lampiran</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $get = mysqli_query($c, "SELECT * FROM bukti_pembayaran ORDER BY tanggal DESC");
                                        $i = 1;
                                        while($b_p = mysqli_fetch_array($get)){
                                            $id_bukti_pembayaran = $b_p['id_bukti_pembayaran'];
                                            $npm = $b_p['npm']; 
                                            $nama = $b_p['nama'];
                                            $jumlah = $b_p['jumlah'];
                                            $tanggal = date('d/m/Y', strtotime($b_p['tanggal']));
                                            $lampiran = $b_p['lampiran']; 
                                            $statuz = $b_p['statuz'];
                                            $komentar_revisi = $b_p['komentar_revisi']; 
                                        ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><strong><?=$nama;?></strong><br><small><?=$npm;?></small></td>
                                            <td><?=number_format($jumlah, 0, ',', '.');?></td>
                                            <td><?=$tanggal;?></td>
                                            <td class="text-center">
                                                <?php if(!empty($lampiran)): ?>
                                                    <a href="uploads/<?=$lampiran;?>" class="btn btn-outline-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
                                                <?php else: ?>
                                                    <span class="text-muted small">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($statuz == 'Diajukan') {
                                                    echo '<span class="badge bg-warning text-dark">Diajukan</span>';
                                                } elseif ($statuz == 'Disetujui') {
                                                    echo '<span class="badge bg-success">Lunas</span>';
                                                } elseif ($statuz == 'Revisi') {
                                                    echo '<span class="badge bg-danger mb-1">Revisi</span><br>';
                                                    echo '<a href="#" data-bs-toggle="modal" data-bs-target="#komentar'.$id_bukti_pembayaran.'" class="link-alasan">
                                                            <i class="fas fa-comment-dots me-1"></i>Komentar
                                                          </a>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <?php if ($statuz == 'Disetujui'): ?>
                                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#revisi<?= $id_bukti_pembayaran; ?>">
                                                            <i class="fas fa-undo"></i> Batalkan & Revisi
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#verif<?= $id_bukti_pembayaran; ?>">
                                                            <i class="fas fa-check"></i> Setujui
                                                        </button>
                                                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#revisi<?= $id_bukti_pembayaran; ?>">
                                                            <i class="fas fa-edit"></i> Revisi
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="verif<?= $id_bukti_pembayaran; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title">Konfirmasi Setuju</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Setujui pembayaran dari <strong><?=$nama;?></strong>?
                                                            <input type="hidden" name="id_b_p" value="<?= $id_bukti_pembayaran; ?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" name="verifikasi_bukti_pembayaran" class="btn btn-success">Ya, Setujui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="revisi<?= $id_bukti_pembayaran; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header bg-dark text-white">
                                                            <h5 class="modal-title">Instruksi Revisi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label class="mb-2">Alasan Revisi/Pembatalan:</label>
                                                            <textarea name="komentar_revisi" class="form-control" rows="3" required placeholder="Contoh: Nominal salah, mohon upload ulang..."></textarea>
                                                            <input type="hidden" name="id_b_p" value="<?= $id_bukti_pembayaran; ?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                            <button type="submit" name="revisi_bukti_pembayaran" class="btn btn-danger">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Komentar -->
                                        <div class="modal fade" id="komentar<?=$id_bukti_pembayaran;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Alasan Revisi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?=$b_p['komentar_revisi'] ? $b_p['komentar_revisi'] : '<i>Tidak ada komentar</i>';?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script>
        const datatablesSimple = document.getElementById('datatablesSimple');
        if (datatablesSimple) { 
            new simpleDatatables.DataTable(datatablesSimple); // Baris inilah yang membuat pagination muncul
        }
    </script>
</body>
</html>