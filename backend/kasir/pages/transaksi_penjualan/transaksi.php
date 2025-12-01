<?php
// File: kasir.php (letakkan di folder yang sesuai)
// Pastikan session_start di paling atas sebelum output HTML apapun
session_start();

include "../../partials/sidebar.php";
include "../../partials/header.php";

$page = 'transaksi';

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

// Ambil id kasir dari session (fallback ke user pertama jika tidak ada)
$id_kasir = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;

// Helper PHP: konversi harga string -> number (assume input bisa "Rp 5.000" atau "5000")
function hargaToNumber($harga_string)
{
    // ambil digit saja
    $only_digits = preg_replace('/[^\d]/', '', $harga_string);
    return intval($only_digits);
}

function formatHarga($angka)
{
    return 'Rp ' . number_format(intval($angka), 0, ',', '.');
}

// Ambil daftar produk (stok > 0)
$query = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id_kategori WHERE p.stok > 0 ORDER BY k.nama_kategori, p.nama_produk";
$res = mysqli_query($koneksi, $query);
$produk = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        // pastikan harga angka ada
        if (!isset($r['harga_angka'])) {
            $r['harga_angka'] = hargaToNumber($r['harga']);
        }
        $produk[] = $r;
    }
}

// Validasi id_kasir; jika tidak valid ambil user kasir pertama
if ($id_kasir <= 0) {
    $qk = "SELECT id_user FROM user WHERE hak_akses IN ('kasir','administrator') LIMIT 1";
    $rk = mysqli_query($koneksi, $qk);
    if ($rk && mysqli_num_rows($rk) > 0) {
        $d = mysqli_fetch_assoc($rk);
        $id_kasir = intval($d['id_user']);
    } else {
        $id_kasir = 1; // fallback
    }
}
?>

