<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../../../pages/auth/login.php';
    </script>";
    exit;
}

// Cek hak akses untuk admin dashboard
if (strpos($_SERVER['PHP_SELF'], 'admin') !== false && $_SESSION['hak_akses'] != 'administrator') {
    echo "<script>
        alert('Anda tidak memiliki akses ke halaman ini!');
        window.location.href = '../../../pages/auth/login.php';
    </script>";
    exit;
}

include "../../app.php";

// Query untuk statistik dashboard
$qStatistik = "SELECT 
    (SELECT COUNT(*) FROM produk) as total_produk,
    (SELECT COUNT(*) FROM kategori_produk) as total_kategori,
    (SELECT COUNT(*) FROM user) as total_user,
    (SELECT COALESCE(SUM(total_bayar), 0) FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()) as pendapatan_hari_ini,
    (SELECT COUNT(*) FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()) as transaksi_hari_ini,
    (SELECT COALESCE(SUM(stok), 0) FROM produk) as total_stok";

$statistik = mysqli_fetch_assoc(mysqli_query($connect, $qStatistik));

// Query untuk grafik penjualan per bulan (12 bulan terakhir)
$qGrafikPenjualan = "SELECT 
    YEAR(tanggal_transaksi) as tahun,
    MONTH(tanggal_transaksi) as bulan,
    COUNT(*) as jumlah_transaksi,
    COALESCE(SUM(total_bayar), 0) as total_penjualan
FROM transaksi 
WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
GROUP BY YEAR(tanggal_transaksi), MONTH(tanggal_transaksi)
ORDER BY tahun ASC, bulan ASC";

$resultGrafik = mysqli_query($connect, $qGrafikPenjualan);

$labels = [];
$dataTransaksi = [];
$dataPenjualan = [];

// Array nama bulan dalam Bahasa Indonesia
$namaBulan = [
    1 => 'Jan',
    'Feb',
    'Mar',
    'Apr',
    'Mei',
    'Jun',
    'Jul',
    'Agu',
    'Sep',
    'Okt',
    'Nov',
    'Des'
];

// Buat array untuk 12 bulan terakhir
$currentMonth = (int)date('m');
$currentYear = (int)date('Y');
$allMonthsData = [];

// Inisialisasi semua bulan dengan nilai 0
for ($i = 11; $i >= 0; $i--) {
    $month = $currentMonth - $i;
    $year = $currentYear;

    if ($month < 1) {
        $month += 12;
        $year -= 1;
    }

    $allMonthsData[$year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT)] = [
        'tahun' => $year,
        'bulan' => $month,
        'jumlah_transaksi' => 0,
        'total_penjualan' => 0
    ];
}

// Isi dengan data yang ada dari database
while ($row = mysqli_fetch_assoc($resultGrafik)) {
    $key = $row['tahun'] . '-' . str_pad($row['bulan'], 2, '0', STR_PAD_LEFT);
    if (isset($allMonthsData[$key])) {
        $allMonthsData[$key] = $row;
    }
}

// Siapkan data untuk chart
foreach ($allMonthsData as $monthData) {
    $labels[] = $namaBulan[$monthData['bulan']] . ' ' . $monthData['tahun'];
    $dataTransaksi[] = $monthData['jumlah_transaksi'];
    $dataPenjualan[] = $monthData['total_penjualan'];
}

// Query untuk produk terlaris
$qProdukTerlaris = "SELECT 
    p.nama_produk,
    COALESCE(SUM(dt.jumlah), 0) as total_terjual,
    COALESCE(SUM(dt.jumlah * p.harga), 0) as total_penjualan
FROM produk p
LEFT JOIN detail_transaksi dt ON p.id_produk = dt.id_produk
GROUP BY p.id_produk, p.nama_produk
ORDER BY total_terjual DESC
LIMIT 5";

$produkTerlaris = mysqli_query($connect, $qProdukTerlaris);

// Query untuk stok menipis
$qStokMenipis = "SELECT 
    nama_produk,
    stok,
    harga
FROM produk 
WHERE stok <= 10 
ORDER BY stok ASC
LIMIT 5";

$stokMenipis = mysqli_query($connect, $qStokMenipis);

include "../../partials/header.php";
include "../../partials/sidebar.php";

$page = 'dashboard';
?>


