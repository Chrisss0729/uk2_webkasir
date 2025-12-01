<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $nama_kategori = escapeString($_POST['nama_kategori']);

    $qInsert = "INSERT INTO kategori_produk (nama_kategori) VALUES ('$nama_kategori')";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "Kategori berhasil ditambah";
        header("Location: ../../pages/kategori/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Kategori gagal ditambah: " . mysqli_error($connect);
        header("Location: ../../pages/kategori/create.php");
        exit();
    }
}
