<?php
session_start();

// Include file koneksi database
include "./app.php"; // atau sesuaikan path ke file koneksi Anda

// Pastikan koneksi berhasil
if (!$koneksi) {
    die("<script>
        alert('Koneksi database gagal!');
        window.location.href = '../../pages/auth/login.php';
    </script>");
}

// Validasi input
if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "<script>
        alert('Username dan password harus diisi!');
        window.location.href = '../../pages/auth/login.php';
    </script>";
    exit;
}

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

// Cek di tabel user
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' LIMIT 1");

if ($query && mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);

    // Verifikasi password
    if (password_verify($password, $data['password'])) {
        // Set session
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['hak_akses'] = $data['hak_akses'];
        $_SESSION['status'] = "login";
        $_SESSION['tanggal_gabung'] = $data['tanggal_gabung'];

        // Redirect berdasarkan hak akses
        if ($data['hak_akses'] == 'administrator') {
            echo "<script>
                alert('Login berhasil! Selamat datang Administrator');
                window.location.href = '../../admin/pages/dashboard/index.php';
            </script>";
        } else if ($data['hak_akses'] == 'kasir') { // Sesuaikan dengan nilai di database
            echo "<script>
                alert('Login berhasil! Selamat datang Kasir');
                window.location.href = '../../kasir/pages/dashboard/index.php';
            </script>";
        }
        exit;
    } else {
        echo "<script>
            alert('Password salah! Silakan coba lagi');
            window.location.href = '../../pages/auth/login.php';
        </script>";
        exit;
    }
} else {
    // Username tidak ditemukan
    echo "<script>
        alert('Username tidak ditemukan! Silakan coba lagi');
        window.location.href = '../../pages/auth/login.php';
    </script>";
    exit;
}
