<?php
include "../../../../config/koneksi.php";
$qUser = "SELECT * FROM user";
$result = mysqli_query($connect, $qUser) or die(mysqli_error($connect));
?>

<?php include '../../partials/header.php'; ?>
<?php $page = 'pengguna'; ?>
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

    /* Status badges */
    .badge-akses {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .akses-administrator {
        background-color: #e8f4ff;
        color: #0095e8;
    }

    .akses-kasir {
        background-color: #e6f7ee;
        color: #00b96c;
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
                    <h4 class="fw-bold text-primary page-title">👥 Data User</h4>
                    <a href="./create.php" class="btn btn-primary"> ✚ Tambah</a>
                </div>
                <div class="table-responsive">
                    <table id="userTable" class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Lengkap</th>
                                <th class="text-center">Username</th>
                                <th class="text-center">Hak Akses</th>
                                <th class="text-center">Tanggal Gabung</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($item = $result->fetch_object()):
                            ?>
                                <tr>
                                    <td class="fw-semibold"><?= $no ?></td>
                                    <td class="fw-semibold"><?= $item->nama_lengkap ?></td>
                                    <td class="fw-semibold"><?= $item->username ?></td>
                                    <td class="fw-semibold">
                                        <span class="badge-akses akses-<?= $item->hak_akses ?>">
                                            <?= ucfirst($item->hak_akses) ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold"><?= date('d M Y H:i', strtotime($item->tanggal_gabung)) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <a href="./edit.php?id=<?= $item->id_user ?>" class="btn btn-outline-warning btn-sm btn-action">🖋 Edit</a>
                                            <a href="../../action/user/destroy.php?id=<?= $item->id_user ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
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
        var table = $('#userTable').DataTable({
            language: {
                search: "",
                searchPlaceholder: " Cari user...",
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
                    "targets": [0, 5] // Non-aktifkan sorting untuk kolom No dan Aksi
                },
                {
                    "searchable": false,
                    "targets": [0, 5] // Non-aktifkan pencarian untuk kolom No dan Aksi
                }
            ],
            "order": [
                [1, 'asc'] // Default sorting berdasarkan Nama Lengkap
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