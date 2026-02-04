<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Panggil koneksi dari file terpisah
require 'koneksi.php';

// cek login
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// 📌 Ambil ID Mahasiswa dari SESSION untuk disimpan ke database
// Pastikan variabel 'user_id' sudah tersimpan di SESSION saat proses login
$id_mahasiswa_session = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$nama_mahasiswa_session = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Mahasiswa Tanpa Nama';


// FUNCTION TAMBAH TRANSAKSI MASUK 
if (isset($_POST['tambah_transaksi_masuk'])) {

    // ===== TRIM INPUT =====
    foreach ($_POST as $k => $v) {
        $_POST[$k] = trim($v);
    }

    // ===== VALIDASI FIELD WAJIB =====
    $required = ['tanggal', 'kategori', 'jenis', 'jumlah'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Error: Field <b>$field</b> wajib diisi.");
        }
    }

    // ===== VALIDASI JUMLAH =====
    if (!is_numeric($_POST['jumlah']) || $_POST['jumlah'] <= 0) {
        die("Error: Jumlah harus berupa angka lebih dari 0.");
    }

    // ===== ASSIGN VAR =====
    $tanggal    = $_POST['tanggal'];
    $kategori   = $_POST['kategori'];
    $jenis      = $_POST['jenis'];
    $jumlah     = (int) $_POST['jumlah'];

    // keterangan OPSIONAL
    $keterangan = $_POST['keterangan'] ?? '';

    // ===== HANDLE FILE (OPSIONAL) =====
    $lampiran = null;

    if (!empty($_FILES['lampiran']['name'])) {

        $allowedMime = 'application/pdf';
        $tmpName = $_FILES['lampiran']['tmp_name'];

        // Cek MIME type asli
        if (mime_content_type($tmpName) !== $allowedMime) {
            die("Error: Lampiran harus berupa file PDF.");
        }

        // Buat nama file aman & unik
        $lampiran = time() . '_' . uniqid() . '.pdf';
        $uploadDir = 'uploads/';
        $uploadPath = $uploadDir . $lampiran;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($tmpName, $uploadPath)) {
            die("Error: Upload file gagal.");
        }
    }

    // ===== PREPARED STATEMENT =====
    if ($lampiran) {
        $stmt = mysqli_prepare(
            $c,
            "INSERT INTO transaksi_masuk 
            (tanggal, kategori, jenis, jumlah, keterangan, lampiran)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sssiss",
            $tanggal,
            $kategori,
            $jenis,
            $jumlah,
            $keterangan,
            $lampiran
        );
    } else {
        $stmt = mysqli_prepare(
            $c,
            "INSERT INTO transaksi_masuk 
            (tanggal, kategori, jenis, jumlah, keterangan)
            VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sssis",
            $tanggal,
            $kategori,
            $jenis,
            $jumlah,
            $keterangan
        );
    }

    // ===== EKSEKUSI =====
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Transaksi masuk berhasil ditambahkan');
                window.location.href='transaksiMasuk.php';
            </script>";
        exit;
    } else {
        die("Database Error: " . mysqli_error($c));
    }
}

// FUNCTION EDIT TRANSAKSI MASUK 
if (isset($_POST['edit_transaksi_masuk'])) {

    // ===== TRIM INPUT =====
    foreach ($_POST as $k => $v) {
        $_POST[$k] = trim($v);
    }

    // ===== VALIDASI FIELD WAJIB =====
    $required = ['id_t_m', 'tanggal', 'kategori', 'jenis', 'jumlah'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Error: Field <b>$field</b> wajib diisi.");
        }
    }

    // ===== VALIDASI ID =====
    if (!ctype_digit($_POST['id_t_m'])) {
        die("Error: ID transaksi tidak valid.");
    }

    // ===== VALIDASI JUMLAH =====
    if (!is_numeric($_POST['jumlah']) || $_POST['jumlah'] <= 0) {
        die("Error: Jumlah harus berupa angka lebih dari 0.");
    }

    // ===== ASSIGN VAR =====
    $id_t_m     = (int) $_POST['id_t_m'];
    $tanggal    = $_POST['tanggal'];
    $kategori   = $_POST['kategori'];
    $jenis      = $_POST['jenis'];
    $jumlah     = (int) $_POST['jumlah'];

    // keterangan OPSIONAL
    $keterangan = $_POST['keterangan'] ?? '';

    // ===== HANDLE FILE (OPSIONAL) =====
    $lampiran = null;

    if (!empty($_FILES['lampiran']['name'])) {

        $tmpName = $_FILES['lampiran']['tmp_name'];
        $mime    = mime_content_type($tmpName);

        if ($mime !== 'application/pdf') {
            die("Error: Lampiran harus berupa file PDF.");
        }

        // Nama file aman & unik
        $lampiran = time() . '_' . uniqid() . '.pdf';
        $uploadDir  = __DIR__ . '/uploads/';
        $uploadPath = $uploadDir . $lampiran;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($tmpName, $uploadPath)) {
            die("Error: Upload file gagal.");
        }
    }

    // ===== PREPARED STATEMENT =====
    if ($lampiran) {
        $stmt = mysqli_prepare(
            $c,
            "UPDATE transaksi_masuk
             SET tanggal = ?, kategori = ?, jenis = ?, jumlah = ?, keterangan = ?, lampiran = ?
             WHERE id_transaksi_masuk = ?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sssissi",
            $tanggal,
            $kategori,
            $jenis,
            $jumlah,
            $keterangan,
            $lampiran,
            $id_t_m
        );
    } else {
        $stmt = mysqli_prepare(
            $c,
            "UPDATE transaksi_masuk
             SET tanggal = ?, kategori = ?, jenis = ?, jumlah = ?, keterangan = ?
             WHERE id_transaksi_masuk = ?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "sssisi",
            $tanggal,
            $kategori,
            $jenis,
            $jumlah,
            $keterangan,
            $id_t_m
        );
    }

    // ===== EKSEKUSI =====
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Transaksi masuk berhasil diupdate');
                window.location.href='transaksiMasuk.php';
              </script>";
        exit;
    } else {
        die("Database Error: " . mysqli_error($c));
    }
}


