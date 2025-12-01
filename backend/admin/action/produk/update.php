<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $id = (int)$_GET['id'];
    $kode_produk = escapeString($_POST['kode_produk']);
    $nama_produk = escapeString($_POST['nama_produk']);
    $id_kategori = escapeString($_POST['id_kategori']);
    $harga = escapeString($_POST['harga']);
    $stok = escapeString($_POST['stok']);

    // Handle file upload jika ada foto baru
    $foto_update = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            $foto_update = uniqid() . '.' . $file_ext;
            $upload_path = '../../../../storages/produk/' . $foto_update;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus foto lama jika ada
                $old_foto_query = "SELECT foto FROM produk WHERE id_produk = '$id'";
                $old_foto_result = mysqli_query($connect, $old_foto_query);
                if ($old_foto_result && $old_foto = mysqli_fetch_assoc($old_foto_result)) {
                    if (!empty($old_foto['foto']) && file_exists('../../../../storages/produk/' . $old_foto['foto'])) {
                        unlink('../../../../storages/produk/' . $old_foto['foto']);
                    }
                }
            } else {
                $foto_update = '';
            }
        }
    }

    if (!empty($foto_update)) {
        $qUpdate = "UPDATE produk SET 
                    kode_produk = '$kode_produk', 
                    nama_produk = '$nama_produk', 
                    foto = '$foto_update', 
                    harga = '$harga', 
                    stok = '$stok',
                    id_kategori = '$id_kategori'
                    WHERE id_produk = '$id'";
    } else {
        $qUpdate = "UPDATE produk SET 
                    kode_produk = '$kode_produk', 
                    nama_produk = '$nama_produk', 
                    harga = '$harga', 
                    stok = '$stok',
                    id_kategori = '$id_kategori'
                    WHERE id_produk = '$id'";
    }

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Produk berhasil diubah";
        header("Location: ../../pages/produk/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Produk gagal diubah: " . mysqli_error($connect);
        header("Location: ../../pages/produk/edit.php?id=$id");
        exit();
    }
}
