<div class="left-side-bar">
    <div class="brand-logo text-center">
        <a href="#">
            <img src="../../../../storages/logo/logo.png" alt="Logo" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>

    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">

                <!-- Dashboard -->
                <li class="sidebar-item <?= (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : '' ?>">
                    <a href="../dashboard/index.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                <!-- Produk -->
                <li class="sidebar-item <?= ($page == 'produk') ? 'active' : '' ?>">
                    <a href="../produk/index.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-box"></span>
                        <span class="mtext">Produk</span>
                    </a>
                </li>

                <!-- Kategori -->
                <li class="sidebar-item <?= ($page == 'kategori') ? 'active' : '' ?>">
                    <a href="../kategori/index.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-list"></span>
                        <span class="mtext">Kategori</span>
                    </a>
                </li>

                <!-- Laporan -->
                <li class="dropdown <?= (in_array($page, ['laporan_stok', 'laporan_produk'])) ? 'active open' : '' ?>">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon dw dw-analytics-21"></span>
                        <span class="mtext">Laporan</span>
                        <span class="arrow dw dw-down"></span>
                    </a>
                    <ul class="submenu" style="<?= (in_array($page, ['laporan_stoks', 'laporan_produks'])) ? 'display:block;' : '' ?>">
                        <li>
                            <a href="../laporan_stok/index.php" class="<?= ($page == 'laporan_stoks') ? 'active' : '' ?>">Stok</a>
                        </li>
                        <li>
                            <a href="../laporan_produk/index.php" class="<?= ($page == 'laporan_produks') ? 'active' : '' ?>">Produk</a>
                        </li>
                    </ul>
                </li>

                <!-- Pengguna -->
                <li class="sidebar-item <?= ($page == 'pengguna') ? 'active' : '' ?>">
                    <a href="../user/index.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-user1"></span>
                        <span class="mtext">Pengguna</span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="sidebar-item">
                    <a href="../../../action/auth/logout.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-logout"></span>
                        <span class="mtext">Keluar</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<!-- CSS Sidebar Active -->
<style>
    /* Aktif di menu utama */
    .sidebar-item.active>a {
        background-color: #0d6efd;
        color: #fff !important;
        border-radius: 6px;
    }

    /* Aktif di dropdown */
    .submenu a.active {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-radius: 6px;
    }

    /* Warna dropdown saat terbuka */
    .dropdown.open>.dropdown-toggle {
        background-color: #0d6efd;
        color: #fff !important;
        border-radius: 6px;
    }

    /* Sembunyikan submenu default */
    .submenu {
        display: none;
        list-style: none;
        padding-left: 20px;
    }

    /* Tampilkan submenu saat open */
    .dropdown.open>.submenu {
        display: block;
    }

    /* Efek hover */
    .sidebar-menu a:hover {
        background-color: #e7f1ff;
        color: #0d6efd;
        border-radius: 6px;
    }

    .arrow {
        float: right;
        transition: transform 0.3s ease;
    }

    .dropdown.open .arrow {
        transform: rotate(180deg);
    }
</style>

<!-- JS untuk Expand/Collapse -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.dropdown > a.dropdown-toggle');

        dropdowns.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;

                // Tutup dropdown lain
                document.querySelectorAll('.dropdown').forEach(drop => {
                    if (drop !== parent) drop.classList.remove('open');
                });

                // Toggle dropdown sekarang
                parent.classList.toggle('open');
            });
        });
    });
</script>