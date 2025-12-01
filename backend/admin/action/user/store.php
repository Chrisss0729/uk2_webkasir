<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $nama_lengkap = escapeString($_POST['nama_lengkap']);
    $username     = escapeString($_POST['username']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $hak_akses    = escapeString($_POST['hak_akses']);
    $tanggal_gabung = date('Y-m-d H:i:s');

    $qInsert = "INSERT INTO user (nama_lengkap, username, password, hak_akses, tanggal_gabung) 
                VALUES ('$nama_lengkap', '$username', '$password', '$hak_akses', '$tanggal_gabung')";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "Data berhasil ditambah";
        header("Location: ../../pages/user/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Data gagal ditambah: " . mysqli_error($connect);
        header("Location: ../../pages/user/create.php");
        exit();
    }
}
