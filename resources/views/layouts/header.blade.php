<header id="header" class="header fixed-top" data-bs-theme="auto">
    <nav class="navbar navbar-expand-lg w-100">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center px-3" href="/dashboard">
            <img src="/img/bimmo_icon.png" alt="Logo" style="height: 26px; width: 26px;" class="me-2">
            <span class="fw-bold">BIMMO</span>
        </a>

        <!-- Toggle -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">Dasbor</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('anggaran', 'kalkulator') ? 'active' : '' }}"
                        href="#" data-bs-toggle="dropdown">Penganggaran</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/anggaran" data-pjax>Anggaran</a></li>
                        <li><a class="dropdown-item" href="{{ route('kalkulator.index') }}">Hasil Anggaran</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('barang', 'dana-darurat') ? 'active' : '' }}"
                        href="#" data-bs-toggle="dropdown">Investasi</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/barang">Daftar Aset</a></li>
                        <li><a class="dropdown-item" href="/dana-darurat">Dana Darurat</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('pemasukan', 'pengeluaran', 'transaksi', 'compare') ? 'active' : '' }}"
                        href="#" data-bs-toggle="dropdown">Pergerakan Uang</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/pemasukan">Jenis Pemasukan</a></li>
                        <li><a class="dropdown-item" href="/pengeluaran">Jenis Pengeluaran</a></li>
                        <li><a class="dropdown-item" href="/transaksi">Arus Kas</a></li>
                        <li><a class="dropdown-item" href="/pinjaman">Pinjaman</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('ubah-password') ? 'active' : '' }}"
                        href="#" data-bs-toggle="dropdown">Pengaturan</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/ubah-password">Ubah Kata Sandi</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Kanan -->
            <ul class="navbar-nav ms-auto d-flex align-items-center pe-3">
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown" title="Ganti Tema">
                        <i class="bi bi-lightbulb-fill" style="color: orange; font-size: 1.3em;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="setTheme('light')"><i class="bi bi-sun me-2"></i> Light</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setTheme('dark')"><i class="bi bi-moon me-2"></i> Dark</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setTheme('auto')"><i class="bi bi-circle-half me-2"></i> Auto</a></li>
                    </ul>
                </li>

                <li class="nav-item d-flex align-items-center">
                    <div class="vr mx-2" style="height: 24px;"></div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/logout" title="Keluar">
                        <img src="/icon/logout.png" alt="Logout" style="height: 22px; width: 22px;">
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>