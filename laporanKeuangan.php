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
                        <h1 class="mt-4 fw-bold">Data Laporan Keuangan</h1>
                        <button type="button" class="btn btn-primary mb-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Laporan Keuangan
                        </button>

                        <div class="card shadow mb-4">
                            <div class="card-body text-dark">
                                <div class="card-header bg-white">
                                    <i class="fas fa-table me-1"></i>
                                Data Laporan Keuangan
                                </div>

                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead class="table light">
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Jenis</th>
                                            <th>Keterangan</th>
                                            <th>Lampiran</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $get = mysqli_query($c, "select * from laporan_keuangan");
                                    $i = 1;

                                    while($l_k=mysqli_fetch_array($get)){
                                        $periode = $l_k['periode'];    
                                        $tanggal = $l_k['tanggal'];    
                                        $kategori = $l_k['kategori'];      
                                        $jenis = $l_k['jenis'];
                                        $keterangan = $l_k['keterangan'];
                                        $lampiran = $l_k['lampiran'];   
                                        $statuz = $l_k['statuz'];   
                                        $id_laporan_keuangan = $l_k['id_laporan_keuangan'];  
                                    ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=$periode;?></td>
                                            <td class="text-center"><?=date('d/m/Y', strtotime($l_k['tanggal']));?></td>
                                            <td><?=$kategori;?></td>
                                            <td><?=$jenis;?></td>
                                            <td><?=$keterangan;?></td>

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
                                                    echo '<span class="badge bg-success">Disetujui</span>';
                                                } elseif ($statuz == 'Revisi') {
                                                    echo '<span class="badge bg-danger mb-1">Revisi</span><br>';
                                                    echo '<a href="#" data-bs-toggle="modal" data-bs-target="#komentar'.$id_laporan_keuangan.'" class="link-alasan">
                                                            <i class="fas fa-comment-dots me-1"></i>Komentar
                                                          </a>';
                                                }
                                                ?>
                                            </td>

                                            <td class="text-center"> 
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#edit<?=$id_laporan_keuangan;?>"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$id_laporan_keuangan;?>"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="edit<?=$id_laporan_keuangan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <!-- Modal Header -->
                                                    <div class="modal-header bg-warning text-white">
                                                        <h4 class="modal-title">Ubah Laporan Nomor <?=$i-1;?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <form method="post" enctype="multipart/form-data">

                                                        <div class="modal-body">

                                                            <!-- Periode (WAJIB) -->
                                                            <select name="periode" class="form-select mt-2" required>
                                                                <option value="" disabled>Pilih Periode</option>
                                                                <?php 
                                                                for ($periode = 2025; $periode <= 2035; $periode++) {
                                                                    echo "<option value='$periode' ".($periode == $l_k['periode'] ? 'selected' : '').">
                                                                            Periode $periode
                                                                        </option>";
                                                                }
                                                                ?>
                                                            </select>

                                                            <!-- Tanggal -->
                                                            <div class="mb-3">
                                                                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d', strtotime($l_k['tanggal'])); ?>" required>
                                                            </div>

                                                            <!-- Kategori (WAJIB) -->
                                                            <select name="kategori"
                                                                    class="form-select mt-2 kategoriSelect"
                                                                    data-id="<?=$id_laporan_keuangan;?>"
                                                                    required>
                                                                <option value="" disabled>Pilih Kategori</option>
                                                                <option value="Laporan Realisasi Anggaran" <?=($kategori=='Laporan Realisasi Anggaran')?'selected':'';?>>
                                                                    Laporan Realisasi Anggaran
                                                                </option>
                                                                <option value="Neraca" <?=($kategori=='Neraca')?'selected':'';?>>
                                                                    Neraca
                                                                </option>
                                                                <option value="Laporan Operasional" <?=($kategori=='Laporan Operasional')?'selected':'';?>>
                                                                    Laporan Operasional
                                                                </option>
                                                            </select>

                                                            <!-- Jenis (WAJIB) -->
                                                            <select name="jenis"
                                                                    class="form-select mt-2 jenisSelect"
                                                                    id="jenisSelect<?=$id_laporan_keuangan;?>"
                                                                    required>
                                                                <option value="<?=$jenis;?>"><?=$jenis;?></option>
                                                            </select>

                                                            <!-- Keterangan (OPSIONAL) -->
                                                            <input type="text"
                                                                name="keterangan"
                                                                class="form-control mt-2"
                                                                placeholder="Keterangan (opsional)"
                                                                value="<?=$keterangan;?>">

                                                            <!-- Lampiran (OPSIONAL saat edit) -->
                                                            <label class="mt-2">Upload File PDF (opsional)</label>
                                                            <input type="file"
                                                                name="lampiran"
                                                                class="form-control"
                                                                accept="application/pdf">

                                                            <input type="hidden" name="id_l_k" value="<?=$id_laporan_keuangan;?>">

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit"
                                                                    class="btn btn-success"
                                                                    name="edit_laporan_keuangan">
                                                                Submit
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-danger"
                                                                    data-dismiss="modal">
                                                                Close
                                                            </button>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Komentar -->
                                        <div class="modal fade" id="komentar<?=$id_laporan_keuangan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Komentar Revisi</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?=$l_k['komentar_revisi_l_k'] ? $l_k['komentar_revisi_l_k'] : '<i>Tidak ada komentar</i>';?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Verifikasi -->
                                        <div class="modal fade" id="verif<?=$id_laporan_keuangan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Verifikasi Laporan</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            Yakin ingin memverifikasi laporan ini?
                                                            <input type="hidden" name="id_l_k" value="<?=$id_laporan_keuangan;?>">
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success" name="verifikasi_laporan">Setujui</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Modal Revisi -->
                                        <div class="modal fade" id="revisi<?=$id_laporan_keuangan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Revisi Laporan</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <label>Berikan Komentar Revisi</label>
                                                            <textarea name="komentar_revisi_l_k" class="form-control" required></textarea>
                                                            <input type="hidden" name="id_l_k" value="<?=$id_laporan_keuangan;?>">
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-dark" name="revisi_laporan">Kirim Revisi</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Modal Delete -->
                                        <div class="modal fade" id="delete<?=$id_laporan_keuangan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h4 class="modal-title">Hapus Laporan <?=$i-1;?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            Apakah Anda yakin akan menghapus laporan keuangan ini?
                                                            <input type="hidden" name="id_l_k" value="<?=$id_laporan_keuangan;?>"> 
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success" name="hapus_laporan_keuangan">Submit</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }; //end of while

                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>

        <!-- Modal Tambah -->
        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h4 class="modal-title">Tambah Laporan Keuangan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="post" enctype="multipart/form-data">

                        <div class="modal-body">

                            <!-- Periode -->
                            <select name="periode" class="form-select mt-2" required>
                                <option value="" disabled selected>Pilih Periode</option>
                                <?php
                                for ($periode = 2025; $periode <= 2035; $periode++) {
                                    echo "<option value='$periode'>Periode $periode</option>";
                                }
                                ?>
                            </select>

                            <!-- Tanggal -->
                            <div class="mb-3">
                                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d', strtotime($l_k['tanggal'])); ?>" required>
                            </div>

                            <!-- Kategori -->
                            <select name="kategori" id="kategoriSelect" class="form-select mt-2" required>
                                <option value="" disabled selected>Pilih Kategori Laporan</option>
                                <option value="Laporan Realisasi Anggaran">Laporan Realisasi Anggaran</option>
                                <option value="Neraca">Neraca</option>
                                <option value="Laporan Operasional">Laporan Operasional</option>
                            </select>

                            <!-- Jenis -->
                            <select name="jenis" id="jenisSelect" class="form-select mt-2" required>
                                <option value="" disabled selected>Pilih Jenis Laporan</option>
                            </select>

                            <!-- Keterangan (OPSIONAL) -->
                            <input type="text" name="keterangan" class="form-control mt-2" placeholder="Keterangan (opsional)">

                            <!-- Lampiran (WAJIB) -->
                            <label class="mt-2">Upload File PDF <span class="text-danger">*</span></label>
                            <input type="file"
                                name="lampiran"
                                class="form-control"
                                accept="application/pdf"
                                required>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" name="tambah_laporan_keuangan">Submit</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>


        <!-- Script Dinamis -->
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const jenisOptions = {
                "Laporan Realisasi Anggaran": [
                    "Realisasi Pendapatan",
                    "Realisasi Belanja Pendidikan",
                    "Realisasi Belanja Penelitian dan Pengabdian",
                    "Realisasi Belanja Administrasi dan Umum",
                    "Realisasi Belanja Pengembangan"
                ],
                "Neraca": [
                    "Nilai Asset pertahun anggaran",
                    "Ekuitas Dana pertahun anggaran",
                    "Jumlah kewajiban dan ekuitas"
                ],
                "Laporan Operasional": [
                    "Pendapatan",
                    "Beban",
                    "Surplus/Defisit dari Operasi",
                    "Surplus/Defisit dari Kegiatan Non Operasional"
                ]
            };

            // Untuk Modal Tambah
            const kategoriSelectTambah = document.getElementById('kategoriSelect');
            const jenisSelectTambah = document.getElementById('jenisSelect');
            if (kategoriSelectTambah && jenisSelectTambah) {
                kategoriSelectTambah.addEventListener('change', function() {
                    const selectedKategori = this.value;
                    const options = jenisOptions[selectedKategori] || [];
                    jenisSelectTambah.innerHTML = '<option value="" disabled selected>Pilih Jenis Laporan</option>';
                    options.forEach(function(jenis) {
                        const option = document.createElement('option');
                        option.value = jenis;
                        option.textContent = jenis;
                        jenisSelectTambah.appendChild(option);
                    });
                });
            }

            // Untuk Modal Edit
            document.querySelectorAll('.kategoriSelect').forEach(function(select) {
                select.addEventListener('change', function() {
                    const id = this.dataset.id || '';
                    const jenisSelect = document.getElementById('jenisSelect' + id);
                    if (!jenisSelect) return;
                    const selectedKategori = this.value;
                    const options = jenisOptions[selectedKategori] || [];
                    jenisSelect.innerHTML = '<option value="" disabled selected>Pilih Jenis Laporan</option>';
                    options.forEach(function(jenis) {
                        const option = document.createElement('option');
                        option.value = jenis;
                        option.textContent = jenis;
                        jenisSelect.appendChild(option);
                    });
                });
            });
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

    </script>

    </body>
</html>
