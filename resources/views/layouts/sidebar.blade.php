<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{asset('assets/img/logo_bkd.png')}}" height="50">
            </span>
            <!-- <span class="demo menu-text fw-bolder text-primary ms-2">Beban Kerja Dosen</span> -->
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Dashboard</span>
        </li>
        <li class="menu-item {{ Route::currentRouteName()=='home' ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">Halaman Utama</div>
            </a>
        </li>

        <!-- Apps & Pages -->
        @if(Auth::user()->hasRole(2))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">BKD/LKD Dosen</span>
        </li>

        <li class="menu-item {{ request()->route()->getPrefix() == '/dosen/pendidikan' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-trophy"></i>
                <div>Bidang Pendidikan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='bebanpendidikan' ? 'active' : '' }}">
                    <a href="{{ route('bebanpendidikan') }}" class="menu-link">
                        <div>Beban Kerja Dosen</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='pendidikan' ? 'active' : '' }}">
                    <a href="{{ route('pendidikan') }}" class="menu-link">
                        <div>Lembar Kerja Dosen</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->route()->getPrefix() == '/dosen/penelitian' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-vial"></i>
                <div>Bidang Penelitian</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='bebanpenelitian' ? 'active' : '' }}">
                    <a href="{{ route('bebanpenelitian') }}" class="menu-link">
                        <div>Beban Kerja Dosen</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='penelitian' ? 'active' : '' }}">
                    <a href="{{ route('penelitian') }}" class="menu-link">
                        <div>Lembar Kerja Dosen</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->route()->getPrefix() == '/dosen/abdimas' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-voice"></i>
                <div>Bidang Abdimas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='bebanabdimas' ? 'active' : '' }}">
                    <a href="{{ route('bebanabdimas') }}" class="menu-link">
                        <div>Beban Kerja Dosen</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='abdimas' ? 'active' : '' }}">
                    <a href="{{ route('abdimas') }}" class="menu-link">
                        <div>Lembar Kerja Dosen</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->route()->getPrefix() == '/dosen/lainnya' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-customize"></i>
                <div>Bidang Lainnya</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='bebanlainnya' ? 'active' : '' }}">
                    <a href="{{ route('bebanlainnya') }}" class="menu-link">
                        <div>Beban Kerja Dosen</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='lainnya' ? 'active' : '' }}">
                    <a href="{{ route('lainnya') }}" class="menu-link">
                        <div>Lembar Kerja Dosen</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- <li class="menu-item {{ Route::currentRouteName()=='pendidikan' ? 'active' : '' }}">
            <a href="{{ route('pendidikan') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-trophy'></i>
                <div data-i18n="Pendidikan">Bidang Pendidikan</div>
            </a>
        </li> -->

        <!-- <li class="menu-item {{ Route::currentRouteName()=='penelitian' ? 'active' : '' }}">
            <a href="{{ route('penelitian') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-vial'></i>
                <div data-i18n="Penelitian">Bidang Penelitian</div>
            </a>
        </li> -->

        <!-- <li class="menu-item {{ Route::currentRouteName()=='abdimas' ? 'active' : '' }}">
            <a href="{{ route('abdimas') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-user-voice'></i>
                <div data-i18n="Abdimas">Bidang Abdimas</div>
            </a>
        </li> -->

        <!-- <li class="menu-item {{ Route::currentRouteName()=='lainnya' ? 'active' : '' }}">
            <a href="{{ route('lainnya') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-customize'></i>
                <div data-i18n="Pendidikan">Bidang Lainnya</div>
            </a>
        </li> -->
        @endif

        @if(Auth::user()->hasRole(1) || Auth::user()->hasRole(3) || Auth::user()->hasRole(4) ||
        Auth::user()->hasRole(5))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Rekap Data</span>
        </li>
        @endif
        @if(Auth::user()->hasRole(3))
        <li class="menu-item {{ request()->route()->getPrefix() == '/prodi' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="Rekap BKD Prodi">Rekap Prodi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='prodi_pendidikan' ? 'active' : '' }}">
                    <a href="{{ route('prodi_pendidikan') }}" class="menu-link">
                        <div data-i18n="Pendidikan">Pendidikan</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='prodi_penelitian' ? 'active' : '' }}">
                    <a href="{{ route('prodi_penelitian') }}" class="menu-link">
                        <div data-i18n="Penelitian">Penelitian</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='prodi_abdimas' ? 'active' : '' }}">
                    <a href="{{ route('prodi_abdimas') }}" class="menu-link">
                        <div data-i18n="Abdimas">Abdimas</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='prodi_lainnya' ? 'active' : '' }}">
                    <a href="{{ route('prodi_lainnya') }}" class="menu-link">
                        <div data-i18n="Lain">Lainnya</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->hasRole(4))
        <li class="menu-item {{ request()->route()->getPrefix() == '/fakultas' ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="Rekap BKD Prodi">Rekap Fakultas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='fakultas_pendidikan' ? 'active' : '' }}">
                    <a href="{{ route('fakultas_pendidikan') }}" class="menu-link">
                        <div data-i18n="Pendidikan">Pendidikan</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='fakultas_penelitian' ? 'active' : '' }}">
                    <a href="{{ route('fakultas_penelitian') }}" class="menu-link">
                        <div data-i18n="Penelitian">Penelitian</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='fakultas_abdimas' ? 'active' : '' }}">
                    <a href="{{ route('fakultas_abdimas') }}" class="menu-link">
                        <div data-i18n="Abdimas">Abdimas</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='fakultas_lainnya' ? 'active' : '' }}">
                    <a href="{{ route('fakultas_lainnya') }}" class="menu-link">
                        <div data-i18n="Lainnya">Lainnya</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif


        @if(Auth::user()->hasRole(5) || Auth::user()->hasRole(1))
        <li class="menu-item {{ request()->route()->getPrefix() == '/universitas' ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="Rekap BKD Prodi">Rekap Universitas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='universitas_pendidikan' ? 'active' : '' }}">
                    <a href="{{ route('universitas_pendidikan') }}" class="menu-link">
                        <div data-i18n="Pendidikan">Pendidikan</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='universitas_penelitian' ? 'active' : '' }}">
                    <a href="{{ route('universitas_penelitian') }}" class="menu-link">
                        <div data-i18n="Penelitian">Penelitian</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='universitas_abdimas' ? 'active' : '' }}">
                    <a href="{{ route('universitas_abdimas') }}" class="menu-link">
                        <div data-i18n="Abdimas">Abdimas</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='universitas_lainnya' ? 'active' : '' }}">
                    <a href="{{ route('universitas_lainnya') }}" class="menu-link">
                        <div data-i18n="Lainnya">Lainnya</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Misc -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Lain-lain</span></li>
        @if(Auth::user()->hasRole(1))
        <li class="menu-item {{ request()->route()->getPrefix() == '/pengaturan' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Pengaturan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName()=='pengaturan.akun' ? 'active' : '' }}">
                    <a href="{{ route('pengaturan.akun') }}" class="menu-link">
                        <div>Akun</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='pengaturan.semester' ? 'active' : '' }}">
                    <a href="{{ route('pengaturan.semester') }}" class="menu-link">
                        <div>Semester</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='pengaturan.kegiatan' ? 'active' : '' }}">
                    <a href="{{ route('pengaturan.kegiatan') }}" class="menu-link">
                        <div>Kegiatan</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName()=='pengaturan.bukti' ? 'active' : '' }}">
                    <a href="{{ route('pengaturan.bukti') }}" class="menu-link">
                        <div>Bukti</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        <li class="menu-item">
            <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/html/vertical-menu-template/dashboards-analytics.html"
                target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Panduan">Panduan</div>
            </a>
        </li>
    </ul>



</aside>
<!-- / Menu -->