//hapus transaksi masuk
if(isset($_POST['hapus_transaksi_masuk'])){
    $id_t_m = $_POST['id_t_m'];

    // hapus file pdf dulu
    $cek_file = mysqli_query($c, "SELECT lampiran FROM transaksi_masuk WHERE id_transaksi_masuk='$id_t_m'");
    $data_file = mysqli_fetch_assoc($cek_file);
    if($data_file && $data_file['lampiran'] != ""){
        $file_path = "uploads/".$data_file['lampiran'];
        if(file_exists($file_path)){
            unlink($file_path); //hapus file fisik
        }
    }

    $query = mysqli_query($c, "DELETE FROM transaksi_masuk WHERE id_transaksi_masuk='$id_t_m'");

    if($query){
        echo ' 
        <script>
            alert("Transaksi masuk berhasil dihapus");
            window.location.href="transaksiMasuk.php";
        </script>
        ';
    } else {
        echo ' 
        <script>
            alert("Gagal hapus transaksi masuk");
            window.location.href="transaksiMasuk.php";
        </script>
        ';
    }
}

// Function menambah transaksi keluar
if (isset($_POST['tambah_transaksi_keluar'])) {

    // =====================
    // VALIDASI INPUT WAJIB
    // =====================
    $tanggal      = trim($_POST['tanggal']);
    $kategori     = trim($_POST['kategori']);
    $jenis        = trim($_POST['jenis']);
    $jenis_detail = trim($_POST['jenis_detail']);
    $jumlah       = (int) $_POST['jumlah'];
    $keterangan   = trim($_POST['keterangan'] ?? '');

    if (
        empty($tanggal) ||
        empty($kategori) ||
        empty($jenis) ||
        empty($jenis_detail) ||
        $jumlah <= 0
    ) {
        die("Input wajib tidak boleh kosong.");
    }

    // =====================
    // HANDLE FILE (OPSIONAL)
    // =====================
    $lampiran = null;

    if (!empty($_FILES['lampiran']['name'])) {

        $allowed = ['pdf'];
        $ext = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("File harus PDF.");
        }

        if ($_FILES['lampiran']['size'] > 2 * 1024 * 1024) {
            die("Ukuran file maksimal 2MB.");
        }

        $lampiran = time() . '_' . uniqid() . '.pdf';
        $target   = __DIR__ . '/uploads/' . $lampiran;

        if (!move_uploaded_file($_FILES['lampiran']['tmp_name'], $target)) {
            die("Gagal upload file.");
        }
    }

    // =====================
    // INSERT DATABASE
    // =====================
    if ($lampiran) {
        $stmt = $c->prepare("
            INSERT INTO transaksi_keluar
            (tanggal, kategori, jenis, jenis_detail, jumlah, keterangan, lampiran)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssiss",
            $tanggal,
            $kategori,
            $jenis,
            $jenis_detail,
            $jumlah,
            $keterangan,
            $lampiran
        );
    } else {
        $stmt = $c->prepare("
            INSERT INTO transaksi_keluar
            (tanggal, kategori, jenis, jenis_detail, jumlah, keterangan)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssis",
            $tanggal,
            $kategori,
            $jenis,
            $jenis_detail,
            $jumlah,
            $keterangan
        );
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('Transaksi keluar berhasil ditambahkan');
                window.location.href='transaksiKeluar.php';
            </script>";
        exit;
    } else {
        echo "<script>
                alert('Gagal menambahkan transaksi keluar');
                window.location.href='transaksiKeluar.php';
            </script>";
        exit;
    }
}


