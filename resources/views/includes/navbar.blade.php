<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-success" href="{{ route('dashboard') }}">
            <img src="{{ asset('logo.png') }}" alt="DompetKu" height="32" class="me-2">
            <span>DompetKu</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('kategori*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('kategori.index') }}">
                        <i class="bi bi-journal-text me-1"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('transaksi*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('transaksi.index') }}">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('riwayat*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('riwayat.index') }}">
                        <i class="bi bi-clock-history me-1"></i> Riwayat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('target*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('target.index') }}">
                        <i class="bi bi-bullseye me-1"></i> Target
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-dark" href="#"
                        role="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-person-fill text-success"></i>
                        </div>
                        <span class="d-none d-lg-inline">{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                <i class="bi bi-person-circle me-2 text-success"></i> Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
