<?php
session_start();
require_once 'koneksi.php';
include 'function.php';

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
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Laporan Keuangan</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet"/>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
        <style>
            .badge-category { font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 50rem; }
            .table-hover tbody tr:hover { background-color: #f8f9fa; }
        </style>

    </head>

    <body class="sb-nav-fixed">
        
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="dashboardStaf.php">Aplikasi SIMAKE</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Navbar User Dropdown -->
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
                                <div class="sb-nav-link-icon"><i class="fas fa-user-check"></i></div> 
                                Verifikasi Transaksi Mahasiswa <?php if($count_pending > 0): ?><span class="badge bg-danger ms-2"><?= $count_pending; ?></span><?php endif; ?>
                            </a>
                            <a class="nav-link <?= ($current_page == 'laporanKeuangan.php') ? 'active' : ''; ?>" href="laporanKeuangan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            Laporan Keuangan
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4 fw-bold">Transaksi Masuk</h1>
                        
                        <button type="button" class="btn btn-primary mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Transaksi Masuk
                        </button>

                        <div class="card shadow mb-4">
                            <div class="card-body text-dark">
                                <div class="card-header bg-white">
                                    <i class="fas fa-table me-1"></i> Daftar Transaksi Masuk
                                </div>
                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Jenis</th> <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <th>Lampiran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $get = mysqli_query($c, "SELECT * FROM transaksi_masuk ORDER BY tanggal DESC");
                                        $i = 1;
                                        while($t_m = mysqli_fetch_array($get)){
                                            $id_tm = $t_m['id_transaksi_masuk'];
                                            $lampiran = $t_m['lampiran'];
                                        ?>
                                        <tr>
                                            <td class="text-center"><?=$i++;?></td>
                                            <td class="text-center"><?=date('d/m/Y', strtotime($t_m['tanggal']));?></td>
                                            <td class="fw-bold"><?=$t_m['kategori'];?></td>
                                            <td class="fw-bold"><?=$t_m['jenis'];?></td>
                                            <td class="text-end fw-bold text-success">Rp<?=number_format($t_m['jumlah']);?></td>
                                            <td><?= htmlspecialchars($t_m['keterangan'] ?: '-'); ?></td>
                                            <td class="text-center">
                                                <?php if($lampiran): ?>
                                                    <a href="uploads/<?=$lampiran;?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf"></i> PDF</a>
                                                <?php else: ?>
                                                    <small class="text-muted">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"> 
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#edit<?=$id_tm;?>"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$id_tm;?>"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="edit<?=$id_tm;?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title">Edit Transaksi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post" enctype="multipart/form-data">
                                                        <div class="modal-body text-start">
                                                            <input type="hidden" name="id_t_m" value="<?=$id_tm;?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Tanggal</label>
                                                                <input type="date" name="tanggal" class="form-control" 
                                                                    value="<?= date('Y-m-d', strtotime($t_m['tanggal'])); ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Kategori</label>
                                                                <select name="kategori" class="form-select kategoriEdit" data-id="<?=$id_tm;?>" data-current-jenis="<?=$t_m['jenis'];?>" required>
                                                                    <option value="Pendapatan Jasa Layanan Pendidikan" <?=($t_m['kategori'] == 'Pendapatan Jasa Layanan Pendidikan') ? 'selected':'';?>>Layanan Pendidikan</option>
                                                                    <option value="Pendapatan Bimbingan Penelitian dan Sidang TA" <?=($t_m['kategori'] == 'Pendapatan Bimbingan Penelitian dan Sidang TA') ? 'selected':'';?>>Bimbingan & Sidang</option>
                                                                    <option value="Pendapatan Hibah" <?=($t_m['kategori'] == 'Pendapatan Hibah') ? 'selected':'';?>>Hibah</option>
                                                                    <option value="Pendapatan Unit Bisnis" <?=($t_m['kategori'] == 'Pendapatan Unit Bisnis') ? 'selected':'';?>>Unit Bisnis</option>
                                                                    <option value="Pendapatan Lainnya" <?=($t_m['kategori'] == 'Pendapatan Lainnya') ? 'selected':'';?>>Lain-Lain</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jenis</label>
                                                                <select name="jenis" id="jenisEdit<?=$id_tm;?>" class="form-select" required></select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jumlah (Rp)</label>
                                                                <input type="number" name="jumlah" class="form-control" value="<?=$t_m['jumlah'];?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Update Lampiran PDF (Opsional)</label>
                                                                <input type="file" name="lampiran" class="form-control" accept=".pdf">
                                                                
                                                                <?php if(!empty($t_m['lampiran'])): ?>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-file-pdf text-danger"></i> File saat ini: 
                                                                            <a href="uploads/<?=$t_m['lampiran'];?>" target="_blank" class="text-decoration-none"><?=$t_m['lampiran'];?></a>
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                                
                                                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                                                    *Pilih file baru jika ingin mengganti lampiran lama. Format harus PDF.
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Keterangan</label>
                                                                <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($t_m['keterangan']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="edit_transaksi_masuk" class="btn btn-warning text-white">Update Data</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="delete<?=$id_tm;?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Hapus Data</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus transaksi ini?
                                                            <input type="hidden" name="id_t_m" value="<?=$id_tm;?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="hapus_transaksi_masuk" class="btn btn-danger">Ya, Hapus</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <div class="modal fade" id="myModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Transaksi Masuk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="kategori" id="kategoriTambah" class="form-select" required>
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    <option value="Pendapatan Jasa Layanan Pendidikan">Layanan Pendidikan</option>
                                    <option value="Pendapatan Bimbingan Penelitian dan Sidang TA">Bimbingan & Sidang</option>
                                    <option value="Pendapatan Hibah">Hibah</option>
                                    <option value="Pendapatan Unit Bisnis">Unit Bisnis</option>
                                    <option value="Pendapatan Lainnya">Lain-Lain</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis</label>
                                <select name="jenis" id="jenisTambah" class="form-select" required>
                                    <option value="">Pilih Kategori Dulu</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Lampiran PDF</label>
                                <input type="file" name="lampiran" class="form-control" accept=".pdf">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="tambah_transaksi_masuk" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        
        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil'): ?>
        <script>
            alert("Data transaksi masuk berhasil ditambah");
            window.history.replaceState({}, document.title, window.location.pathname);
        </script>
        <?php endif; ?>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dataJenis = {
                "Pendapatan Jasa Layanan Pendidikan": ["Dana SKS", "Dana UTS & UAS", "Dana Praktikum", "Dana Tetap", "Dana Pengembangan Fakultas", "Dana Perpustakaan"],
                "Pendapatan Bimbingan Penelitian dan Sidang TA": ["Bimbingan Penelitian", "Sidang Tugas Akhir"],
                "Pendapatan Hibah": ["Hibah Penelitian", "Hibah Pengabdian"],
                "Pendapatan Unit Bisnis": ["Pendapatan Teaching Factory"],
                "Pendapatan Lainnya": ["Pendapatan Bunga Bank BRI", "Pendapatan Bunga Bank Mandiri", "Pendapatan Lain-Lain"]
            };

            function populateJenis(kategoriVal, targetSelect, currentVal = "") {
                targetSelect.innerHTML = "";
                const options = dataJenis[kategoriVal] || [];
                options.forEach(item => {
                    const opt = document.createElement("option");
                    opt.value = item; opt.textContent = item;
                    if(item === currentVal) opt.selected = true;
                    targetSelect.appendChild(opt);
                });
            }

            const katTambah = document.getElementById("kategoriTambah");
            const jenTambah = document.getElementById("jenisTambah");
            if(katTambah) katTambah.addEventListener("change", function() { populateJenis(this.value, jenTambah); });

            document.querySelectorAll(".kategoriEdit").forEach(select => {
                const id = select.getAttribute("data-id");
                const currentJenis = select.getAttribute("data-current-jenis");
                const targetJenisSelect = document.getElementById("jenisEdit" + id);
                populateJenis(select.value, targetJenisSelect, currentJenis);
                select.addEventListener("change", function() { populateJenis(this.value, targetJenisSelect); });
            });

            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                // Pastikan tidak ada inisialisasi ganda
                if (!datatablesSimple.classList.contains("datatable-input")) {
                    new simpleDatatables.DataTable(datatablesSimple, {
                        labels: {
                            placeholder: "Cari data...",
                            perPage: "data per halaman",
                            noRows: "Tidak ada data ditemukan",
                            info: "Menampilkan {start} sampai {end} dari {rows} data",
                        }
                    });
                }
            }
        });
        </script>
    </body>
</html>