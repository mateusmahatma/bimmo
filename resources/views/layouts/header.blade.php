<header id="header" class="header fixed-top d-flex align-items-center shadow">
    <img src="/img/bimmo_icon.png" style="height: 22px; width: 22px;" />
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link {{ Request::is('dashboard') ? 'active' : 'collapsed' }}" href="/dashboard">Dashboard</a>
        </li>

        <!-- Budgeting -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('anggaran', 'kalkulator') ? 'active' : 'collapsed' }}" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span>Budgeting</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item {{ Request::is('anggaran') ? 'active' : '' }}" href="/anggaran">
                        <span>Budget List</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('kalkulator') ? 'active' : '' }}" href="/kalkulator">
                        <span>Budget Results</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Investment -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('barang', 'dana-darurat') ? 'active' : 'collapsed' }}" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span>Investment</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item {{ Request::is('barang') ? 'active' : '' }}" href="/barang">
                        <span>Asset List</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('dana-darurat') ? 'active' : '' }}" href="/dana-darurat">
                        <span>Emergency Fund</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Transaksi -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('pemasukan', 'pengeluaran', 'transaksi', 'compare') ? 'active' : 'collapsed' }}" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span>Money Movement</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item {{ Request::is('pemasukan') ? 'active' : '' }}" href="/pemasukan">
                        <span>Income Type</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('pengeluaran') ? 'active' : '' }}" href="/pengeluaran">
                        <span>Expense Type</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('transaksi', 'compare') ? 'active' : '' }}" href="/transaksi">
                        <span>Cash Flow</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ Request::is('pinjaman') ? 'active' : '' }}" href="/pinjaman">
                        <span>Loans</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Pengaturan -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ Request::is('ubah-password') ? 'active' : 'collapsed' }}" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span>Setting</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item {{ Request::is('ubah-password') ? 'active' : '' }}" href="/ubah-password">
                        <span>Change Password</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <!-- Log out  -->
    <nav class="header-nav ms-auto extra-space">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li>

            <div class="search-bar center-search-bar">
            </div>

            <!-- Dropdown Tema -->
            <li class="nav-item dropdown">
                <a
                    class="nav-link"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title="Ganti Tema">
                    <i class="bi bi-lightbulb-fill" style="color: orange; font-size: 1.5em;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="setTheme('light')"><i class="bi bi-sun me-2"></i> Light</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setTheme('dark')"><i class="bi bi-moon me-2"></i> Dark</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setTheme('auto')"><i class="bi bi-circle-half me-2"></i> Auto</a></li>
                </ul>
            </li>

            <!-- Garis pemisah -->
            <li class="nav-item d-flex align-items-center">
                <div class="vr mx-2" style="height: 24px;"></div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="/logout">
                    <img src="/icon/logout.png" alt="Logout" style="height: 22px; width: 22px;">
                </a>
            </li>
        </ul>
    </nav>
</header>