<?php
include "../../../../config/koneksi.php";
$qProduk = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id_kategori";
$result = mysqli_query($connect, $qProduk) or die(mysqli_error($connect));
?>

<?php include '../../partials/header.php'; ?>
<?php $page = 'produk'; ?>
<?php include '../../partials/sidebar.php'; ?>

<style>
    /* ===== GLOBAL DATA TABLE STYLING ===== */
    /* Konten utama menyesuaikan sidebar */
    #main {
        margin-left: 260px;
        margin-top: 70px;
        padding: 20px;
        width: calc(100% - 260px);
        box-sizing: border-box;
    }

    /* Modern Table Styling */
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 10px;
        width: 100% !important;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background-color: #f5f7fa;
        color: #6c757d;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: bold;
        border: none;
        padding: 12px;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
    }

    .table-modern tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .table-modern td {
        border: none;
        padding: 14px;
        vertical-align: middle;
    }

    /* Tombol aksi minimalis */
    .btn-action {
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        margin: 2px;
    }

    /* DataTable Custom Styling */
    .dataTables_wrapper {
        position: relative;
        padding-top: 10px;
    }

    .dataTables_filter {
        margin-bottom: 15px;
        float: right;
    }

    .dataTables_filter label {
        display: flex;
        align-items: center;
        font-weight: normal;
        margin-bottom: 0;
    }

    .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px 12px;
        margin-left: 10px;
        width: 200px;
    }

    .dataTables_length {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dataTables_length label {
        font-weight: normal;
        margin-bottom: 0;
    }

    .dataTables_length select {
        min-width: 70px;
        text-align: center;
    }

    .dataTables_info {
        margin-top: 15px;
        font-size: 14px;
        color: #6c757d;
        float: left;
        padding-top: 10px;
    }

    .dataTables_paginate {
        margin-top: 15px;
        float: right;
    }

    /* Card styling */
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .card-body {
        padding: 25px;
    }

    /* Header styling */
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0;
    }

    /* Button styling */
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #3a56c4;
        border-color: #3a56c4;
    }

    /* Table header styling */
    .table-modern thead th {
        background-color: #f8f9fc;
        color: #6e707e;
        border-bottom: 1px solid #e3e6f0;
    }

    /* Action buttons */
    .btn-outline-warning {
        color: #f6c23e;
        border-color: #f6c23e;
    }

    .btn-outline-warning:hover {
        background-color: #f6c23e;
        color: white;
    }

    .btn-outline-danger {
        color: #e74a3b;
        border-color: #e74a3b;
    }

    .btn-outline-danger:hover {
        background-color: #e74a3b;
        color: white;
    }

    .btn-outline-info {
        color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-outline-info:hover {
        background-color: #17a2b8;
        color: white;
    }

    /* Product image styling */
    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .price-format {
        font-weight: bold;
        color: #28a745;
    }

    .stock-format {
        font-weight: bold;
    }

    .stock-low {
        color: #dc3545;
    }

    .stock-medium {
        color: #ffc107;
    }

    .stock-high {
        color: #28a745;
    }

    /* Kategori styling */
    .kategori-badge {
        background-color: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #main {
            margin-left: 0;
            width: 100%;
        }

        .dataTables_filter {
            float: none;
            margin-bottom: 15px;
        }

        .dataTables_length {
            margin-bottom: 15px;
        }

        .table-modern td,
        .table-modern th {
            padding: 8px;
        }

        .btn-action {
            padding: 4px 8px;
            font-size: 12px;
        }
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <!-- Card Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold text-primary page-title">📦 Data Produk</h4>
                    <a href="./create.php" class="btn btn-primary"> ✚ Tambah</a>
                </div>
                <div class="table-responsive">
                    <table id="produkTable" class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Kode Produk</th>
                                <th class="text-center">Nama Produk</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($item = $result->fetch_object()):
                                $stockClass = '';
                                if ($item->stok == 0) {
                                    $stockClass = 'stock-low';
                                } elseif ($item->stok < 10) {
                                    $stockClass = 'stock-medium';
                                } else {
                                    $stockClass = 'stock-high';
                                }
                            ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?= $no ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($item->foto)): ?>
                                            <img src="../../../../storages/produk/<?= $item->foto ?>" alt="<?= $item->nama_produk ?>" class="product-image">
                                        <?php else: ?>
                                            <div class="text-muted">No Image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold"><?= $item->kode_produk ?></td>
                                    <td class="fw-semibold"><?= $item->nama_produk ?></td>
                                    <td class="text-center">
                                        <span class="kategori-badge"><?= $item->nama_kategori ?: 'Tidak ada kategori' ?></span>
                                    </td>
                                    <td class="price-format">Rp <?= $item->harga ?></td>
                                    <td class="fw-semibold <?= $stockClass ?>"><?= $item->stok ?> pcs</td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <a href="./detail.php?id=<?= $item->id_produk ?>" class="btn btn-outline-info btn-sm btn-action">👁 Detail</a>
                                            <a href="./edit.php?id=<?= $item->id_produk ?>" class="btn btn-outline-warning btn-sm btn-action">🖋 Edit</a>
                                            <a href="../../action/produk/destroy.php?id=<?= $item->id_produk ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                                class="btn btn-outline-danger btn-sm btn-action"> 🗑 Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                $no++;
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php include '../../partials/footer.php'; ?>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<!-- Tambahkan DataTables CSS dan JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        var table = $('#produkTable').DataTable({
            language: {
                search: "",
                searchPlaceholder: " Cari produk...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹"
                }
            },

            "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 1, 7] // Non-aktifkan sorting untuk kolom No, Foto, dan Aksi
                },
                {
                    "searchable": false,
                    "targets": [0, 1, 7] // Non-aktifkan pencarian untuk kolom No, Foto, dan Aksi
                }
            ],
            "order": [
                [3, 'asc'] // Default sorting berdasarkan Nama Produk
            ],
            "responsive": true,
            "autoWidth": false,
            "drawCallback": function(settings) {
                // Pastikan nomor urut tetap konsisten setelah filtering/pagination
                var api = this.api();
                var startIndex = api.page.info().start;

                api.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "initComplete": function() {
                // Tambahkan kelas ke elemen DataTable setelah inisialisasi
                $('.dataTables_length label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();

                $('.dataTables_length label').prepend('Tampilkan ');
                $('.dataTables_length label').append(' data per halaman');

                $('.dataTables_filter label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();

                $('.dataTables_filter label').prepend('Cari:');
            }
        });
    });
</script>