//Function edit transaksi keluar
if (isset($_POST['edit_transaksi_keluar'])) {

    $id_t_k       = (int) $_POST['id_t_k'];
    $tanggal      = trim($_POST['tanggal']);
    $kategori     = trim($_POST['kategori']);
    $jenis        = trim($_POST['jenis']);
    $jenis_detail = trim($_POST['jenis_detail']);
    $jumlah       = (int) $_POST['jumlah'];
    $keterangan   = trim($_POST['keterangan'] ?? '');

    // Validasi Input
    if ($id_t_k <= 0 || empty($tanggal) || empty($kategori) || empty($jenis) || empty($jenis_detail) || $jumlah <= 0) {
        die("Input tidak valid.");
    }

    if (!empty($_FILES['lampiran']['name'])) {
        // 1. Validasi File Baru
        $allowed = ['pdf'];
        $ext = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("File harus PDF.");
        }

        // 2. Ambil nama file lama dari database untuk dihapus
        $query_old = $c->prepare("SELECT lampiran FROM transaksi_keluar WHERE id_transaksi_keluar = ?");
        $query_old->bind_param("i", $id_t_k);
        $query_old->execute();
        $result_old = $query_old->get_result();
        $data_old = $result_old->fetch_assoc();
        
        // Hapus file lama jika ada di folder
        if (!empty($data_old['lampiran'])) {
            $old_file_path = __DIR__ . '/uploads/' . $data_old['lampiran'];
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }

        // 3. Proses Upload File Baru
        $lampiran = time() . '_' . uniqid() . '.pdf';
        $target   = __DIR__ . '/uploads/' . $lampiran;

        if (!move_uploaded_file($_FILES['lampiran']['tmp_name'], $target)) {
            die("Gagal upload file.");
        }

        // 4. Update dengan Lampiran Baru
        $stmt = $c->prepare("
            UPDATE transaksi_keluar SET
                tanggal = ?, kategori = ?, jenis = ?, jenis_detail = ?,
                jumlah = ?, keterangan = ?, lampiran = ?
            WHERE id_transaksi_keluar = ?
        ");
        $stmt->bind_param("ssssissi", $tanggal, $kategori, $jenis, $jenis_detail, $jumlah, $keterangan, $lampiran, $id_t_k);

    } else {
        // Update tanpa mengubah lampiran
        $stmt = $c->prepare("
            UPDATE transaksi_keluar SET
                tanggal = ?, kategori = ?, jenis = ?, jenis_detail = ?,
                jumlah = ?, keterangan = ?
            WHERE id_transaksi_keluar = ?
        ");
        $stmt->bind_param("ssssisi", $tanggal, $kategori, $jenis, $jenis_detail, $jumlah, $keterangan, $id_t_k);
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('Transaksi keluar berhasil diperbarui');
                window.location.href='transaksiKeluar.php';
            </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui data: " . addslashes($stmt->error) . "');
                window.location.href='transaksiKeluar.php';
            </script>";
    }
    exit;
}


//hapus transaksi keluar
if(isset($_POST['hapus_transaksi_keluar'])){
    $id_t_k = $_POST['id_t_k'];

    // hapus file pdf dulu
    $cek_file = mysqli_query($c, "SELECT lampiran FROM transaksi_keluar WHERE id_transaksi_keluar='$id_t_k'");
    $data_file = mysqli_fetch_assoc($cek_file);
    if($data_file && $data_file['lampiran'] != ""){
        $file_path = "uploads/".$data_file['lampiran'];
        if(file_exists($file_path)){
            unlink($file_path); //hapus file fisik
        }
    }

    $query = mysqli_query($c, "DELETE FROM transaksi_keluar WHERE id_transaksi_keluar='$id_t_k'");

    if($query){
        echo ' 
        <script>
            alert("Transaksi keluar berhasil dihapus");
            window.location.href="transaksiKeluar.php";
        </script>
        ';
    } else {
        echo ' 
        <script>
            alert("Gagal hapus transaksi keluar");
            window.location.href="transaksiKeluar.php";
        </script>
        ';
    }
}


// === TAMBAH RAPBF ===
if (isset($_POST['tambah_rapbf'])) {
    // 1. Pastikan session ada
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Sesi habis, silakan login kembali'); window.location.href='login.php';</script>";
        exit;
    }

    $id_prodi = $_SESSION['user_id']; 
    $nama = mysqli_real_escape_string($c, $_POST['nama']);
    $periode = mysqli_real_escape_string($c, $_POST['periode']);
    $total_anggaran = mysqli_real_escape_string($c, $_POST['total_anggaran']);
    $keterangan = mysqli_real_escape_string($c, $_POST['keterangan'] ?? '');

    // 2. Kelola File
    $tmp = $_FILES['lampiran']['tmp_name'];
    $ext = pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION);
    
    if (strtolower($ext) !== 'pdf') {
        echo "<script>alert('Lampiran harus PDF'); window.history.back();</script>";
        exit;
    }

    $lampiran = 'RAPBF_' . time() . '.pdf';
    $dir = 'file_rapbf/';
    
    if (move_uploaded_file($tmp, $dir . $lampiran)) {
        // 3. Eksekusi Query
        // Menambahkan kolom statuz dengan nilai default 'Diajukan'
        $query = "INSERT INTO rapbf (id_prodi, nama, periode, total_anggaran, tanggal, keterangan, lampiran, statuz) 
                  VALUES ('$id_prodi', '$nama', '$periode', '$total_anggaran', NOW(), '$keterangan', '$lampiran', 'Diajukan')";

        $exec = mysqli_query($c, $query);

        if ($exec) {
            echo "<script>alert('RAPBF Berhasil ditambahkan!'); window.location.href='rapbf.php';</script>";
        } else {
            echo "Error Database: " . mysqli_error($c);
        }
    } else {
        echo "<script>alert('Gagal upload file');</script>";
    }
}

