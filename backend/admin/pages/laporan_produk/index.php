<?php
session_start();
include '../../app.php';

// Query untuk laporan produk - TABEL DIPERBAIKI
$qProduk = "SELECT 
    p.id_produk,
    p.kode_produk,
    p.nama_produk,
    p.foto,
    p.harga,
    p.stok,
    p.id_kategori,
    k.nama_kategori
FROM produk p
LEFT JOIN kategori_produk k ON p.id_kategori = k.id_kategori
ORDER BY p.nama_produk ASC";

$result = mysqli_query($connect, $qProduk);

// Cek jika query gagal
if (!$result) {
    die("Error dalam query: " . mysqli_error($connect));
}
?>

<?php include '../../partials/header.php'; ?>
<?php $page = 'laporan_produk'; ?>
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

    /* Excel-like Border dengan Rounded Corner */
    .table-excel {
        border-collapse: separate !important;
        border-spacing: 0;
        border: 1px solid #d0d0d0;
        border-radius: 12px;
        overflow: hidden;
        width: 100%;
    }

    /* Border grid tiap sel */
    .table-excel th,
    .table-excel td {
        border: 1px solid #d0d0d0 !important;
        padding: 10px;
    }

    /* Opsional: tebalkan border luar */
    .table-excel tr:first-child th:first-child {
        border-top-left-radius: 12px;
    }

    .table-excel tr:first-child th:last-child {
        border-top-right-radius: 12px;
    }

    .table-excel tr:last-child td:first-child {
        border-bottom-left-radius: 12px;
    }

    .table-excel tr:last-child td:last-child {
        border-bottom-right-radius: 12px;
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

    .table-modern img {
        border-radius: 8px;
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

    .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin-left: 2px;
        margin-right: 2px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #007bff;
        cursor: pointer;
        background: white;
        text-decoration: none;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .dataTables_paginate .paginate_button:hover {
        background-color: #f8f9fa;
        color: #0056b3;
        text-decoration: none;
    }

    .dataTables_paginate .paginate_button.current:hover {
        background-color: #007bff;
        color: white;
    }

    .dataTables_paginate .paginate_button.disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Clearfix untuk wrapper */
    .dataTables_wrapper:after {
        content: "";
        display: table;
        clear: both;
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

    /* Table row styling */
    .table-modern tbody tr {
        border-radius: 8px;
        overflow: hidden;
    }

    /* Status badges custom */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .stok-aman {
        background-color: #e6f7ee;
        color: #00b96c;
    }

    .stok-sedikit {
        background-color: #fff7e6;
        color: #fa8c16;
    }

    .stok-habis {
        background-color: #fff0f0;
        color: #e63757;
    }

    /* Export buttons */
    .export-buttons {
        margin-bottom: 20px;
    }

    /* Harga styling */
    .harga {
        font-weight: 600;
        color: #2c3e50;
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
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold text-primary">📦 Laporan Produk</h4>
                </div>

                <div class="table-responsive">
                    <table id="produkTable" class="table table-modern table-excel align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Foto</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)):
                                $stok_class = '';
                                $stok_text = '';

                                if ($row['stok'] == 0) {
                                    $stok_class = 'stok-habis';
                                    $stok_text = 'Habis';
                                } elseif ($row['stok'] <= 10) {
                                    $stok_class = 'stok-sedikit';
                                    $stok_text = 'Sedikit';
                                } else {
                                    $stok_class = 'stok-aman';
                                    $stok_text = 'Aman';
                                }
                            ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $row['kode_produk'] ?></td>
                                    <td>
                                        <div style="max-width: 200px; word-wrap: break-word;">
                                            <?= htmlspecialchars($row['nama_produk']) ?>
                                        </div>
                                    </td>
                                    <td><?= $row['nama_kategori'] ?? 'Tidak berkategori' ?></td>
                                    <td>
                                        <?php if (!empty($row['foto'])): ?>
                                            <img src="../../../../storages/produk/<?= $row['foto'] ?>"
                                                alt="Foto Produk" style="max-width: 60px; height: auto; border-radius: 6px;">
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada foto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td><?= $row['stok'] ?></td>
                                    <td>
                                        <span class="status-badge <?= $stok_class ?>">
                                            <?= $stok_text ?>
                                        </span>
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

<!-- DataTables & Export Libraries -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#produkTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-success',
                    text: '📊 Excel',
                    title: 'Laporan Produk'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger',
                    text: '📄 PDF',
                    title: 'Laporan Produk',
                    orientation: 'landscape'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info',
                    text: '🖨️ Print',
                    title: 'Laporan Produk',
                    customize: function(win) {
                        $(win.document.body).css('font-size', '10pt');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: ">",
                    previous: "<"
                }
            },
            responsive: true,
            order: [
                [2, 'asc']
            ],
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ]
        });

        // Style buttons
        $('.dt-buttons').addClass('btn-group');
        $('.dt-button').addClass('btn btn-sm');
    });

    function cetakLaporan() {
        window.print();
    }

    function exportPDF() {
        $('.buttons-pdf').click();
    }

    function exportExcel() {
        $('.buttons-excel').click();
    }
</script>