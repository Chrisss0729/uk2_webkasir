<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $kode_produk = escapeString($_POST['kode_produk']);
    $nama_produk = escapeString($_POST['nama_produk']);
    $id_kategori = escapeString($_POST['id_kategori']);
    $harga = escapeString($_POST['harga']);
    $stok = escapeString($_POST['stok']);

    // Handle file upload
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Generate unique filename
            $foto = uniqid() . '.' . $file_ext;
            $upload_path = '../../../../storages/produk/' . $foto;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $foto = '';
            }
        }
    }

    $qInsert = "INSERT INTO produk (kode_produk, nama_produk, foto, harga, stok, id_kategori) 
                VALUES ('$kode_produk', '$nama_produk', '$foto', '$harga', '$stok', '$id_kategori')";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "Produk berhasil ditambah";
        header("Location: ../../pages/produk/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Produk gagal ditambah: " . mysqli_error($connect);
        header("Location: ../../pages/produk/create.php");
        exit();
    }
}