<style>
    :root {
        --primary: #5b6bf0;
        --primary-light: #eef0ff;
        --secondary: #8b5cf6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #1f2937;
        --light: #f8fafc;
        --gray: #6b7280;
        --gray-light: #e5e7eb;
    }

    .main-container {
        margin-left: 290px;
        margin-top: 70px;
        padding: 24px;
        transition: all 0.3s ease;
        background-color: #f5f7fb;
        min-height: calc(100vh - 70px);
    }

    @media (max-width: 768px) {
        .main-container {
            margin-left: 0;
        }
    }

    .stat-card {
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        color: white;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-total {
        background: linear-gradient(135deg, #5b6bf0 0%, #8b5cf6 100%);
    }

    .stat-pendapatan {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stat-transaksi {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-stok {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 12px;
        opacity: 0.9;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .stat-label {
        font-size: 0.95rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .card-header {
        background: white;
        border-bottom: 1px solid var(--gray-light);
        font-weight: 600;
        color: var(--dark);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 24px;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-modern thead th {
        border: none;
        background: transparent;
        color: var(--gray);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
    }

    .table-modern tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        transition: all 0.2s ease-in-out;
    }

    .table-modern tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .table-modern td {
        border: none;
        padding: 16px;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .table-modern td:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .table-modern td:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .badge-stok {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .stok-aman {
        background-color: #e6f7ee;
        color: #10b981;
    }

    .stok-sedikit {
        background-color: #fff7e6;
        color: #f59e0b;
    }

    .stok-habis {
        background-color: #fff0f0;
        color: #ef4444;
    }

    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }

    .info-card {
        text-align: center;
        padding: 24px;
        transition: all 0.3s ease;
        border-radius: 16px;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .info-icon {
        font-size: 2.5rem;
        margin-bottom: 12px;
        display: inline-block;
        padding: 16px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary);
    }

    .info-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--dark);
    }

    .info-subtitle {
        color: var(--gray);
        font-size: 0.9rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 24px;
        position: relative;
        padding-bottom: 12px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--primary);
        border-radius: 3px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray);
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .view-more {
        color: var(--primary);
        font-weight: 500;
        text-decoration: none;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .view-more:hover {
        color: var(--secondary);
    }
</style>

<!-- Content -->
<div class="main-container">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title">Dashboard</h1>
        </div>
        <div class="text-muted">
            <?php echo date('l, d F Y'); ?>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-total">
                <div class="stat-icon">📦</div>
                <div class="stat-number"><?= $statistik['total_produk'] ?? 0 ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-pendapatan">
                <div class="stat-icon">💰</div>
                <div class="stat-number">Rp <?= number_format($statistik['pendapatan_hari_ini'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Pendapatan Hari Ini</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-transaksi">
                <div class="stat-icon">🛒</div>
                <div class="stat-number"><?= $statistik['transaksi_hari_ini'] ?? 0 ?></div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-stok">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?= $statistik['total_stok'] ?? 0 ?></div>
                <div class="stat-label">Total Stok</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Penjualan -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><span>📈</span> Grafik Penjualan 12 Bulan Terakhir</h5>
                    <a href="#" class="view-more">
                        Lihat Detail <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="penjualanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><span>🔥</span> Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Terjual</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($produkTerlaris) > 0):
                                    while ($produk = mysqli_fetch_assoc($produkTerlaris)):
                                ?>
                                        <tr>
                                            <td>
                                                <div style="max-width: 120px; word-wrap: break-word; font-weight: 500;">
                                                    <?= htmlspecialchars($produk['nama_produk']) ?>
                                                </div>
                                            </td>
                                            <td><strong><?= $produk['total_terjual'] ?></strong></td>
                                            <td>Rp <?= number_format($produk['total_penjualan'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">📦</div>
                                                <p>Belum ada data penjualan</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Stok Menipis -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><span>⚠️</span> Stok Menipis</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($stokMenipis) > 0):
                                    while ($stok = mysqli_fetch_assoc($stokMenipis)):
                                        $stok_class = '';
                                        $stok_text = '';

                                        if ($stok['stok'] == 0) {
                                            $stok_class = 'stok-habis';
                                            $stok_text = 'Habis';
                                        } elseif ($stok['stok'] <= 5) {
                                            $stok_class = 'stok-habis';
                                            $stok_text = 'Kritis';
                                        } else {
                                            $stok_class = 'stok-sedikit';
                                            $stok_text = 'Sedikit';
                                        }
                                ?>
                                        <tr>
                                            <td>
                                                <div style="max-width: 120px; word-wrap: break-word; font-weight: 500;">
                                                    <?= htmlspecialchars($stok['nama_produk']) ?>
                                                </div>
                                            </td>
                                            <td><strong><?= $stok['stok'] ?></strong></td>
                                            <td>
                                                <span class="badge-stok <?= $stok_class ?>">
                                                    <?= $stok_text ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">✅</div>
                                                <p>Stok semua produk aman</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Tambahan -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card info-card">
                <div class="info-icon">🏪</div>
                <h5 class="info-title">Sistem POS</h5>
                <p class="info-subtitle">Point of Sale Management</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card info-card">
                <div class="info-icon">📑</div>
                <h5 class="info-title"><?= $statistik['total_kategori'] ?? 0 ?> Kategori</h5>
                <p class="info-subtitle">Kategori Produk</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card info-card">
                <div class="info-icon">👥</div>
                <h5 class="info-title"><?= $statistik['total_user'] ?? 0 ?> Pengguna</h5>
                <p class="info-subtitle">Total User Sistem</p>
            </div>
        </div>
    </div>
</div>

<?php
include "../../partials/script.php";
?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Grafik Penjualan
    const ctx = document.getElementById('penjualanChart').getContext('2d');
    const penjualanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: <?= json_encode($dataTransaksi) ?>,
                backgroundColor: 'rgba(91, 107, 240, 0.7)',
                borderColor: 'rgba(91, 107, 240, 1)',
                borderWidth: 1,
                borderRadius: 6,
                yAxisID: 'y'
            }, {
                label: 'Total Penjualan (Rp)',
                data: <?= json_encode($dataPenjualan) ?>,
                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                borderColor: 'rgba(139, 92, 246, 1)',
                borderWidth: 2,
                type: 'line',
                tension: 0.3,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Penjualan (Rp)'
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label.includes('Penjualan')) {
                                return label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
</script>

<?php
include "../../partials/footer.php";
?>