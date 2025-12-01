<?php
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    $qDelete = "DELETE FROM user WHERE id_user = '$id'";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        $_SESSION['success'] = "Data berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($connect);
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
}

header("Location: ../../pages/user/index.php");
exit();
