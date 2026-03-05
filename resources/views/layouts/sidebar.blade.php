<aside class="sidebar d-flex flex-column">
    <div class="p-3 fw-bold fs-5 d-flex align-items-center justify-content-between border-bottom mb-2">
        <picture>
            <source srcset="{{ asset('img/bimmo_dark.png') }}" media="(prefers-color-scheme: dark)">
            <source srcset="{{ asset('img/bimmo_light.png') }}" media="(prefers-color-scheme: light)">
            <img src="{{ asset('img/bimmo_light.png') }}" class="me-2" style="height: 25px; width: 110px;">
        </picture>
    </div>

    <div class="sidebar-menu-items flex-grow-1">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}"
                    href="{{ url('dashboard') }}">
                    <i class="bi bi-speedometer me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('dompet*') ? 'active' : '' }}"
                    href="{{ route('dompet.index') }}">
                    <i class="bi bi-wallet2 me-2"></i>
                    <span>Wallet</span>
                </a>
            </li>

            <!-- Anggaran -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('anggaran*','kalkulator*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuAnggaran" role="button">
                    <i class="bi bi-calculator-fill me-2"></i>
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

            <!-- Assets -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('aset*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuAset" role="button">
                <i class="bi bi-box-seam me-2"></i>
                <span>Assets</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ Request::is('aset*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuAset">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('aset') ? 'active' : '' }}" href="{{ route('aset.index') }}">
                                <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('aset/report') ? 'active' : '' }}" href="{{ route('aset.report') }}">
                                <i class="bi bi-arrow-right-circle-fill me-2"></i>
                                Analysis & Report
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center {{ Request::is('dana-darurat*') ? 'active' : '' }}" href="/dana-darurat" role="button">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span>Emergency Fund</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center {{ Request::is('tujuan-keuangan*') ? 'active' : '' }}" href="/tujuan-keuangan" role="button">
            <i class="bi bi-bullseye me-2"></i>
            <span>Financial Goals</span>
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
                        Income
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sub-link {{ Request::is('pengeluaran') ? 'active' : '' }}" href="/pengeluaran">
                        <i class="bi bi-arrow-right-circle-fill me-2"></i>
                        Expense
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
                        Liability
                    </a>
                </li>
            </ul>
        </div>
    </li>
    </ul>
    </div>

    <div class="sidebar-bottom mt-auto border-top pt-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link"
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#feedbackModal"
                    data-bs-toggle="tooltip"
                    data-bs-placement="right"
                    title="Send suggestions or report issues to help improve the application">

                    <i class="bi bi-bug me-2"></i>
                    <span>Send Feedback</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="/logout">
                    <i class="bi bi-box-arrow-left me-2 text-danger"></i>
                    <span>Log Out</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('profil*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                    @if(Auth::user()->profile_photo)
                    <img src="{{ route('storage.profile_photo', ['filename' => basename(Auth::user()->profile_photo)]) }}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;" alt="Profile">
                    @else
                    <i class="bi bi-person-circle me-2"></i>
                    @endif
                    <span class="text-truncate" style="max-width: 150px;">{{ Auth::user()->name }}</span>
                </a>
            </li>
        </ul>
    </div>
</aside>