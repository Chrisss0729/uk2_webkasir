<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $id = (int)$_GET['id'];
    $nama_kategori = escapeString($_POST['nama_kategori']);

    $qUpdate = "UPDATE kategori_produk SET 
                nama_kategori = '$nama_kategori' 
                WHERE id_kategori = '$id'";

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Kategori berhasil diubah";
        header("Location: ../../pages/kategori/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Kategori gagal diubah: " . mysqli_error($connect);
        header("Location: ../../pages/kategori/edit.php?id=$id");
        exit();
    }
}
