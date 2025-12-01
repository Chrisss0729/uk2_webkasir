<?php
include "../../partials/header.php";
$page = 'produk';
include "../../partials/sidebar.php";
?>

<style>
    #main {
        margin-left: 260px;
        margin-top: 70px;
        padding: 15px;
        width: calc(100% - 260px);
        box-sizing: border-box;
    }

    /* FORM CONTAINER FULL WIDTH */
    .form-container {
        width: 100%;
        margin: 0;
    }

    /* CARD FULL WIDTH SAMPE POJOK */
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        width: 100%;
        border: 1px solid #e0e0e0;
    }

    .card-body {
        padding: 25px;
    }

    /* FORM ELEMENTS COMPACT */
    .form-control {
        padding: 10px 14px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f8f9fa;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    /* BUTTON COMPACT */
    .btn {
        padding: 8px 16px;
        font-size: 13px;
        border-radius: 6px;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #3a56c4;
        border-color: #3a56c4;
    }

    /* HEADER STYLING COMPACT */
    .card-header-custom {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 25px;
        border-radius: 8px 8px 0 0 !important;
    }

    h4.fw-bold {
        font-size: 20px;
        color: #2c3e50;
        margin: 0;
    }

    /* FORM GROUP COMPACT */
    .mb-4 {
        margin-bottom: 20px !important;
    }

    /* IMAGE STYLING */
    .product-image {
        max-width: 300px;
        max-height: 300px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px;
    }

    .price-format {
        font-weight: bold;
        color: #28a745;
    }

    /* SELECT STYLING */
    .form-select {
        padding: 10px 14px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f8f9fa;
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-0">
        <!-- Card Table FULL WIDTH SAMPE POJOK -->
        <div class="card border-0">
            <!-- Header Card -->
            <div class="card-header-custom">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="fw-bold text-primary mb-0">📦 Detail Produk</h4>
                    <a href="./index.php" class="btn btn-primary">❮❮ Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <div class="form-container">
                    <?php
                    include '../../action/produk/show.php';

                    // Ambil nama kategori
                    include '../../../../config/koneksi.php';
                    $nama_kategori = '-';
                    if ($produk->id_kategori) {
                        $query_kategori = "SELECT nama_kategori FROM kategori_produk WHERE id_kategori = '$produk->id_kategori'";
                        $result_kategori = mysqli_query($connect, $query_kategori);
                        if ($result_kategori && $kategori_data = mysqli_fetch_assoc($result_kategori)) {
                            $nama_kategori = $kategori_data['nama_kategori'];
                        }
                    }
                    ?>
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="id_produk" class="form-label">ID Produk</label>
                                    <input type="text" class="form-control" value="<?= $produk->id_produk ?>" disabled>
                                </div>
                                <div class="mb-4">
                                    <label for="kode_produk" class="form-label">Kode Produk</label>
                                    <input type="text" class="form-control" value="<?= $produk->kode_produk ?>" disabled>
                                </div>
                                <div class="mb-4">
                                    <label for="nama_produk" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" value="<?= $produk->nama_produk ?>" disabled>
                                </div>
                                <div class="mb-4">
                                    <label for="id_kategori" class="form-label">Kategori</label>
                                    <input type="text" class="form-control" value="<?= $nama_kategori ?>" disabled>
                                </div>
                                <div class="mb-4">
                                    <label for="harga" class="form-label">Harga</label>
                                    <input type="text" class="form-control price-format" value="Rp <?= number_format($produk->harga, 0, ',', '.') ?>" disabled>
                                </div>
                                <div class="mb-4">
                                    <label for="stok" class="form-label">Stok</label>
                                    <input type="text" class="form-control" value="<?= $produk->stok ?> pcs" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Foto Produk</label>
                                    <div>
                                        <?php if (!empty($produk->foto)): ?>
                                            <img src="../../../../storages/produk/<?= $produk->foto ?>" alt="<?= $produk->nama_produk ?>" class="product-image">
                                        <?php else: ?>
                                            <div class="text-muted">Tidak ada foto</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>
<?php include '../../partials/script.php'; ?>