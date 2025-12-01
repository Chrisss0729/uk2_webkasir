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
                <?php
                // Debug: Lihat apa yang terjadi
                $current_page = basename($_SERVER['PHP_SELF']);
                $current_dir = basename(dirname($_SERVER['PHP_SELF']));

                echo "<!-- Debug: Current Page: $current_page -->";
                echo "<!-- Debug: Current Directory: $current_dir -->";
                ?>

                <!-- Dashboard -->
                <li class="sidebar-item <?= (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : '' ?>">
                    <a href="../dashboard/index.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                <!-- Transaksi -->
                <li class="sidebar-item <?= (strpos($_SERVER['REQUEST_URI'], 'transaksi_penjualan') !== false || $current_page == 'transaksi.php') ? 'active' : '' ?>">
                    <a href="../transaksi_penjualan/transaksi.php" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-shopping-cart"></span>
                        <span class="mtext">Transaksi Penjualan</span>
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