<?php
session_start();
include '../../app.php';

// Query untuk laporan stok dengan peringatan - TABEL DIPERBAIKI
$qStok = "SELECT 
    p.id_produk,
    p.kode_produk,
    p.nama_produk,
    p.foto,
    p.harga,
    p.stok,
    p.id_kategori,
    k.nama_kategori,
    CASE 
        WHEN p.stok = 0 THEN 'HABIS'
        WHEN p.stok <= 10 THEN 'SEDIKIT'
        ELSE 'AMAN'
    END as status_stok
FROM produk p
LEFT JOIN kategori_produk k ON p.id_kategori = k.id_kategori
ORDER BY p.stok ASC, p.nama_produk ASC";

$result = mysqli_query($connect, $qStok);

// Cek jika query gagal
if (!$result) {
    die("Error dalam query: " . mysqli_error($connect));
}

// Hitung statistik stok - TABEL DIPERBAIKI
$qStatistik = "SELECT 
    COUNT(*) as total_produk,
    SUM(CASE WHEN stok = 0 THEN 1 ELSE 0 END) as stok_habis,
    SUM(CASE WHEN stok > 0 AND stok <= 10 THEN 1 ELSE 0 END) as stok_sedikit,
    SUM(CASE WHEN stok > 10 THEN 1 ELSE 0 END) as stok_aman
FROM produk";

$statistik = mysqli_fetch_assoc(mysqli_query($connect, $qStatistik));
?>

<?php include '../../partials/header.php'; ?>
<?php $page = 'laporan_stok'; ?>
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

    /* Statistik Cards */
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
        text-align: center;
    }

    .stat-total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-aman {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-sedikit {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    }

    .stat-habis {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
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

    /* Stok rendah warning */
    .stok-rendah {
        background-color: #fff0f0 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #main {
            margin-left: 0;
            width: 100%;
        }

        .table-modern td,
        .table-modern th {
            padding: 8px;
        }
    }
</style>

<div class="container-fluid page-body-wrapper">
    <div id="main" class="p-4">
        <!-- Statistik Stok -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-total">
                    <div class="stat-number"><?= $statistik['total_produk'] ?></div>
                    <div class="stat-label">Total Produk</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-aman">
                    <div class="stat-number"><?= $statistik['stok_aman'] ?></div>
                    <div class="stat-label">Stok Aman</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-sedikit">
                    <div class="stat-number"><?= $statistik['stok_sedikit'] ?></div>
                    <div class="stat-label">Stok Sedikit</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-habis">
                    <div class="stat-number"><?= $statistik['stok_habis'] ?></div>
                    <div class="stat-label">Stok Habis</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold text-primary">📊 Laporan Stok Produk</h4>
                </div>

                <div class="table-responsive">
                    <table id="stokTable" class="table table-modern table-excel align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Status Stok</th>
                                <th>Peringatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)):
                                $stok_class = '';
                                $stok_text = '';
                                $peringatan = '';

                                if ($row['stok'] == 0) {
                                    $stok_class = 'stok-habis';
                                    $stok_text = 'HABIS';
                                    $peringatan = '⚠️ Stok habis, perlu restok segera!';
                                } elseif ($row['stok'] <= 10) {
                                    $stok_class = 'stok-sedikit';
                                    $stok_text = 'SEDIKIT';
                                    $peringatan = '⚠️ Stok menipis, perhatikan!';
                                } else {
                                    $stok_class = 'stok-aman';
                                    $stok_text = 'AMAN';
                                    $peringatan = '✅ Stok aman';
                                }
                            ?>
                                <tr class="<?= ($row['stok'] <= 10) ? 'stok-rendah' : '' ?>">
                                    <td><?= $no ?></td>
                                    <td><?= $row['kode_produk'] ?></td>
                                    <td>
                                        <div style="max-width: 200px; word-wrap: break-word;">
                                            <?= htmlspecialchars($row['nama_produk']) ?>
                                        </div>
                                    </td>
                                    <td><?= $row['nama_kategori'] ?? 'Tidak berkategori' ?></td>
                                    <td><strong><?= $row['stok'] ?></strong></td>
                                    <td>
                                        <span class="status-badge <?= $stok_class ?>">
                                            <?= $stok_text ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="<?= ($row['stok'] <= 10) ? 'text-danger' : 'text-success' ?>">
                                            <?= $peringatan ?>
                                        </small>
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
        var table = $('#stokTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    className: 'btn btn-success',
                    text: '📊 Excel',
                    title: 'Laporan Stok Produk'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger',
                    text: '📄 PDF',
                    title: 'Laporan Stok Produk',
                    orientation: 'landscape'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info',
                    text: '🖨️ Print',
                    title: 'Laporan Stok Produk',
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
                [4, 'asc']
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
</script>