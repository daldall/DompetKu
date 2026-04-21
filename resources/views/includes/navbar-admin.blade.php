<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-success" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('logo.png') }}" alt="DompetKu" height="32" class="me-2">
            <span>DompetKu</span>
            <span class="badge text-bg-success ms-2">Admin</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-1"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.ai-usage.*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('admin.ai-usage.index') }}">
                        <i class="bi bi-cpu me-1"></i> AI Usage
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.default-kategori.*') ? 'active fw-semibold text-dark border-bottom border-2 border-success' : 'text-secondary' }}"
                        href="{{ route('admin.default-kategori.index') }}">
                        <i class="bi bi-tags me-1"></i> Kategori Default
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-dark" href="#" role="button"
                        data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                            style="width: 32px; height: 32px;">
                            <i class="bi bi-person-fill text-success"></i>
                        </div>
                        <span class="d-none d-lg-inline">{{ Auth::user()->nama ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                <i class="bi bi-arrow-left-circle me-2 text-success"></i> Kembali ke Aplikasi
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
