<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
    <div class="container-fluid navbar-inner">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP"
                        style="width: 40px; height: 40px; object-fit: contain;">
                </div>
            </div>
            <h4 class="logo-title ms-2">Inventori ATK</h4>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20px" class="icon-20" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
            </i>
        </div>
        <div class="input-group search-input d-none d-md-flex">
            <span class="input-group-text">
                <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"></circle>
                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"></path>
                </svg>
            </span>
            <input type="search" id="navbar-search" class="form-control" placeholder="Cari data persediaan atau transaksi...">
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon">
                <span class="mt-2 navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <div class="avatar avatar-40 rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center border border-primary-subtle">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="caption ms-3 d-none d-md-block text-start">
                            <h6 class="mb-0 caption-title fw-bold text-dark">{{ auth()->user()->name ?? 'Pengguna' }}</h6>
                            <div class="d-flex align-items-center gap-1 mt-0.5">
                                @php
                                    $userRole = auth()->user()->role ?? 'pegawai';
                                    $roleBadge = match ($userRole) {
                                        'kasubag_tu', 'admin' => 'bg-primary text-white',
                                        'arsiparis' => 'bg-info text-white',
                                        'pic_distribusi' => 'bg-warning text-dark',
                                        'subdit' => 'bg-success text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                    $roleTitle = match ($userRole) {
                                        'kasubag_tu', 'admin' => 'Kasubag TU',
                                        'arsiparis' => 'Arsiparis',
                                        'pic_distribusi' => 'PIC Distribusi',
                                        'subdit' => 'Subdirektorat',
                                        default => 'Pegawai',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadge }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                    {{ $roleTitle }}
                                </span>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdown">
                        <li class="px-3 py-2 border-bottom d-md-none">
                            <strong>{{ auth()->user()->name }}</strong>
                            <div class="small text-muted">{{ $roleTitle }}</div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">Profil Saya</a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.password') }}">Ubah Password</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