<!-- ========== UI MODERN ========= -->
<div class="main-container" style="margin-left:290px; margin-top:70px; padding:20px;">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-primary mb-1">💳 Web Kasir</h3>
                        </div>
                        <div class="text-right">
                            <div class="badge badge-success bg-success p-2">
                                <i class="fas fa-user me-1"></i>Kasir: <?= htmlspecialchars($id_kasir) ?>
                            </div>
                            <div class="text-muted small mt-1"><?= date('d/m/Y H:i:s') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM TRANSAKSI -->
            <form method="POST" action="transaksi_action.php" id="transactionForm">
                <div class="row">
                    <!-- Left: Produk -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-transparent py-3">
                                <h5 class="mb-0">🛍️ Daftar Produk</h5>
                                <div class="input-group mt-2">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input id="searchProduk" type="text" class="form-control border-start-0" placeholder="Cari produk (nama / kategori)...">
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="product-grid row g-3" id="productGrid" style="max-height:65vh; overflow-y:auto;">
                                    <?php if (empty($produk)): ?>
                                        <div class="col-12 text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                                <p>Tidak ada produk tersedia</p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($produk as $p): ?>
                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                <div class="card product-card h-100 border-0 shadow-sm"
                                                    onclick="showProductModal(<?= intval($p['id_produk']) ?>, '<?= addslashes(htmlspecialchars($p['nama_produk'])) ?>', '<?= addslashes($p['harga']) ?>', <?= intval($p['stok']) ?>, '<?= addslashes($p['foto']) ?>')">
                                                    <div class="card-body p-3 text-center">
                                                        <div class="product-image mb-3">
                                                            <?php if (!empty($p['foto'])): ?>
                                                                <img src="../../../../storages/produk/<?= htmlspecialchars($p['foto']) ?>"
                                                                    class="img-fluid rounded"
                                                                    style="width:120px; height:120px; object-fit:cover;"
                                                                    alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                                                            <?php else: ?>
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto"
                                                                    style="width:120px; height:120px;">
                                                                    <i class="fas fa-image text-muted fa-2x"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h6 class="product-name fw-bold mb-2" style="font-size:14px; line-height:1.3;">
                                                            <?= htmlspecialchars($p['nama_produk']) ?>
                                                        </h6>
                                                        <div class="product-price text-success fw-bold mb-2">
                                                            <?= formatHarga($p['harga_angka']) ?>
                                                        </div>
                                                        <div class="product-stock">
                                                            <span class="badge bg-<?= $p['stok'] > 10 ? 'success' : ($p['stok'] > 5 ? 'warning' : 'danger') ?>">
                                                                Stok: <?= intval($p['stok']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Keranjang -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0">🛒 Keranjang Belanja</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height:45vh; overflow-y:auto;">
                                    <table class="table table-hover mb-0" id="cartTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40%">Produk</th>
                                                <th width="20%" class="text-center">Qty</th>
                                                <th width="20%" class="text-end">Subtotal</th>
                                                <th width="20%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cartItems">
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <p>Keranjang kosong</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Summary -->
                                <div class="border-top p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">Total:</span>
                                        <span class="fw-bold text-primary fs-5" id="totalAmount">Rp 0</span>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">💵 Jumlah Bayar</label>
                                        <input type="text" class="form-control form-control-lg"
                                            id="jumlah_bayar" name="jumlah_bayar"
                                            placeholder="0"
                                            oninput="formatRupiah(this); hitungKembalian();">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">🔄 Kembalian</label>
                                        <input type="text" class="form-control form-control-lg"
                                            id="kembalian" readonly
                                            style="background-color:#f8f9fa; font-weight:bold;">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-danger btn-lg"
                                            onclick="resetCart()">
                                            <i class="fas fa-trash me-2"></i>Reset
                                        </button>
                                        <button type="submit" name="proses_transaksi"
                                            class="btn btn-success btn-lg py-3"
                                            id="btnProses" disabled>
                                            <i class="fas fa-paper-plane me-2"></i>Proses Transaksi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Produk -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">➕ Tambah ke Keranjang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="modalImage" class="mb-3"></div>
                <h4 id="modalName" class="mb-2"></h4>
                <div id="modalPrice" class="text-success fs-5 fw-bold mb-3"></div>

                <div class="quantity-selector mb-3">
                    <label class="form-label fw-bold">Jumlah:</label>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-outline-primary rounded-circle"
                            onclick="modalChangeQty(-1)"
                            style="width:45px; height:45px;">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input id="modalQty" class="form-control text-center mx-3"
                            style="width:100px; font-size:1.2rem; font-weight:bold;"
                            value="1" readonly>
                        <button type="button" class="btn btn-outline-primary rounded-circle"
                            onclick="modalChangeQty(1)"
                            style="width:45px; height:45px;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <small id="modalStock" class="text-muted"></small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="addToCartFromModal()">
                    <i class="fas fa-cart-plus me-2"></i>Tambahkan
                </button>
            </div>
        </div>
    </div>
</div>

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
    }

    .product-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e3f2fd;
        border-radius: 12px;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        border-color: #2196f3;
    }

    .product-image {
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
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

    .btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-lg {
        padding: 12px 24px;
        font-size: 1.1rem;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e3f2fd;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #2196f3;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    }

    .badge {
        border-radius: 8px;
        font-weight: 500;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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
</style>

<!-- JavaScript -->
<script>
    // Data produk dari server (untuk cek stok di JS)
    const PRODUK_LIST = <?= json_encode($produk, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    // Cart client-side
    let cart = [];
    let total = 0;
    let currentProduct = null;
    let productModal = null;

    // Utility format rupiah
    function formatRupiahInput(input) {
        let v = input.value.replace(/\D/g, '');
        if (v === '') {
            input.value = '';
            return;
        }
        input.value = parseInt(v).toLocaleString('id-ID');
    }

    function formatRupiah(input) {
        formatRupiahInput(input);
    }

    function rupiahToNumber(str) {
        if (!str) return 0;
        return parseInt(str.toString().replace(/\D/g, '') || 0);
    }

    function numberToRupiah(n) {
        return 'Rp ' + (parseInt(n) || 0).toLocaleString('id-ID');
    }

    // Filter produk berdasarkan search
    function filterProduk() {
        const q = document.getElementById('searchProduk').value.toLowerCase();
        document.querySelectorAll('#productGrid .product-card').forEach(card => {
            const text = card.innerText.toLowerCase();
            card.parentElement.style.display = text.includes(q) ? '' : 'none';
        });
    }

    // Modal produk functions
    function showProductModal(id, name, harga, stok, foto) {
        currentProduct = {
            id,
            name,
            harga,
            stok,
            foto
        };

        document.getElementById('modalName').textContent = name;
        document.getElementById('modalPrice').textContent = harga;
        document.getElementById('modalQty').value = 1;
        document.getElementById('modalStock').textContent = 'Stok tersedia: ' + stok;

        const imgBox = document.getElementById('modalImage');
        if (foto) {
            imgBox.innerHTML = `<img src="../../../../storages/produk/${foto}" style="width:180px;height:180px;object-fit:cover;border-radius:12px;border:3px solid #e3f2fd;">`;
        } else {
            imgBox.innerHTML = `<div style="width:180px;height:180px;background:linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);display:flex;align-items:center;justify-content:center;border-radius:12px;border:3px solid #e3f2fd;"><i class="fas fa-image text-muted fa-3x"></i></div>`;
        }

        productModal.show();
    }

    function modalChangeQty(delta) {
        const el = document.getElementById('modalQty');
        let v = parseInt(el.value);
        if (isNaN(v)) v = 1;
        const inCart = (cart.find(i => i.id === currentProduct.id) || {
            jumlah: 0
        }).jumlah;
        const max = currentProduct ? currentProduct.stok - inCart : 100;
        v += delta;
        if (v < 1) v = 1;
        if (v > max) {
            alert('Stok tidak mencukupi.');
            v = max;
        }
        el.value = v;
    }

    function addToCartFromModal() {
        if (!currentProduct) {
            alert('Produk tidak ditemukan!');
            return;
        }

        const qty = parseInt(document.getElementById('modalQty').value);
        if (qty <= 0) {
            alert('Jumlah harus lebih dari 0!');
            return;
        }

        // Cek stok
        const idx = cart.findIndex(i => i.id === currentProduct.id);
        const harga_angka = rupiahToNumber(currentProduct.harga);

        if (idx >= 0) {
            if (cart[idx].jumlah + qty > currentProduct.stok) {
                alert('Stok tidak mencukupi.');
                return;
            }
            cart[idx].jumlah += qty;
        } else {
            if (qty > currentProduct.stok) {
                alert('Stok tidak mencukupi.');
                return;
            }
            cart.push({
                id: currentProduct.id,
                nama: currentProduct.name,
                harga: currentProduct.harga,
                harga_angka: harga_angka,
                jumlah: qty
            });
        }

        // Tutup modal
        productModal.hide();
        updateCartDisplay();
        showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
    }

    // Cart functions
    function updateCartDisplay() {
        const tbody = document.getElementById('cartItems');
        tbody.innerHTML = '';
        total = 0;

        if (cart.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-shopping-cart fa-2x mb-2"></i><p>Keranjang kosong</p></td></tr>`;
            document.getElementById('btnProses').disabled = true;
        } else {
            cart.forEach((it, i) => {
                const subtotal = it.harga_angka * it.jumlah;
                total += subtotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="vertical-align:middle">
                        <div class="fw-bold">${it.nama}</div>
                        <small class="text-muted">${it.harga}</small>
                    </td>
                    <td style="vertical-align:middle" class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" type="button" onclick="changeQty(${i}, -1)">-</button>
                            <button class="btn btn-light" style="min-width:40px;" disabled>${it.jumlah}</button>
                            <button class="btn btn-outline-primary" type="button" onclick="changeQty(${i}, 1)">+</button>
                        </div>
                    </td>
                    <td style="vertical-align:middle" class="text-end fw-bold text-success">
                        ${numberToRupiah(subtotal)}
                    </td>
                    <td style="vertical-align:middle" class="text-center">
                        <button class="btn btn-danger btn-sm rounded-circle" onclick="removeFromCart(${i})" style="width:35px; height:35px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            document.getElementById('btnProses').disabled = false;
        }

        document.getElementById('totalAmount').textContent = numberToRupiah(total);
        hitungKembalian();
        updateFormFields();
    }

    function changeQty(index, delta) {
        const it = cart[index];
        const productInfo = PRODUK_LIST.find(p => p.id_produk == it.id);
        const max = productInfo ? parseInt(productInfo.stok) : 9999;
        cart[index].jumlah += delta;
        if (cart[index].jumlah < 1) cart[index].jumlah = 1;
        if (cart[index].jumlah > max) {
            alert('Stok tidak mencukupi');
            cart[index].jumlah = max;
        }
        updateCartDisplay();
    }

    function removeFromCart(i) {
        cart.splice(i, 1);
        updateCartDisplay();
        showNotification('Produk dihapus dari keranjang', 'warning');
    }

    function resetCart() {
        if (confirm('Reset semua item keranjang?')) {
            cart = [];
            updateCartDisplay();
            showNotification('Keranjang berhasil direset', 'info');
        }
    }

    // hitung kembalian
    function hitungKembalian() {
        const bayar = rupiahToNumber(document.getElementById('jumlah_bayar').value);
        const kembali = bayar - total;
        const el = document.getElementById('kembalian');
        if (kembali >= 0) {
            el.value = numberToRupiah(kembali);
            el.style.color = '#198754';
        } else {
            el.value = 'Kurang: ' + numberToRupiah(Math.abs(kembali));
            el.style.color = '#dc3545';
        }
    }

    // update hidden form fields sebelum submit
    function updateFormFields() {
        // remove existing hidden inputs
        document.querySelectorAll('input[name="produk_id[]"], input[name="jumlah[]"], input[name="harga[]"]').forEach(n => n.remove());
        const form = document.getElementById('transactionForm');
        cart.forEach(it => {
            const i1 = document.createElement('input');
            i1.type = 'hidden';
            i1.name = 'produk_id[]';
            i1.value = it.id;
            form.appendChild(i1);

            const i2 = document.createElement('input');
            i2.type = 'hidden';
            i2.name = 'jumlah[]';
            i2.value = it.jumlah;
            form.appendChild(i2);

            const i3 = document.createElement('input');
            i3.type = 'hidden';
            i3.name = 'harga[]';
            i3.value = it.harga_angka;
            form.appendChild(i3);
        });
    }

    // before submit ensure form fields updated
    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Keranjang kosong!');
            return false;
        }

        const bayar = rupiahToNumber(document.getElementById('jumlah_bayar').value);
        if (bayar < total) {
            e.preventDefault();
            alert('Jumlah bayar kurang dari total!');
            return false;
        }

        updateFormFields();
        return true;
    });

    // Notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal
        productModal = new bootstrap.Modal(document.getElementById('productModal'));

        updateCartDisplay();

        // attach formatting event to jumlah_bayar
        document.getElementById('jumlah_bayar').addEventListener('input', function() {
            formatRupiah(this);
            hitungKembalian();
        });

        // Add search functionality
        document.getElementById('searchProduk').addEventListener('input', filterProduk);
    });
</script>

<?php
include "../../partials/script.php";

include "../../partials/footer.php";
?>