<?php
include '../../app.php';

// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID tidak valid';
    header('Location: ../../pages/kategori/index.php');
    exit;
}

$id = (int) $_GET['id'];

// Query ambil data
$qSelect = "SELECT * FROM kategori_produk WHERE id_kategori = $id";
$result = mysqli_query($connect, $qSelect);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = 'Data tidak ditemukan';
    header('Location: ../../pages/kategori/index.php');
    exit;
}

$kategori = mysqli_fetch_object($result);
