<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../../../auth/login.php';
    </script>";
    exit;
}

// Cek hak akses untuk kasir dashboard  
if ($_SESSION['hak_akses'] != 'kasir') {
    echo "<script>
        alert('Anda tidak memiliki akses ke halaman ini!');
        window.location.href = '../../../auth/login.php';
    </script>";
    exit;
}

// Database config
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'uk2_db_pos_loko_campuran';

// koneksi
$koneksi = mysqli_connect($hostname, $username, $password, $database);
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data statistik untuk kasir
$id_kasir = $_SESSION['id_user'];

// Data untuk grafik transaksi 7 hari terakhir
$query_weekly_transactions = "SELECT 
    DATE(tanggal_transaksi) as tanggal,
    COUNT(*) as jumlah_transaksi,
    COALESCE(SUM(total_bayar), 0) as total_pendapatan
    FROM transaksi 
    WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND id_kasir = '$id_kasir'
    GROUP BY DATE(tanggal_transaksi)
    ORDER BY tanggal ASC";
$result_weekly = mysqli_query($koneksi, $query_weekly_transactions);
$weekly_data = [];
$labels = [];
$transaksi_data = [];
$pendapatan_data = [];

// Generate labels untuk 7 hari terakhir
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($date));
    $weekly_data[$date] = ['transaksi' => 0, 'pendapatan' => 0];
}

// Isi data dari database
while ($row = mysqli_fetch_assoc($result_weekly)) {
    $date = $row['tanggal'];
    $weekly_data[$date] = [
        'transaksi' => $row['jumlah_transaksi'],
        'pendapatan' => $row['total_pendapatan']
    ];
}

// Siapkan data untuk chart
foreach ($weekly_data as $data) {
    $transaksi_data[] = $data['transaksi'];
    $pendapatan_data[] = $data['pendapatan'];
}

// Data produk terlaris bulan ini
$query_top_products = "SELECT 
    p.nama_produk,
    SUM(dt.jumlah) as total_terjual
    FROM detail_transaksi dt
    JOIN produk p ON dt.id_produk = p.id_produk
    JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
    WHERE MONTH(t.tanggal_transaksi) = MONTH(CURDATE())
    AND YEAR(t.tanggal_transaksi) = YEAR(CURDATE())
    AND t.id_kasir = '$id_kasir'
    GROUP BY p.id_produk, p.nama_produk
    ORDER BY total_terjual DESC
    LIMIT 5";
$result_top_products = mysqli_query($koneksi, $query_top_products);
$top_products = [];
$product_names = [];
$product_sales = [];

while ($row = mysqli_fetch_assoc($result_top_products)) {
    $top_products[] = $row;
    $product_names[] = $row['nama_produk'];
    $product_sales[] = $row['total_terjual'];
}

// 5 transaksi terbaru
$query_recent_transactions = "SELECT t.kode_transaksi, t.tanggal_transaksi, t.total_bayar, 
                             u.nama_lengkap as nama_kasir
                             FROM transaksi t 
                             LEFT JOIN user u ON t.id_kasir = u.id_user 
                             WHERE t.id_kasir = '$id_kasir'
                             ORDER BY t.tanggal_transaksi DESC 
                             LIMIT 5";
$result_recent = mysqli_query($koneksi, $query_recent_transactions);
$recent_transactions = [];
while ($row = mysqli_fetch_assoc($result_recent)) {
    $recent_transactions[] = $row;
}

// Produk dengan stok menipis
$query_low_stock_list = "SELECT nama_produk, stok 
                       FROM produk 
                       WHERE stok > 0 AND stok <= 10 
                       ORDER BY stok ASC 
                       LIMIT 5";
$result_low_stock_list = mysqli_query($koneksi, $query_low_stock_list);
$low_stock_products = [];
while ($row = mysqli_fetch_assoc($result_low_stock_list)) {
    $low_stock_products[] = $row;
}

include "../../partials/header.php";
include "../../partials/sidebar.php";
?>

<?php $page = 'dashboard'; ?>

