<?php 
session_start();
require_once 'koneksi.php'; 
include 'function.php';

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

$id_mahasiswa_saat_ini = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Bukti Pembayaran - SIMAKE</title>
        
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

        <style>
            .sb-nav-fixed #layoutSidenav_content { background-color: #f8f9fa; }
            .card { border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            .card-header { background-color: #fff !important; font-weight: bold; border-bottom: 1px solid #eee !important; }
            .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
                color: #fff !important;
                background-color: rgba(255, 255, 255, 0.1);
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
                        <?= htmlspecialchars($_SESSION['nama']) . " (" . htmlspecialchars($_SESSION['username']) . ")"; ?>
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
                        <h1 class="mt-4">Bukti Pembayaran</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboardMahasiswa.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Data Bukti</li>
                        </ol>

                        <button type="button" class="btn btn-primary mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Bukti Baru
                        </button>

                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <i class="fas fa-list me-2"></i> Riwayat Pengajuan Anda
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                            <th>Lampiran</th>
                                            <th>Status</th>
                                            <th>Komentar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $get = mysqli_query($c, "SELECT * FROM bukti_pembayaran WHERE id_mahasiswa = '$id_mahasiswa_saat_ini' ORDER BY tanggal DESC");
                                    $i = 1;

                                    while($b_p = mysqli_fetch_array($get)){
                                        $id_bukti_pembayaran = $b_p['id_bukti_pembayaran'];
                                        $statuz = $b_p['statuz'];
                                    ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td class="fw-bold text-success">Rp<?=number_format($b_p['jumlah']);?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($b_p['tanggal'])); ?></td>
                                            <td><?=$b_p['keterangan'];?></td>
                                            <td>
                                                <a href="uploads/<?=$b_p['lampiran'];?>" target="_blank" class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-file-pdf"></i> Lihat File
                                                </a>
                                            </td>
                                            <td>
                                                <?php 
                                                    if ($statuz == 'Diajukan') echo '<span class="badge bg-warning text-dark">Diajukan</span>';
                                                    elseif ($statuz == 'Disetujui') echo '<span class="badge bg-success">Lunas</span>';
                                                    elseif ($statuz == 'Revisi') echo '<span class="badge bg-danger">Revisi</span>';
                                                    else echo '<span class="badge bg-secondary">Draft</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if($statuz == 'Revisi'): ?>
                                                    <button class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#komentar<?=$id_bukti_pembayaran;?>">
                                                        <i class="fas fa-comment-dots"></i> Lihat Alasan
                                                    </button>
                                                <?php else: ?> - <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?=$id_bukti_pembayaran;?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete<?=$id_bukti_pembayaran;?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="komentar<?=$id_bukti_pembayaran;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">Komentar Revisi</h5>
                                                        <button type="button" class="close text-white" data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-0 text-muted">Pesan dari Admin:</p>
                                                        <div class="alert alert-secondary mt-2">
                                                            <?= $b_p['komentar_revisi'] ?: '<i>Tidak ada rincian komentar.</i>'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="delete<?=$id_bukti_pembayaran;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Data</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus pengajuan bukti sebesar <strong>Rp<?=number_format($b_p['jumlah']);?></strong> ini?
                                                            <input type="hidden" name="id_b_p" value="<?=$id_bukti_pembayaran;?>"> 
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger" name="hapus_bukti_pembayaran">Ya, Hapus</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="edit<?=$id_bukti_pembayaran;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Edit Bukti Pembayaran</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form method="post" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id_b_p" value="<?=$id_bukti_pembayaran;?>">
                                                            <input type="hidden" name="lampiran_lama" value="<?=$b_p['lampiran'];?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jumlah Pembayaran</label>
                                                                <input type="number" name="jumlah" class="form-control" value="<?=$b_p['jumlah'];?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Keterangan</label>
                                                                <input type="text" name="keterangan" class="form-control" value="<?=$b_p['keterangan'];?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Ganti File PDF (Kosongkan jika tidak ganti)</label>
                                                                <input type="file" name="lampiran_baru" class="form-control" accept="application/pdf">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary" name="edit_bukti_pembayaran">Simpan Perubahan</button>
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
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        <script src="js/datatables-simple-demo.js"></script>

        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Bukti Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah Pembayaran (Rp)</label>
                                <input type="number" name="jumlah" class="form-control" placeholder="0" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Contoh: UKT Semester 4" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload File (PDF)</label>
                                <input type="file" name="lampiran" class="form-control" accept="application/pdf" required>
                                <small class="text-muted italic">*Wajib PDF</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" name="tambah_bukti_pembayaran">Submit Bukti</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>