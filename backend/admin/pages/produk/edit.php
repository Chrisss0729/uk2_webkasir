<?php include '../../partials/header.php'; ?>
<?php $page = 'produk'; ?>
<?php include '../../partials/sidebar.php'; ?>

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

    /* FILE INPUT STYLING */
    .form-control[type="file"] {
        padding: 8px;
    }

    /* NUMBER INPUT STYLING */
    .form-control[type="number"] {
        padding: 10px 14px;
    }

    /* IMAGE PREVIEW */
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px;
    }

    .current-image {
        max-width: 200px;
        max-height: 200px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px;
        margin-bottom: 10px;
    }

    /* SELECT STYLING */
    .form-select {
        padding: 10px 14px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-0">
        <div class="card border-0">
            <!-- Header Card -->
            <div class="card-header-custom">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="fw-bold text-primary mb-0">📦 Edit Produk</h4>
                    <a href="./index.php" class="btn btn-primary">❮❮ Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <div class="form-container">
                    <?php
                    include '../../action/produk/show.php';

                    // Ambil data kategori dari database
                    include '../../../../config/koneksi.php';
                    $query_kategori = "SELECT * FROM kategori_produk ORDER BY nama_kategori";
                    $result_kategori = mysqli_query($connect, $query_kategori);
                    $kategori = [];
                    if ($result_kategori) {
                        while ($row = mysqli_fetch_assoc($result_kategori)) {
                            $kategori[] = $row;
                        }
                    }
                    ?>
                    <form action="../../action/produk/update.php?id=<?= $produk->id_produk ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="kode_produk" class="form-label">Kode Produk</label>
                            <input type="text" name="kode_produk" class="form-control" id="kode_produk"
                                placeholder="Masukan Kode Produk.." required value="<?= $produk->kode_produk ?>">
                        </div>

                        <div class="mb-4">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control" id="nama_produk"
                                placeholder="Masukan Nama Produk.." required value="<?= $produk->nama_produk ?>">
                        </div>

                        <div class="mb-4">
                            <label for="id_kategori" class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select" id="id_kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>"
                                        <?= ($produk->id_kategori == $kat['id_kategori']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="text" name="harga" class="form-control" id="harga"
                                placeholder="Masukan Harga.." min="0" step="0.01" required value="<?= $produk->harga ?>">
                        </div>

                        <div class="mb-4">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" id="stok"
                                placeholder="Masukan Stok.." min="0" required value="<?= $produk->stok ?>">
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label">Foto Produk</label>

                            <?php if (!empty($produk->foto)): ?>
                                <div class="mb-2">
                                    <label class="form-label text-muted">Foto Saat Ini:</label>
                                    <div>
                                        <img src="../../../../storages/produk/<?= $produk->foto ?>" alt="Current Image" class="current-image">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <input type="file" name="foto" class="form-control" id="foto" accept="image/*">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah foto</div>
                            <img id="imagePreview" class="image-preview" src="#" alt="Preview" style="display: none;">
                        </div>

                        <button type="submit" class="btn btn-success" name="tombol">💾 Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>

<script>
    // Preview image sebelum upload
    document.getElementById('foto').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>

<?php include '../../partials/script.php'; ?>