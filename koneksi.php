<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "simake"; 

// Membuat koneksi
$c = mysqli_connect($servername, $username, $password, $database);

// Mengecek koneksi
if (!$c) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