<!-- Content -->
<div class="main-container" style="margin-left:290px; margin-top:70px; padding:20px;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-primary mb-1">👋 Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Kasir') ?>!</h3>
                            <p class="text-muted mb-0">Dashboard Kasir - Analisis Penjualan</p>
                        </div>
                        <div class="text-right">
                            <div class="badge badge-success bg-success p-2">
                                <i class="fas fa-user me-1"></i>Kasir
                            </div>
                            <div class="text-muted small mt-1"><?= date('l, d F Y H:i:s') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Utama -->
    <div class="row mb-4">
        <!-- Grafik Transaksi 7 Hari Terakhir -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Transaksi 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="transactionChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Produk Terlaris -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🏆 Produk Terlaris Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="productChart" height="250"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php if (!empty($top_products)): ?>
                            <?php foreach ($top_products as $index => $product): ?>
                                <span class="mr-2">
                                    <i class="fas fa-circle" style="color: <?= getChartColor($index) ?>"></i>
                                    <?= htmlspecialchars($product['nama_produk']) ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Belum ada data penjualan</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaksi Terbaru -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Transaksi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_transactions)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-receipt fa-2x mb-2"></i>
                                            <p>Belum ada transaksi</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_transactions as $transaction): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($transaction['kode_transaksi']) ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($transaction['tanggal_transaksi'])) ?></td>
                                            <td class="text-success font-weight-bold">
                                                Rp <?= number_format($transaction['total_bayar'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Stok -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Info Stok Menipis</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($low_stock_products)): ?>
                        <div class="text-center text-success py-3">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>Semua stok aman</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($low_stock_products as $product): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="text-truncate" style="max-width: 70%;"><?= htmlspecialchars($product['nama_produk']) ?></span>
                                    <span class="badge bg-<?= $product['stok'] <= 5 ? 'danger' : 'warning' ?> rounded-pill">
                                        <?= $product['stok'] ?> stok
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Data dari PHP
    const chartLabels = <?= json_encode($labels) ?>;
    const transaksiData = <?= json_encode($transaksi_data) ?>;
    const pendapatanData = <?= json_encode($pendapatan_data) ?>;
    const productNames = <?= json_encode($product_names) ?>;
    const productSales = <?= json_encode($product_sales) ?>;

    // Grafik Transaksi
    const transactionCtx = document.getElementById('transactionChart').getContext('2d');
    const transactionChart = new Chart(transactionCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: transaksiData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            }, {
                label: 'Total Pendapatan (Rp)',
                data: pendapatanData,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Pendapatan (Rp)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Pendapatan')) {
                                return label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });

    // Grafik Produk Terlaris
    if (productNames.length > 0) {
        const productCtx = document.getElementById('productChart').getContext('2d');
        const productChart = new Chart(productCtx, {
            type: 'doughnut',
            data: {
                labels: productNames,
                datasets: [{
                    data: productSales,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#6f42c1', '#e83e8c', '#fd7e14'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b',
                        '#6b6d7d', '#484a54', '#5a32a3', '#d91a72', '#e56702'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' terjual';
                            }
                        }
                    }
                },
                cutout: '70%',
            },
        });
    } else {
        document.getElementById('productChart').innerHTML =
            '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><p>Belum ada data</p></div>';
    }
</script>

<!-- Modern CSS -->
<style>
    .main-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #e3f2fd;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeIn 0.5s ease-out;
    }

    .chart-area {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .chart-pie {
        position: relative;
        height: 250px;
        width: 100%;
    }
</style>

<?php
// Helper function untuk warna chart
function getChartColor($index)
{
    $colors = [
        '#4e73df',
        '#1cc88a',
        '#36b9cc',
        '#f6c23e',
        '#e74a3b',
        '#858796',
        '#5a5c69',
        '#6f42c1',
        '#e83e8c',
        '#fd7e14'
    ];
    return $colors[$index % count($colors)];
}

include "../../partials/script.php";
include "../../partials/footer.php";

// Tutup koneksi database
mysqli_close($koneksi);
?>