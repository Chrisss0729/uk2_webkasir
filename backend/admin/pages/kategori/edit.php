<?php include '../../partials/header.php'; ?>
<?php $page = 'kategori'; ?>
<?php include '../../partials/sidebar.php'; ?>

<style>
    #main {
        margin-left: 260px;
        margin-top: 70px;
        padding: 20px;
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
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    /* BUTTON COMPACT */
    .btn {
        padding: 10px 20px;
        font-size: 14px;
        border-radius: 6px;
        font-weight: 600;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        width: 100%;
        margin-top: 15px;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        padding: 8px 16px;
        font-size: 13px;
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
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main-container" class="p-0" style="margin-left:290px; margin-top:70px; padding:20px;">
        <div class="card border-0">
            <!-- Header Card -->
            <div class="card-header-custom">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="fw-bold text-primary mb-0">📁 Edit Kategori Produk</h4>
                    <a href="./index.php" class="btn btn-primary">❮❮ Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <div class="form-container">
                    <?php
                    include '../../action/kategori/show.php';
                    ?>
                    <form action="../../action/kategori/update.php?id=<?= $kategori->id_kategori ?>" method="POST">
                        <div class="mb-4">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" id="nama_kategori"
                                placeholder="Masukan Nama Kategori.." required value="<?= $kategori->nama_kategori ?>">
                            <div class="form-text">Masukan nama kategori produk</div>
                        </div>
                        <button type="submit" class="btn btn-success" name="tombol">💾 Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>
<?php include '../../partials/script.php'; ?>