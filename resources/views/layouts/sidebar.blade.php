<aside class="sidebar">
    <div class="p-3 fw-bold fs-5 d-flex align-items-center justify-content-between">
        <img src="{{ asset('img/bimmo_2.png') }}" class="me-2" style="height: 25px; width: 110px;" alt="BIMMO Logo">
        <a href="{{ route('profil.index') }}" class="text-decoration-none text-dark" title="Profil">
            <i class="bi bi-person-circle fs-5"></i>
        </a>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}"
                href="{{ url('dashboard') }}">
                <i class="bi bi-speedometer me-2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Anggaran -->
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ Request::is('anggaran*','kalkulator*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuAnggaran" role="button">
                <i class="bi bi-wallet2 me-2"></i>
                <span>Budget</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>

            <div class="collapse {{ Request::is('anggaran*','kalkulator*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuAnggaran">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('anggaran') ? 'active' : '' }}" href="/anggaran">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Budget Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('kalkulator') ? 'active' : '' }}" href="/kalkulator">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Budget Monitoring
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ Request::is('barang*') ? 'active' : '' }} disabled"
                data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuInvestasi" role="button">
                <i class="bi bi-clipboard-data-fill me-2 disabled">
                </i> <span>Investment</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>

            <div class="collapse {{ Request::is('barang*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuInvestasi">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('barang') ? 'active' : '' }}" href="/barang" class="disabled">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Assets
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ Request::is('dana-darurat*') ? 'active' : '' }}" href="/dana-darurat" role="button">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                </i> <span>Emergency Fund</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center {{ Request::is('pemasukan*','pengeluaran*','transaksi*', 'pinjaman*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuMoneyMovement" role="button">
                <i class="bi bi-arrow-down-up me-2"></i>
                <span>Money Movement</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>

            <div class="collapse {{ Request::is('pemasukan*','pengeluaran*','transaksi*', 'pinjaman*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuMoneyMovement">
                <ul class="nav flex-column ms-4">
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('pemasukan') ? 'active' : '' }}" href="/pemasukan">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Income Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('pengeluaran') ? 'active' : '' }}" href="/pengeluaran">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Expense Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('transaksi') ? 'active' : '' }}" href="/transaksi">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Cash Flow
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sub-link {{ Request::is('pinjaman') ? 'active' : '' }}" href="/pinjaman">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>
                            Loan
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link text-danger" href="/logout">
                <i class="bi bi-box-arrow-left me-2 text-danger"></i>
                Log Out
            </a>
        </li>
    </ul>
</aside>