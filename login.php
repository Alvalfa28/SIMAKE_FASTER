<?php
session_start();
require_once 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($c, $_POST['username']);
    $password = md5($_POST['password']); 

    $query = mysqli_query($c, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        switch ($data['role']) {
            case 'admin': header("Location: index.php"); break;
            case 'staf_keuangan': header("Location: dashboardStaf.php"); break;
            case 'mahasiswa': header("Location: dashboardMahasiswa.php"); break;
            case 'prodi': header("Location: dashboardProdi.php"); break;
            case 'wakil_dekan_dua': header("Location: dashboardWdDua.php"); break;
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login | SIM Keuangan FASTer</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Overlay gelap agar background tidak terlalu terang */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(31, 122, 63, 0.8), rgba(20, 92, 48, 0.9));
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-bottom: none;
            padding-top: 40px;
        }

        .logo-faster {
            width: 100px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            margin-bottom: 20px;
        }

        .app-title {
            font-weight: 700;
            color: #145c30;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 1.2rem;
        }

        .app-subtitle {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(31, 122, 63, 0.25);
            border-color: #1f7a3f;
        }

        .btn-success {
            background-color: #1f7a3f;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-success:hover {
            background-color: #145c30;
            box-shadow: 0 5px 15px rgba(31, 122, 63, 0.4);
        }

        .input-group-text {
            background: transparent;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #1f7a3f;
        }

        .input-with-icon {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-white text-center">
                    <img src="assets/img/logo-faster.png" alt="Logo FASTer" class="logo-faster">
                    <div class="app-title">SIMAKE FASTer</div>
                    <div class="app-subtitle">Sistem Informasi Manajemen Keuangan</div>
                    <p class="small text-muted">Universitas Suryakancana</p>
                </div>

                <div class="card-body px-4 pb-4">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input class="form-control input-with-icon" name="username" type="text" placeholder="Masukkan username" required />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input class="form-control input-with-icon" name="password" type="password" placeholder="Masukkan password" required />
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-success w-100 shadow-sm">
                            Masuk Ke Sistem <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
                
                <div class="card-footer bg-light text-center py-3">
                    <div class="small text-muted">
                        &copy; <?= date('Y'); ?> FAKULTAS SAINS TERAPAN
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>