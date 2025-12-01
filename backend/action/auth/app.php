<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "uk2_db_pos_loko_campuran";

$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi escape string untuk keamanan
function escapeString($data)
{
    global $koneksi;
    return mysqli_real_escape_string($koneksi, $data);
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');
