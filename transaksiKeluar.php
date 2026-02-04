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
                        <h1 class="mt-4 fw-bold">Transaksi Keluar</h1>
                        <button type="button" class="btn btn-primary mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Transaksi Keluar
                        </button>

                        <div class="card shadow mb-4">
                            <div class="card-body text-dark">
                                <div class="card-header bg-white">
                                    <i class="fas fa-table me-1"></i> Daftar Transaksi Keluar
                                </div>
                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Jenis</th>
                                            <th>Jenis Detail</th> 
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <th>Lampiran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $get = mysqli_query($c, "SELECT * FROM transaksi_keluar ORDER BY tanggal DESC");
                                        $i = 1;
                                        while($t_k = mysqli_fetch_array($get)){
                                            $id_tk = $t_k['id_transaksi_keluar'];
                                            $lampiran = $t_k['lampiran'];
                                        ?>
                                        <tr>
                                            <td class="text-center"><?=$i++;?></td>
                                            <td class="text-center"><?=date('d/m/Y', strtotime($t_k['tanggal']));?></td>
                                            <td class="fw-bold"><?=$t_k['kategori'];?></td>
                                            <td class="fw-bold"><?=$t_k['jenis'];?></td>
                                            <td class="fw-bold"><?=$t_k['jenis_detail'];?></td>
                                            <td class="text-end fw-bold text-danger">Rp<?=number_format($t_k['jumlah']);?></td>
                                            <td><?= htmlspecialchars($t_k['keterangan'] ?: '-'); ?></td>
                                            <td class="text-center">
                                                <?php if($lampiran): ?>
                                                    <a href="uploads/<?=$lampiran;?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf"></i> PDF</a>
                                                <?php else: ?>
                                                    <small class="text-muted">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"> 
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#edit<?=$id_tk;?>"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$id_tk;?>"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="edit<?=$id_tk;?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title">Edit Transaksi Keluar</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post" enctype="multipart/form-data">
                                                        <div class="modal-body text-start">
                                                            <input type="hidden" name="id_t_k" value="<?=$id_tk;?>">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Tanggal</label>
                                                                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d', strtotime($t_k['tanggal'])); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Kategori</label>
                                                                <select name="kategori" class="form-select kategoriEdit" data-id="<?=$id_tk;?>" data-current-jenis="<?=$t_k['jenis'];?>" required>
                                                                    <option value="Belanja Rutin Bidang Akademik" <?=($t_k['kategori'] == 'Belanja Rutin Bidang Akademik') ? 'selected':'';?>>Belanja Rutin Bidang Akademik</option>
                                                                    <option value="Belanja Rutin Bidang Penelitian dan Pengabdian" <?=($t_k['kategori'] == 'Belanja Rutin Bidang Penelitian dan Pengabdian') ? 'selected':'';?>>Belanja Rutin Bidang Penelitian dan Pengabdian</option>
                                                                    <option value="Belanja Rutin Bidang Administrasi dan Umum" <?=($t_k['kategori'] == 'Belanja Rutin Bidang Administrasi dan Umum') ? 'selected':'';?>>Belanja Rutin Bidang Administrasi dan Umum</option>
                                                                    <option value="Belanja Pengembangan" <?=($t_k['kategori'] == 'Belanja Pengembangan') ? 'selected':'';?>>Belanja Pengembangan</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jenis</label>
                                                                <select name="jenis" id="jenisEdit<?=$id_tk;?>" class="form-select" required></select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jenis Detail</label>
                                                                <input type="text" name="jenis_detail" class="form-control" value="<?=$t_k['jenis_detail'];?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Jumlah (Rp)</label>
                                                                <input type="number" name="jumlah" class="form-control" value="<?=$t_k['jumlah'];?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Keterangan</label>
                                                                <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($t_k['keterangan']); ?></textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Update Lampiran PDF (Opsional)</label>
                                                                <input type="file" name="lampiran" class="form-control" accept=".pdf">
                                                                
                                                                <?php if(!empty($t_k['lampiran'])): ?>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-file-pdf text-danger"></i> File saat ini: 
                                                                            <a href="uploads/<?=$t_k['lampiran'];?>" target="_blank" class="text-decoration-none"><?=$t_k['lampiran'];?></a>
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                                
                                                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                                                    *Pilih file baru jika ingin mengganti lampiran lama. Format harus PDF.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="edit_transaksi_keluar" class="btn btn-warning text-white">Update Data</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="delete<?=$id_tk;?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Hapus Data</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            Apakah Anda yakin ingin menghapus transaksi pengeluaran ini?
                                                            <input type="hidden" name="id_t_k" value="<?=$id_tk;?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="hapus_transaksi_keluar" class="btn btn-danger">Ya, Hapus</button>
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
                        <h5 class="modal-title">Tambah Transaksi Keluar</h5>
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
                                    <option value="Belanja Rutin Bidang Akademik">Belanja Rutin Bidang Akademik</option>
                                    <option value="Belanja Rutin Bidang Penelitian dan Pengabdian">Belanja Rutin Bidang Penelitian dan Pengabdian</option>
                                    <option value="Belanja Rutin Bidang Administrasi dan Umum">Belanja Rutin Bidang Administrasi dan Umum</option>
                                    <option value="Belanja Pengembangan">Belanja Pengembangan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis</label>
                                <select name="jenis" id="jenisTambah" class="form-select" required>
                                    <option value="">Pilih Kategori Dulu</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Detail</label>
                                <input type="text" name="jenis_detail" class="form-control" required>
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
                            <button type="submit" name="tambah_transaksi_keluar" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            // DATA DINAMIS SESUAI INSTRUKSI
            const dataJenis = {
                "Belanja Rutin Bidang Akademik": ["Pendidikan", "Kemahasiswaan dan Alumni", "Bagian dari Fakultas", "Perpustakaan", "Laboratorium", "Senat Fakultas", "Informasi dan Humas"],
                "Belanja Rutin Bidang Penelitian dan Pengabdian": ["Bidang Penelitian", "Bidang Pengabdian"],
                "Belanja Rutin Bidang Administrasi dan Umum": ["Belanja Pegawai", "Belanja Barang", "Perjalanan Dinas", "Rapat Dosen dan/ Tim Manajemen", "Utilitas Fakultas", "Kerjasama"],
                "Belanja Pengembangan": ["Pengembangan Penyelenggaraan Pendidikan", "Pengembangan Sumber Daya Manusia (SDM)", "Pengembangan Sarana & Prasarana"]
            };

            function populateJenis(kategoriVal, targetSelect, currentVal = "") {
                targetSelect.innerHTML = "";
                const options = dataJenis[kategoriVal] || [];
                
                if(options.length === 0) {
                    const opt = document.createElement("option");
                    opt.textContent = "Pilih Kategori Dulu";
                    targetSelect.appendChild(opt);
                    return;
                }

                options.forEach(item => {
                    const opt = document.createElement("option");
                    opt.value = item; 
                    opt.textContent = item;
                    if(item === currentVal) opt.selected = true;
                    targetSelect.appendChild(opt);
                });
            }

            // Logic untuk Modal Tambah
            const katTambah = document.getElementById("kategoriTambah");
            const jenTambah = document.getElementById("jenisTambah");
            if(katTambah) {
                katTambah.addEventListener("change", function() { 
                    populateJenis(this.value, jenTambah); 
                });
            }

            // Logic untuk Modal Edit (Multiple)
            document.querySelectorAll(".kategoriEdit").forEach(select => {
                const id = select.getAttribute("data-id");
                const currentJenis = select.getAttribute("data-current-jenis");
                const targetJenisSelect = document.getElementById("jenisEdit" + id);
                
                // Inisialisasi awal saat modal dibuka
                populateJenis(select.value, targetJenisSelect, currentJenis);
                
                // Event saat kategori diubah di modal edit
                select.addEventListener("change", function() { 
                    populateJenis(this.value, targetJenisSelect); 
                });
            });

            // Inisialisasi Datatable
            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                new simpleDatatables.DataTable(datatablesSimple);
            }
        });
        </script>
    </body>
</html>