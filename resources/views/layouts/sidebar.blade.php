<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <!-- Request untuk mengaktifkan halaman secara otomatis di side bar -->
            <a class="nav-link  {{ Request::is('dashboard') ? 'active' : 'collapsed' }}" href="/dashboard">
                <i class="bi bi-activity"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Menu Pengelolaan Keuangan-->
        <li class="nav-item">
            <a class="nav-link {{ Request::is('pemasukan', 'pengeluaran', 'anggaran', 'transaksi', 'compare','anggaran', 'kalkulator') ? 'active' : 'collapsed' }}" data-bs-target="#master" data-bs-toggle="collapse" href="#">
                <i class="bi bi-server"></i><span>Pengelolaan Keuangan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="master" class="nav-content {{ Request::is('pemasukan', 'pengeluaran', 'transaksi', 'compare', 'anggaran','pinjaman','kalkulator') ? 'show' : 'collapse' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a class="{{ Request::is('pemasukan') ? 'active' : 'collapsed' }}" href="/pemasukan">
                        <i class="bi bi-circle"></i><span>Jenis Pemasukan</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('pengeluaran') ? 'active' : 'collapsed' }}" href="/pengeluaran">
                        <i class="bi bi-circle"></i><span>Jenis Pengeluaran</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('transaksi', 'compare') ? 'active' : 'collapsed' }}" href="/transaksi">
                        <i class="bi bi-circle"></i><span>Data Transaksi</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('anggaran') ? 'active' : 'collapsed' }}" href="/anggaran">
                        <i class="bi bi-circle"></i><span>Anggaran</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('kalkulator') ? 'active' : 'collapsed' }}" href="/kalkulator">
                        <i class="bi bi-circle"></i><span>Kalkulator Anggaran</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('pinjaman') ? 'active' : 'collapsed' }}" href="/pinjaman">
                        <i class="bi bi-circle"></i><span>Data Pinjaman</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Menu Pengelolaan Stok Barang -->
        <li class="nav-item">
            <a class="nav-link {{ Request::is('barang') ? 'active' : 'collapsed' }}" data-bs-target="#data" data-bs-toggle="collapse" href="#">
                <i class="bi bi-clipboard2-data"></i><span>Pengelolaan Stok Barang</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="data" class="nav-content {{ Request::is('barang', '') ? 'show' : 'collapse' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a class="{{ Request::is('barang') ? 'active' : 'collapsed' }}" href="/barang">
                        <i class="bi bi-circle"></i><span>Barang</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Log Out -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="/logout">
                <i class="bi bi-power" style="color: red"></i>
                <span style="color: red">Keluar</span>
            </a>
        </li>
    </ul>
</aside>