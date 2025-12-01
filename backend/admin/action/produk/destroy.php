<?php
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Hapus foto jika ada
    $foto_query = "SELECT foto FROM produk WHERE id_produk = '$id'";
    $foto_result = mysqli_query($connect, $foto_query);
    if ($foto_result && $foto_data = mysqli_fetch_assoc($foto_result)) {
        if (!empty($foto_data['foto']) && file_exists('../../../../storages/produk/' . $foto_data['foto'])) {
            unlink('../../../../storages/produk/' . $foto_data['foto']);
        }
    }

    $qDelete = "DELETE FROM produk WHERE id_produk = '$id'";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        $_SESSION['success'] = "Produk berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus produk: " . mysqli_error($connect);
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
}

header("Location: ../../pages/produk/index.php");
exit();
