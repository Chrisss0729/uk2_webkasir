<?php
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    $qDelete = "DELETE FROM kategori_produk WHERE id_kategori = '$id'";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        $_SESSION['success'] = "Kategori berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus kategori: " . mysqli_error($connect);
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
}

header("Location: ../../pages/kategori/index.php");
exit();
