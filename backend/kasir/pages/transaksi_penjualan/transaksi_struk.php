<?php
session_start();

// Redirect jika tidak ada data struk
if (!isset($_SESSION['struk_data'])) {
    header("Location: transaksi.php");
    exit;
}

$struk_data = $_SESSION['struk_data'];
$kode_transaksi = $struk_data['kode_transaksi'];
$total_bayar = $struk_data['total_bayar'];
$jumlah_bayar = $struk_data['jumlah_bayar'];
$kembalian = $struk_data['kembalian'];
$cart = $struk_data['cart'];
$tanggal = $struk_data['tanggal'];

// Hapus data struk dari session setelah digunakan
unset($_SESSION['struk_data']);

// Fungsi format harga
function formatHarga($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12px;
                padding: 0;
                margin: 0;
                background: white !important;
            }

            .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .struk-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10px !important;
            }
        }

        .struk-container {
            max-width: 300px;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .border-top-dashed {
            border-top: 1px dashed #000;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .product-item {
            border-bottom: 1px dotted #ccc;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Struk Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <!-- Struk untuk cetak -->
                        <div class="struk-container">
                            <div class="text-center mb-2">
                                <h6><strong>Web Kasir Restaurant</strong></h6>
                                <p class="mb-1">Kweni Rt.07 Panggungharjo Sewon Bantul</p>
                                <p class="mb-1">Telp: 0877-8955-4055</p>
                            </div>
                            <div class="border-top-dashed pt-2 mb-2">
                                <p class="mb-1"><strong>Kode: <?= $kode_transaksi ?></strong></p>
                                <p class="mb-1">Tanggal: <?= date('d/m/Y H:i:s', strtotime($tanggal)) ?></p>
                            </div>
                            <div class="border-top-dashed pt-2 mb-2">
                                <?php foreach ($cart as $item): ?>
                                    <div class="product-item">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><strong><?= substr($item['nama'], 0, 20) ?></strong></span>
                                            <span><strong><?= $item['jumlah'] ?>x</strong></span>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span>@ <?= $item['harga'] ?></span>
                                            <span><strong><?= formatHarga($item['harga_angka'] * $item['jumlah']) ?></strong></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="border-top-dashed pt-2">
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>TOTAL:</span>
                                    <span><?= formatHarga($total_bayar) ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>BAYAR:</span>
                                    <span><?= formatHarga($jumlah_bayar) ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>KEMBALI:</span>
                                    <span><?= formatHarga($kembalian) ?></span>
                                </div>
                            </div>
                            <div class="border-top-dashed pt-2 text-center mt-3">
                                <p class="mb-1"><strong>Terima kasih atas kunjungan Anda</strong></p>
                                <p class="mb-0"><small>*** LOKO CAMPURAN ***</small></p>
                            </div>
                        </div>

                        <div class="text-center mt-4 no-print">
                            <button class="btn btn-primary me-2" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Struk
                            </button>
                            <a href="transaksi.php" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Transaksi Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto print ketika halaman struk dibuka
        window.onload = function() {
            // Optional: Auto print saat halaman dibuka
            // window.print();
        };
    </script>
</body>

</html>