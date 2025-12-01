<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $id = (int)$_GET['id'];
    $nama_lengkap = escapeString($_POST['nama_lengkap']);
    $username = escapeString($_POST['username']);
    $hak_akses = escapeString($_POST['hak_akses']);

    // Jika password diisi, update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $qUpdate = "UPDATE user SET 
                    nama_lengkap = '$nama_lengkap', 
                    username = '$username', 
                    password = '$password', 
                    hak_akses = '$hak_akses' 
                    WHERE id_user = '$id'";
    } else {
        $qUpdate = "UPDATE user SET 
                    nama_lengkap = '$nama_lengkap', 
                    username = '$username', 
                    hak_akses = '$hak_akses' 
                    WHERE id_user = '$id'";
    }

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Data berhasil diubah";
        header("Location: ../../pages/user/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Data gagal diubah: " . mysqli_error($connect);
        header("Location: ../../pages/user/edit.php?id=$id");
        exit();
    }
}
