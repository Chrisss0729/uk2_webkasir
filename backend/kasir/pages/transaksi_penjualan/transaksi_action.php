<?php
session_start();

// Koneksi database langsung
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'uk2_db_pos_loko_campuran';

// Establish connection
$koneksi = mysqli_connect($hostname, $username, $password, $database);

// Check connection
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fungsi untuk konversi string harga ke angka
function hargaToNumber($harga_string)
{
    $only_digits = preg_replace('/[^\d]/', '', $harga_string);
    return intval($only_digits);
}

// Fungsi untuk format angka ke format harga
function formatHarga($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// AMBIL ID KASIR DARI SESSION
$id_kasir = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;

// CEK APAKAH ID KASIR ADA DI DATABASE
if ($id_kasir <= 0) {
    $query_kasir_default = "SELECT id_user FROM user WHERE hak_akses IN ('kasir','administrator') LIMIT 1";
    $result_kasir_default = mysqli_query($koneksi, $query_kasir_default);
    if ($result_kasir_default && mysqli_num_rows($result_kasir_default) > 0) {
        $kasir_data = mysqli_fetch_assoc($result_kasir_default);
        $id_kasir = intval($kasir_data['id_user']);
    } else {
        $id_kasir = 1; // Fallback
    }
}

// Proses transaksi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_transaksi'])) {
    // Validasi keranjang tidak kosong
    if (empty($_POST['produk_id']) || empty($_POST['jumlah'])) {
        $_SESSION['error'] = 'Keranjang belanja kosong!';
        header("Location: transaksi.php");
        exit;
    }

    // Generate kode transaksi
    $kode_transaksi = 'TRX-' . date('YmdHis');
    $tanggal_transaksi = date('Y-m-d H:i:s');

    // Hitung total
    $total_bayar = 0;
    $cart_data = [];

    foreach ($_POST['produk_id'] as $index => $produk_id) {
        $jumlah = intval($_POST['jumlah'][$index]);
        $harga = intval($_POST['harga'][$index]);
        $subtotal = $jumlah * $harga;
        $total_bayar += $subtotal;

        // Simpan data cart untuk struk
        $cart_data[] = [
            'id' => $produk_id,
            'nama' => '', // Akan diisi dari database nanti
            'harga' => formatHarga($harga),
            'harga_angka' => $harga,
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];
    }

    $jumlah_bayar = hargaToNumber($_POST['jumlah_bayar']);
    $kembalian = $jumlah_bayar - $total_bayar;

    // Validasi jumlah bayar
    if ($jumlah_bayar < $total_bayar) {
        $_SESSION['error'] = 'Jumlah bayar kurang dari total! Total: ' . formatHarga($total_bayar);
        header("Location: transaksi.php");
        exit;
    }

    // Mulai transaction
    mysqli_begin_transaction($koneksi);

    try {
        // Simpan transaksi ke database - PERBAIKI NAMA KOLOM id_kasir
        $query_transaksi = "INSERT INTO transaksi (kode_transaksi, tanggal_transaksi, total_bayar, jumlah_bayar, kembalian, id_kasir) 
                           VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $query_transaksi);
        mysqli_stmt_bind_param($stmt, 'ssdddi', $kode_transaksi, $tanggal_transaksi, $total_bayar, $jumlah_bayar, $kembalian, $id_kasir);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal menyimpan transaksi: " . mysqli_error($koneksi));
        }

        $id_transaksi = mysqli_insert_id($koneksi);

        // Simpan detail transaksi dan update stok
        foreach ($_POST['produk_id'] as $index => $produk_id) {
            $jumlah = intval($_POST['jumlah'][$index]);
            $harga_satuan = intval($_POST['harga'][$index]);

            // Cek stok tersedia dan ambil nama produk
            $query_cek_stok = "SELECT stok, nama_produk FROM produk WHERE id_produk = ?";
            $stmt_cek = mysqli_prepare($koneksi, $query_cek_stok);
            mysqli_stmt_bind_param($stmt_cek, 'i', $produk_id);
            mysqli_stmt_execute($stmt_cek);
            $result_cek = mysqli_stmt_get_result($stmt_cek);
            $produk_data = mysqli_fetch_assoc($result_cek);

            if (!$produk_data) {
                throw new Exception("Produk dengan ID $produk_id tidak ditemukan!");
            }

            if ($produk_data['stok'] < $jumlah) {
                throw new Exception("Stok " . $produk_data['nama_produk'] . " tidak mencukupi! Stok tersedia: " . $produk_data['stok']);
            }

            // Update nama produk di cart data
            foreach ($cart_data as &$item) {
                if ($item['id'] == $produk_id) {
                    $item['nama'] = $produk_data['nama_produk'];
                    break;
                }
            }

            // Simpan detail transaksi - PERBAIKI NAMA KOLOM harga_satuan
            $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan) 
                           VALUES (?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($koneksi, $query_detail);
            mysqli_stmt_bind_param($stmt_detail, 'iiid', $id_transaksi, $produk_id, $jumlah, $harga_satuan);

            if (!mysqli_stmt_execute($stmt_detail)) {
                throw new Exception("Gagal menyimpan detail transaksi: " . mysqli_error($koneksi));
            }

            // Update stok produk
            $query_update_stok = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
            $stmt_update = mysqli_prepare($koneksi, $query_update_stok);
            mysqli_stmt_bind_param($stmt_update, 'ii', $jumlah, $produk_id);

            if (!mysqli_stmt_execute($stmt_update)) {
                throw new Exception("Gagal update stok: " . mysqli_error($koneksi));
            }
        }

        // Commit transaction
        mysqli_commit($koneksi);

        // Simpan data transaksi untuk struk di session
        $_SESSION['struk_data'] = [
            'kode_transaksi' => $kode_transaksi,
            'total_bayar' => $total_bayar,
            'jumlah_bayar' => $jumlah_bayar,
            'kembalian' => $kembalian,
            'cart' => $cart_data,
            'tanggal' => $tanggal_transaksi
        ];

        // Redirect ke halaman struk
        header("Location: transaksi_struk.php");
        exit;
    } catch (Exception $e) {
        // Rollback transaction jika ada error
        mysqli_rollback($koneksi);
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header("Location: transaksi.php");
        exit;
    }
} else {
    // Jika bukan POST request, redirect ke halaman transaksi
    header("Location: transaksi.php");
    exit;
}
