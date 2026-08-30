<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" width="130" alt="{{ config('app.name') }}" class="navbar-brand-image">
            </a>
        </h1>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-secondary">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ Route::is('dashboard*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Dashboard
                        </span>
                    </a>
                </li>
                @canany(['lihat level', 'lihat user'])
                    <li class="nav-item {{ Route::is('roles*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('roles.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-accessible"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 16.5l2 -3l2 3m-2 -3v-2l3 -1m-6 0l3 1" /><circle cx="12" cy="7.5" r=".5" fill="currentColor" /></svg>
                            </span>
                            <span class="nav-link-title">
                                Data Level
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('user*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                            </span>
                            <span class="nav-link-title">
                                Data Users
                            </span>
                        </a>
                    </li>
                @endcanany
                @canany(['lihat gaji karyawan', 'lihat tunjangan'])
                    <li class="nav-item dropdown {{ Route::is(['salary*', 'allowance*']) ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="{{ Route::is(['salary*', 'allowance*']) ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash-register"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5" /><path d="M19 21v1m0 -8v1" /><path d="M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2" /><path d="M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1" /><path d="M8 14v.01" /><path d="M8 17v.01" /><path d="M12 13.99v.01" /><path d="M12 17v.01" /></svg>
                            </span>
                            <span class="nav-link-title">
                                Pengaturan Gaji
                            </span>
                        </a>
                        <div class="dropdown-menu {{ Route::is(['salary*', 'allowance*']) ? 'show' : '' }}">
                            @can('lihat tunjangan')
                                <a class="dropdown-item {{ Route::is('allowance.index') ? 'active' : '' }}" href="{{ route('allowance.index') }}" rel="noopener">
                                    Data Tunjangan
                                </a>
                            @endcan
                            @can('lihat gaji karyawan')
                                <a class="dropdown-item {{ Route::is('salary.index') ? 'active' : '' }}" href="{{ route('salary.index') }}" rel="noopener">
                                   Data Gaji
                                </a>
                            @endcan
                            @can('rekap gaji')
                                <a class="dropdown-item {{ Route::is('salary.recap') ? 'active' : '' }}" href="{{ route('salary.recap') }}" rel="noopener">
                                   Rekap Gaji Karyawan
                                </a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @canany(['lihat kasbon', 'approve kasbon'])
                    <li class="nav-item dropdown {{ Route::is(['cash.advance*', 'allowance*']) ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="{{ Route::is(['cash.advance*', 'allowance*']) ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash-banknote-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M12 18h-7a2 2 0 0 1 -2 -2v-8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7" /><path d="M18 12h.01" /><path d="M6 12h.01" /><path d="M16 19h6" /></svg>
                            </span>
                            <span class="nav-link-title">
                                Kasbon
                            </span>
                        </a>
                        <div class="dropdown-menu {{ Route::is(['cash.advance*', 'allowance*']) ? 'show' : '' }}">
                            @can('lihat kasbon')
                                <a class="dropdown-item {{ Route::is('cash.advance.index') ? 'active' : '' }}" href="{{ route('cash.advance.index') }}" rel="noopener">
                                   Pengajuan Kasbon
                                </a>
                            @endcan
                            @can('approve kasbon')
                                <a class="dropdown-item {{ Route::is('cash.advance.approval') ? 'active' : '' }}" href="{{ route('cash.advance.approval') }}" rel="noopener">
                                    Kasbon
                                </a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                @can('lihat pengeluaran')
                    <li class="nav-item {{ Route::is('expenditure.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('expenditure.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg>
                            </span>
                            <span class="nav-link-title">
                                Pengeluaran
                            </span>
                        </a>
                    </li>
                @endcan
                @canany(['lihat absensi', 'lihat pengaturan absensi', 'lihat pengajuan izin/cuti'])
                    <li class="nav-item dropdown {{ Route::is(['absen.setting*', 'koordinat*', 'leave*', 'absen.list*']) ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="{{ Route::is(['absen.setting*', 'koordinat*', 'leave*', 'absen.list*']) ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-time"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /><path d="M18 16.496v1.504l1 1" /></svg>
                            </span>
                            <span class="nav-link-title">
                                Absensi
                            </span>
                        </a>
                        <div class="dropdown-menu {{ Route::is(['absen.setting*', 'koordinat*', 'leave*', 'absen.list*']) ? 'show' : '' }}">
                            @can('lihat koordinat')
                                <a class="dropdown-item {{ Route::is('koordinat.index') ? 'active' : '' }}" href="{{ route('koordinat.index') }}" rel="noopener">
                                    Koordinat Lokasi Absen
                                </a>
                            @endcan
                            @can('rekap absensi')
                                <a class="dropdown-item {{ Route::is('absen.list.rekap') ? 'active' : '' }}" href="{{ route('absen.list.rekap') }}" rel="noopener">
                                    Laporan Absensi
                                </a>
                            @endcan
                            @can('lihat absensi')
                                <a class="dropdown-item {{ Route::is('absen.list.index') ? 'active' : '' }}" href="{{ route('absen.list.index') }}" rel="noopener">
                                    Absensi
                                </a>
                            @endcan
                            @can('lihat pengajuan izin/cuti')
                                <a class="dropdown-item {{ Route::is('leave.index') ? 'active' : '' }}" href="{{ route('leave.index') }}" rel="noopener">
                                    Pengajuan Izin/Cuti
                                </a>
                            @endcan
                        </div>
                    </li>
                @endcanany
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-logout-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" /><path d="M15 12h-12l3 -3" /><path d="M6 15l-3 -3" /></svg>
                        </span>
                        <span class="nav-link-title">
                            Logout
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>