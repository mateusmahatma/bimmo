<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="/dashboard" class="logo d-flex align-items-center">
            <img src="/img/icon_pointech.png" alt="" class="icon-pointech" />
            <span>Pointech</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <!-- start navigation  -->
    <nav class="header-nav ms-auto extra-space">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li>

            <!-- Search -->
            <div class="search-bar center-search-bar">
            </div>

            <!-- Darkmode Switch -->
            <div class="dark-mode-dropdown-container">
                <select id="darkModeDropdown" class="dark-mode-dropdown">
                    <option value="light">
                        <div class="dark-mode-dropdown-icon">
                            Light
                        </div>
                    </option>
                    <option value="dark">
                        <div class="dark-mode-dropdown-icon">
                            <div>
                                Dark
                            </div>
                    </option>
                </select>
            </div>

            <!-- Garis Pemisah -->
            <div class="nav-link-divider"></div>

            <!-- Opsi -->
            <li class="dropdown">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <span class="dropdown">{{ auth()->user()->name }}<br>Pointech</span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/ubah-password">
                            <span>Ubah Password</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>