// === EDIT RAPBF ===
if (isset($_POST['edit_rapbf'])) {
    $id_rap = $_POST['id_rap'];
    $nama = $_POST['nama'];
    $periode = $_POST['periode'];
    $total_anggaran = $_POST['total_anggaran'];
    $keterangan = $_POST['keterangan'] ?? '';
    $dir = 'file_rapbf/'; // Menggunakan path relatif yang sama dengan Tambah

    // ===== FILE BARU (OPSIONAL) =====
    $lampiran_baru = null;
    if (!empty($_FILES['lampiran']['name'])) {
        $tmp = $_FILES['lampiran']['tmp_name'];
        $ext = pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION);
        
        if (strtolower($ext) === 'pdf') {
            // 1. Ambil nama file lama untuk dihapus dari folder
            $cek = mysqli_query($c, "SELECT lampiran FROM rapbf WHERE id_rapbf='$id_rap'");
            $d = mysqli_fetch_assoc($cek);
            if ($d && !empty($d['lampiran'])) {
                $file_lama = $dir . $d['lampiran'];
                if (file_exists($file_lama)) {
                    unlink($file_lama); // Hapus file fisik
                }
            }

            // 2. Siapkan file baru
            $lampiran_baru = 'RAPBF_UPDATE_' . time() . '_' . uniqid() . '.pdf';
            move_uploaded_file($tmp, $dir . $lampiran_baru);
        } else {
            echo "<script>alert('Format file harus PDF!'); window.history.back();</script>";
            exit;
        }
    }

    // ===== UPDATE LOGIC (TANGGAL DIHAPUS AGAR TIDAK BERUBAH) =====
    if ($lampiran_baru) {
        // Update data beserta file baru
        $query = "UPDATE rapbf SET 
                  nama='$nama', 
                  periode='$periode', 
                  total_anggaran='$total_anggaran', 
                  keterangan='$keterangan', 
                  lampiran='$lampiran_baru' 
                  WHERE id_rapbf='$id_rap'";
    } else {
        // Update data saja (file lama tetap)
        $query = "UPDATE rapbf SET 
                  nama='$nama', 
                  periode='$periode', 
                  total_anggaran='$total_anggaran', 
                  keterangan='$keterangan' 
                  WHERE id_rapbf='$id_rap'";
    }

    $exec = mysqli_query($c, $query);

    if ($exec) {
        echo "<script>alert('Berhasil memperbarui data RAPBF'); window.location.href='rapbf.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($c) . "');</script>";
    }
}

