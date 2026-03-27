<aside class="sidebar d-flex flex-column">
    <div class="sidebar-header p-4 d-flex align-items-center justify-content-between border-bottom mb-2">
        <a href="/dashboard">
            <img src="{{ asset('img/bimmo_light.png') }}" class="sidebar-logo me-2" style="height: 22px; width: auto;" alt="BIMMO">
        </a>
        <div class="d-flex align-items-center gap-1">
            <button class="btn btn-sm p-1 px-2 {{ (auth()->user()->language ?? 'en') == 'id' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                    style="font-size: 0.65rem; border-radius: 6px;" 
                    onclick="updateLanguage('id')">ID</button>
            <button class="btn btn-sm p-1 px-2 {{ (auth()->user()->language ?? 'en') == 'en' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                    style="font-size: 0.65rem; border-radius: 6px;" 
                    onclick="updateLanguage('en')">EN</button>
        </div>
    </div>

    <div class="sidebar-menu-items flex-grow-1 py-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}"
                    href="{{ url('dashboard') }}">
                    <i class="bi bi-speedometer me-3"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('dompet*') ? 'active' : '' }}"
                    href="{{ route('dompet.index') }}">
                    <i class="bi bi-wallet2 me-3"></i>
                    <span>{{ __('Wallet') }}</span>
                </a>
            </li>

            <!-- Anggaran -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('anggaran*','kalkulator*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuAnggaran" role="button">
                    <i class="bi bi-calculator-fill me-3"></i>
                    <span>{{ __('Budget') }}</span>
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ Request::is('anggaran*','kalkulator*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuAnggaran">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('anggaran') ? 'active' : '' }}" href="/anggaran">
                                {{ __('Budget Categories') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('kalkulator') ? 'active' : '' }}" href="/kalkulator">
                                {{ __('Budget Monitoring') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Assets -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('aset*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuAset" role="button">
                <i class="bi bi-box-seam me-3"></i>
                <span>{{ __('Assets') }}</span>
                <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ Request::is('aset*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuAset">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('aset') ? 'active' : '' }}" href="{{ route('aset.index') }}">
                                {{ __('Inventory') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('aset/report') ? 'active' : '' }}" href="{{ route('aset.report') }}">
                                {{ __('Analysis & Report') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('dana-darurat*') ? 'active' : '' }}" href="/dana-darurat" role="button">
                    <i class="bi bi-exclamation-triangle-fill me-3"></i>
                    <span>{{ __('Emergency Fund') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('tujuan-keuangan*') ? 'active' : '' }}" href="/tujuan-keuangan" role="button">
                    <i class="bi bi-bullseye me-3"></i>
                    <span>{{ __('Financial Goals') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('threads*') ? 'active' : '' }}" href="{{ route('threads.index') }}" role="button">
                    <i class="bi bi-chat-left-text me-3"></i>
                    <span>{{ __('Threads') }}</span>
                </a>
            </li>

            <!-- Money Movement -->
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ Request::is('pemasukan*','pengeluaran*','transaksi*', 'pinjaman*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#{{ $prefix ?? '' }}menuMoneyMovement" role="button">
                    <i class="bi bi-arrow-down-up me-3"></i>
                    <span>{{ __('Money Movement') }}</span>
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ Request::is('pemasukan*','pengeluaran*','transaksi*', 'pinjaman*') ? 'show' : '' }}" id="{{ $prefix ?? '' }}menuMoneyMovement">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('pemasukan') ? 'active' : '' }}" href="/pemasukan">
                                {{ __('Income') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('pengeluaran') ? 'active' : '' }}" href="/pengeluaran">
                                {{ __('Expense') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('transaksi') ? 'active' : '' }}" href="/transaksi">
                                {{ __('Cash Flow') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sub-link {{ Request::is('pinjaman') ? 'active' : '' }}" href="/pinjaman">
                                {{ __('Loan') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <div class="sidebar-bottom mt-auto border-top pt-2">
        {{-- Baris compact: User Guide | Send Feedback | Donate --}}
        <div class="d-flex align-items-center justify-content-around px-2 pb-1">
            <a href="{{ route('panduan.index') }}"
               class="sidebar-compact-btn {{ request()->is('panduan*') ? 'active' : '' }}"
               title="{{ __('User Guide') }}">
                <i class="bi bi-book"></i>
                <span>{{ __('Guide') }}</span>
            </a>
            <a href="#" class="sidebar-compact-btn"
               data-bs-toggle="modal" data-bs-target="#feedbackModal"
               title="{{ __('Send Feedback') }}">
                <i class="bi bi-bug"></i>
                <span>{{ __('Feedback') }}</span>
            </a>
            <a href="#" class="sidebar-compact-btn text-success"
               data-bs-toggle="modal" data-bs-target="#donateModal"
               title="{{ __('Donate') }}">
                <i class="bi bi-heart-fill"></i>
                <span>{{ __('Donate') }}</span>
            </a>
        </div>

        {{-- Profile & Logout --}}
        <ul class="nav flex-column border-top pt-1">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('profil*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                    @if(Auth::user()->profile_photo)
                    <img src="{{ route('storage.profile_photo', ['filename' => basename(Auth::user()->profile_photo)]) }}" class="rounded-circle me-3" style="width: 24px; height: 24px; object-fit: cover;" alt="Profile">
                    @else
                    <i class="bi bi-person-circle me-3"></i>
                    @endif
                    <span class="text-truncate" style="max-width: 140px;">{{ Auth::user()->name }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="/logout">
                    <i class="bi bi-box-arrow-left me-3"></i>
                    <span>{{ __('Log Out') }}</span>
                </a>
            </li>
        </ul>
    </div>
</aside>