// =====================================================
// VERIFIKASI / SETUJUI RAPBF
// =====================================================
if (isset($_POST['verifikasi_setuju'])) { 

    // Pastikan hanya admin/role yang berwenang (sesuaikan nama role Anda)
    if ($_SESSION['role'] !== 'wakil_dekan_dua') {
        die("Akses ditolak!");
    }

    $id_rapbf = $_POST['id_rapbf'];

    // Menggunakan Prepared Statement untuk keamanan
    $stmt = $c->prepare("UPDATE rapbf SET 
                statuz = 'Disetujui',
                komentar_rapbf = NULL
            WHERE id_rapbf = ?");

    $stmt->bind_param("i", $id_rapbf);
    $query = $stmt->execute();

    if (!$query) {
        die("SQL Error (verifikasi): " . $stmt->error);
    }

    $stmt->close();

    echo "<script>
            alert('Data RAPBF berhasil disetujui!');
            window.location.href='verifikasiRapbf.php';
          </script>";
}


// =====================================================
// KIRIM REVISI RAPBF
// =====================================================
if (isset($_POST['verifikasi_revisi'])) { 

    // Pastikan hanya admin/role yang berwenang
    if ($_SESSION['role'] !== 'wakil_dekan_dua') {
        die("Akses ditolak!");
    }

    $id_rapbf = $_POST['id_rapbf'];
    $komentar = trim($_POST['komentar_rapbf']); 

    // Menggunakan Prepared Statement
    $stmt = $c->prepare("UPDATE rapbf SET 
                statuz = 'Revisi',
                komentar_rapbf = ?
            WHERE id_rapbf = ?");

    $stmt->bind_param("si", $komentar, $id_rapbf);
    $query = $stmt->execute();

    if (!$query) {
        die("SQL Error (revisi): " . $stmt->error);
    }

    $stmt->close();

    echo "<script>
            alert('Catatan revisi berhasil dikirim!');
            window.location.href='verifikasiRapbf.php';
          </script>";
}

// === HAPUS RAPBF ===
if(isset($_POST['hapus_rapbf'])){
    $id_rapbf = $_POST['id_rap'];
    $dir = "file_rapbf/";

    // hapus file fisik
    $cek_file = mysqli_query($c, "SELECT lampiran FROM rapbf WHERE id_rapbf='$id_rapbf'");
    $data_file = mysqli_fetch_assoc($cek_file);
    if($data_file && file_exists($dir . $data_file['lampiran'])){
        unlink($dir . $data_file['lampiran']);
    }

    if (mysqli_query($c, "DELETE FROM rapbf WHERE id_rapbf='$id_rapbf'")) {
        echo "
        <script>
            alert('Data RAPBF berhasil dihapus');
            window.location.href='rapbf.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('Gagal menghapus data: " . addslashes(mysqli_error($c)) . "');
            window.location.href='rapbf.php';
        </script>
        ";
    }
    exit;
}



// === TAMBAH BUKTI PEMBAYARAN ===
if (isset($_POST['tambah_bukti_pembayaran'])) {
    
    // Pastikan session aktif
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Cek ID Mahasiswa dari beberapa kemungkinan nama session (id_mahasiswa atau user_id)
    $id_mhs = $_SESSION['id_mahasiswa'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
    
    // Jika tetap kosong, baru kita hentikan
    if (!$id_mhs) {
        die("Error: Sesi tidak ditemukan. Isi session saat ini: " . print_r($_SESSION, true));
    }

    $npm        = $_SESSION['username'];
    $nama       = $_SESSION['nama'];
    $jumlah     = trim($_POST['jumlah']);
    $keterangan = trim($_POST['keterangan']);
    $statuz     = "Diajukan";

    // Validasi input
    if (empty($jumlah) || empty($keterangan)) {
        echo "<script>alert('Jumlah dan Keterangan wajib diisi!'); window.history.back();</script>";
        exit;
    }

    // Upload File
    if (empty($_FILES['lampiran']['name'])) {
        echo "<script>alert('File bukti wajib diunggah!'); window.history.back();</script>";
        exit;
    }

    $lampiran = time() . "_" . $_FILES['lampiran']['name'];
    if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);

    if (move_uploaded_file($_FILES['lampiran']['tmp_name'], "uploads/" . $lampiran)) {
        
        // Gunakan NOW() di dalam Query agar tanggal otomatis terisi
        $stmt = $c->prepare("INSERT INTO bukti_pembayaran 
            (id_mahasiswa, npm, nama, jumlah, keterangan, statuz, lampiran, tanggal)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

        // Bind Param (7 parameter: i s s i s s s)
        $stmt->bind_param("ississs", 
            $id_mhs, 
            $npm, 
            $nama, 
            $jumlah, 
            $keterangan, 
            $statuz, 
            $lampiran
        );

        if ($stmt->execute()) {
            echo "<script>alert('Bukti Pembayaran berhasil diajukan!'); window.location.href='buktiPembayaran.php';</script>";
        } else {
            echo "Gagal Simpan: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Gagal upload file!'); window.history.back();</script>";
    }
}

// === EDIT BUKTI PEMBAYARAN ===
if (isset($_POST['edit_bukti_pembayaran'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $id_b_p       = $_POST['id_b_p'];
    $npm          = $_SESSION['username']; 
    $nama         = $_SESSION['nama'];     
    $jumlah       = trim($_POST['jumlah']);
    $keterangan   = trim($_POST['keterangan']);
    
    // Pastikan variabel ID Mahasiswa sinkron dengan session (sesuaikan nama variabelnya)
    $id_mahasiswa_session = $_SESSION['id_mahasiswa'] ?? $_SESSION['user_id'] ?? $_SESSION['id'];

    if (empty($id_b_p) || empty($jumlah) || empty($keterangan)) {
        echo "<script>alert('Data tidak lengkap!'); window.location.href='buktiPembayaran.php';</script>";
        exit;
    }

    if (!empty($_FILES['lampiran_baru']['name'])) {
        // --- PROSES DENGAN FILE BARU ---
        $lampiran_baru = time() . "_" . $_FILES['lampiran_baru']['name'];
        $tmp_name      = $_FILES['lampiran_baru']['tmp_name'];
        $lampiran_lama = $_POST['lampiran_lama'];

        if (move_uploaded_file($tmp_name, "uploads/" . $lampiran_baru)) {
            if (!empty($lampiran_lama) && file_exists("uploads/" . $lampiran_lama)) {
                unlink("uploads/" . $lampiran_lama);
            }
        }

        // TAMBAHKAN tanggal=NOW() di sini
        $stmt = $c->prepare("UPDATE bukti_pembayaran SET
                npm=?, nama=?, jumlah=?, keterangan=?, lampiran=?,
                tanggal=NOW(), 
                statuz='Diajukan', komentar_revisi=NULL
            WHERE id_bukti_pembayaran=? AND id_mahasiswa=?"); 
        
        $stmt->bind_param("ssisssi",
            $npm, $nama, $jumlah, $keterangan, $lampiran_baru, $id_b_p, $id_mahasiswa_session
        );
        $query = $stmt->execute();
        $stmt->close();

    } else {
        // --- PROSES TANPA GANTI FILE ---
        // TAMBAHKAN tanggal=NOW() di sini juga
        $stmt = $c->prepare("UPDATE bukti_pembayaran SET
                npm=?, nama=?, jumlah=?, keterangan=?, 
                tanggal=NOW(),
                statuz='Diajukan', komentar_revisi=NULL
            WHERE id_bukti_pembayaran=? AND id_mahasiswa=?"); 
        
        $stmt->bind_param("ssisii",
            $npm, $nama, $jumlah, $keterangan, $id_b_p, $id_mahasiswa_session
        );
        $query = $stmt->execute();
        $stmt->close();
    }

    if ($query) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='buktiPembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.'); window.location.href='buktiPembayaran.php';</script>";
    }
}


// =====================================================
// VERIFIKASI BUKTI PEMBAYARAN
// =====================================================
if (isset($_POST['verifikasi_bukti_pembayaran'])) { // 📌 Ubah nama tombol dari 'verifikasi'

    if ($_SESSION['role'] !== 'staf_keuangan') {
        die("Akses ditolak!");
    }

    $id_b_p = $_POST['id_b_p'];

    // 📌 Menggunakan Prepared Statement
    $stmt = $c->prepare("UPDATE bukti_pembayaran SET 
                statuz = 'Disetujui',
                komentar_revisi = NULL
            WHERE id_bukti_pembayaran = ?");

    $stmt->bind_param("i", $id_b_p);
    $query = $stmt->execute();

    if (!$query) {
        die("SQL Error (verifikasi): " . $stmt->error);
    }

    $stmt->close();

    echo "<script>
            alert('Bukti Pembayaran berhasil diverifikasi!');
            window.location.href='verifikasiBuktiPembayaran.php';
          </script>";
}


// =====================================================
// KIRIM REVISI BUKTI PEMBAYARAN
// =====================================================
if (isset($_POST['revisi_bukti_pembayaran'])) { // 📌 Ubah nama tombol dari 'kirimRevisi'

    if ($_SESSION['role'] !== 'staf_keuangan') {
        die("Akses ditolak!");
    }

    $id_b_p   = $_POST['id_b_p'];
    $komentar = trim($_POST['komentar_revisi']); // 📌 Sesuaikan nama input text area di modal

    // 📌 Menggunakan Prepared Statement
    $stmt = $c->prepare("UPDATE bukti_pembayaran SET 
                statuz = 'Revisi',
                komentar_revisi = ?
            WHERE id_bukti_pembayaran = ?");

    $stmt->bind_param("si", $komentar, $id_b_p);
    $query = $stmt->execute();

    if (!$query) {
        die("SQL Error (revisi): " . $stmt->error);
    }

    $stmt->close();

    echo "<script>
            alert('Revisi berhasil dikirim!');
            window.location.href='verifikasiBuktiPembayaran.php';
          </script>";
}

// === HAPUS BUKTI PEMBAYARAN ===
if(isset($_POST['hapus_bukti_pembayaran'])){
    
    // Cek hak akses: hanya Mahasiswa pemilik data yang bisa hapus
    if ($_SESSION['role'] !== 'mahasiswa') {
        die("Akses ditolak. Hanya Mahasiswa yang dapat menghapus datanya sendiri.");
    }
    
    $id_b_p = $_POST['id_b_p'];

    // 📌 Mencegah Mahasiswa menghapus data yang sudah Disetujui
    $cek_status = mysqli_query($c, "SELECT statuz FROM bukti_pembayaran WHERE id_bukti_pembayaran='$id_b_p'");
    $data_status = mysqli_fetch_assoc($cek_status);
    if($data_status && $data_status['statuz'] == 'Disetujui'){
        echo "<script>alert('Data yang sudah Disetujui (Lunas) tidak dapat dihapus!'); window.location.href='buktiPembayaran.php';</script>";
        exit;
    }


    // Menggunakan Prepared Statement untuk hapus file
    $stmt_file = $c->prepare("SELECT lampiran FROM bukti_pembayaran WHERE id_bukti_pembayaran=? AND id_mahasiswa=?");
    $stmt_file->bind_param("ii", $id_b_p, $id_mahasiswa_session);
    $stmt_file->execute();
    $result = $stmt_file->get_result();
    $data_file = $result->fetch_assoc();
    $stmt_file->close();
    
    if($data_file){
        $file_path = "uploads/".$data_file['lampiran'];
        if($data_file['lampiran'] != "" && file_exists($file_path)){
            unlink($file_path); // hapus file fisik
        }
    } else {
        // Jika data_file kosong, artinya ID tidak ditemukan atau Mahasiswa bukan pemilik
        echo "<script>alert('Data tidak ditemukan atau Anda bukan pemilik data.'); window.location.href='buktiPembayaran.php';</script>";
        exit;
    }

    // Menggunakan Prepared Statement untuk DELETE
    $stmt_delete = $c->prepare("DELETE FROM bukti_pembayaran WHERE id_bukti_pembayaran=? AND id_mahasiswa=?");
    $stmt_delete->bind_param("ii", $id_b_p, $id_mahasiswa_session);
    $query = $stmt_delete->execute();
    $stmt_delete->close();

    if($query){
        echo "<script>alert('Data Bukti Pembayaran berhasil dihapus'); window.location.href='buktiPembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus data Bukti Pembayaran. Anda mungkin tidak memiliki izin.'); window.location.href='buktiPembayaran.php';</script>";
    }
}

// === TAMBAH LAPORAN KEUANGAN ===
if (isset($_POST['tambah_laporan_keuangan'])) {

    // Proteksi role
    if ($_SESSION['role'] !== 'staf_keuangan') {
        die("Akses ditolak!");
    }

    $periode    = (int) $_POST['periode'];
    $tanggal    = trim($_POST['tanggal']);
    $kategori   = trim($_POST['kategori']);
    $jenis      = trim($_POST['jenis']);
    $keterangan = trim($_POST['keterangan'] ?? '');
    $statuz     = "Diajukan";

    // =====================
    // VALIDASI FIELD WAJIB
    // =====================
    if (
        $periode <= 0 ||
        empty($tanggal) ||
        empty($kategori) ||
        empty($jenis)
    ) {
        die("Data wajib tidak boleh kosong.");
    }

    // =====================
    // VALIDASI FILE (WAJIB)
    // =====================
    if (empty($_FILES['lampiran']['name'])) {
        die("File laporan wajib diupload.");
    }

    $ext = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        die("File harus berformat PDF.");
    }

    if ($_FILES['lampiran']['size'] > 2 * 1024 * 1024) {
        die("Ukuran file maksimal 2MB.");
    }

    // Generate nama file aman
    $lampiran = time() . "_" . uniqid() . ".pdf";
    $target   = __DIR__ . "/uploads/" . $lampiran;

    if (!move_uploaded_file($_FILES['lampiran']['tmp_name'], $target)) {
        die("Gagal upload file.");
    }

    // =====================
    // INSERT DATABASE
    // =====================
    $insert = mysqli_query($c, "
        INSERT INTO laporan_keuangan
        (periode, tanggal, kategori, jenis, keterangan, statuz, lampiran)
        VALUES
        ('$periode', '$tanggal', '$kategori', '$jenis', '$keterangan', '$statuz', '$lampiran')
    ");

    if ($insert) {
        echo "<script>
                alert('Laporan keuangan berhasil ditambahkan');
                window.location.href='laporanKeuangan.php';
            </script>";
        exit;
    } else {
        die("Query Error: " . mysqli_error($c));
    }
}


// === EDIT LAPORAN KEUANGAN ===
if (isset($_POST['edit_laporan_keuangan'])) {

    // Proteksi role
    if ($_SESSION['role'] !== 'staf_keuangan') {
        die("Akses ditolak!");
    }

    $id_laporan_keuangan = (int) $_POST['id_l_k'];
    $periode    = (int) $_POST['periode'];
    $tanggal    = trim($_POST['tanggal']);
    $kategori   = trim($_POST['kategori']);
    $jenis      = trim($_POST['jenis']);
    $keterangan = trim($_POST['keterangan'] ?? '');

    // =====================
    // VALIDASI FIELD WAJIB
    // =====================
    if (
        $id_laporan_keuangan <= 0 ||
        $periode <= 0 ||
        empty($tanggal) ||
        empty($kategori) ||
        empty($jenis)
    ) {
        die("Data wajib tidak boleh kosong.");
    }

    // =====================
    // CEK FILE BARU (OPSIONAL)
    // =====================
    if (!empty($_FILES['lampiran']['name'])) {

        $ext = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            die("File harus PDF.");
        }

        if ($_FILES['lampiran']['size'] > 2 * 1024 * 1024) {
            die("Ukuran file maksimal 2MB.");
        }

        $lampiran_baru = time() . "_" . uniqid() . ".pdf";
        $target        = __DIR__ . "/uploads/" . $lampiran_baru;

        if (!move_uploaded_file($_FILES['lampiran']['tmp_name'], $target)) {
            die("Gagal upload file.");
        }

        // Ambil file lama
        $old = mysqli_query($c, "
            SELECT lampiran 
            FROM laporan_keuangan 
            WHERE id_laporan_keuangan='$id_laporan_keuangan'
        ");
        $oldFile = mysqli_fetch_assoc($old)['lampiran'];

        // Hapus file lama
        if (!empty($oldFile) && file_exists(__DIR__ . "/uploads/" . $oldFile)) {
            unlink(__DIR__ . "/uploads/" . $oldFile);
        }

        // Update dengan file baru
        $query = mysqli_query($c, "
            UPDATE laporan_keuangan SET
                periode='$periode',
                tanggal='$tanggal',
                kategori='$kategori',
                jenis='$jenis',
                keterangan='$keterangan',
                lampiran='$lampiran_baru'
            WHERE id_laporan_keuangan='$id_laporan_keuangan'
        ");

    } else {

        // Update tanpa ganti file
        $query = mysqli_query($c, "
            UPDATE laporan_keuangan SET
                periode='$periode',
                tanggal='$tanggal',
                kategori='$kategori',
                jenis='$jenis',
                keterangan='$keterangan'
            WHERE id_laporan_keuangan='$id_laporan_keuangan'
        ");
    }

    if ($query) {
        echo "<script>alert('Laporan keuangan berhasil diperbarui'); window.location.href='laporanKeuangan.php';</script>";
    } else {
        echo "<script>alert('Gagal update laporan'); window.location.href='laporanKeuangan.php';</script>";
    }
}


// =====================================================
// VERIFIKASI LAPORAN KEUANGAN
// =====================================================
if (isset($_POST['verifikasi_l_k'])) {

    if ($_SESSION['role'] !== 'wakil_dekan_dua') {
        die("Akses ditolak!");
    }

    $id_l_k = mysqli_real_escape_string($c, $_POST['id_laporan_keuangan']);

    $sql = "UPDATE laporan_keuangan SET 
                statuz = 'Disetujui',
                komentar_revisi_l_k = NULL
            WHERE id_laporan_keuangan = '$id_l_k'";

    $query = mysqli_query($c, $sql);

    if (!$query) {
        die("SQL Error (verifikasi_l_k): " . mysqli_error($c));
    }

    echo "<script>
            alert('Laporan berhasil diverifikasi!');
            window.location.href='verifikasiLaporanKeuangan.php';
          </script>";
}


// =====================================================
// KIRIM REVISI LAPORAN
// =====================================================
if (isset($_POST['kirimRevisi_l_k'])) {

    if ($_SESSION['role'] !== 'wakil_dekan_dua') {
        die("Akses ditolak!");
    }

    $id_l_k   = mysqli_real_escape_string($c, $_POST['id_laporan_keuangan']);
    $komentar_l_k = mysqli_real_escape_string($c, $_POST['komentar_l_k']);

    $sql = "UPDATE laporan_keuangan SET 
                statuz = 'Revisi',
                komentar_revisi_l_k = '$komentar_l_k'
            WHERE id_laporan_keuangan = '$id_l_k'";

    $query = mysqli_query($c, $sql);

    if (!$query) {
        die("SQL Error (revisi_l_k): " . mysqli_error($c));
    }

    echo "<script>
            alert('Revisi berhasil dikirim!');
            window.location.href='verifikasiLaporanKeuangan.php';
          </script>";
}


// === HAPUS LAPORAN KEUANGAN ===
if (isset($_POST['hapus_laporan_keuangan'])) {

    $id_laporan_keuangan = $_POST['id_l_k'];

    $cek_file = mysqli_query($c, "SELECT lampiran FROM laporan_keuangan WHERE id_laporan_keuangan='$id_laporan_keuangan'");
    $data_file = mysqli_fetch_assoc($cek_file);

    if($data_file && $data_file['lampiran'] != ""){
        $file_path = "uploads/".$data_file['lampiran'];
        if(file_exists($file_path)){
            unlink($file_path); //hapus file fisik
        }
    }

    $query = mysqli_query($c, "DELETE FROM laporan_keuangan WHERE id_laporan_keuangan='$id_laporan_keuangan'");

    if ($query) {
        echo "<script>alert('Data dihapus'); window.location.href='laporanKeuangan.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus'); window.location.href='laporanKeuangan.php';</script>";
    }
}

// === TAMBAH USER ===
if (isset($_POST['tambah_user'])) {
    $nama     = mysqli_real_escape_string($c, $_POST['nama_baru']);
    $username = mysqli_real_escape_string($c, $_POST['username_baru']);
    $password = md5($_POST['password_baru']);
    $role     = $_POST['role_baru'];
    
    // PERBAIKAN: Kolom 'npm' dihapus dari query INSERT
    $query = mysqli_query($c, "INSERT INTO users (username, password, role, nama) 
                               VALUES ('$username', '$password', '$role', '$nama')");

    if ($query) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='kelolaUser.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($c);
    }
}

// === EDIT USER ===
if (isset($_POST['edit_user'])) {
    $id_user  = $_POST['id_user'];
    $nama     = mysqli_real_escape_string($c, $_POST['nama']); 
    $username = mysqli_real_escape_string($c, $_POST['username']);
    $role     = $_POST['role'];
    $pw_baru  = $_POST['password_baru'];

    // PERBAIKAN: Logika $npm_val dibuang karena kolom npm akan dihapus

    if (!empty($pw_baru)) {
        // Jika ganti password
        $pw_hashed = md5($pw_baru);
        $query = "UPDATE users SET 
                    nama='$nama', 
                    username='$username', 
                    role='$role', 
                    password='$pw_hashed' 
                  WHERE id='$id_user'";
    } else {
        // Jika tidak ganti password
        $query = "UPDATE users SET 
                    nama='$nama', 
                    username='$username', 
                    role='$role' 
                  WHERE id='$id_user'";
    }

    if (mysqli_query($c, $query)) {
        echo "<script>alert('Berhasil Update User'); window.location.href='kelolaUser.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($c) . "');</script>";
    }
}

// === HAPUS USER ===
if (isset($_POST['hapus_user'])) {
    $id_user_yang_dihapus = mysqli_real_escape_string($c, $_POST['id_user']);

    $query_hapus = mysqli_query($c, "DELETE FROM users WHERE id='$id_user_yang_dihapus'");

    if ($query_hapus) {
        echo "<script>alert('User berhasil dihapus'); window.location.href='kelolaUser.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user: " . mysqli_error($c) . "'); window.location.href='kelolaUser.php';</script>";
    }
}

?>