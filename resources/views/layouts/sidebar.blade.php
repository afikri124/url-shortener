<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{asset('assets/img/logo-sjgu.png')}}" height="44">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Menu</span>
        </li>
        <li class="menu-item {{ Route::currentRouteName()=='home' ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">Halaman Utama</div>
            </a>
        </li>
        @if(Auth::user()->hasRole('ST') || Auth::user()->hasRole('SD'))
        <li
            class="menu-item {{ Route::currentRouteName()=='url.index' ? 'active' : (request()->route()->getPrefix() == '/URL' ? 'active' : '') }}">
            <a href="{{ route('url.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-link"></i>
                <div>Penyingkat URL</div>
            </a>
        </li>
        <li class="menu-item {{ Route::currentRouteName()=='qr.index' ? 'active' : '' }}">
            <a href="{{ route('qr.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-qr"></i>
                <div>Pembuat QR-Code</div>
            </a>
        </li>
        @endif
        @if(Auth::user()->hasRole('ST'))
        <li
            class="menu-item {{ request()->route()->getPrefix() == '/MT' ? 'open' : '' }} {{ request()->route()->getPrefix() == '/ATT' ? 'open' : '' }} {{ request()->route()->getPrefix() == '/attendance' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                <div>Absensi</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ Route::currentRouteName()=='att.index' ? 'active' : (request()->route()->getPrefix() == '/ATT' ? 'active' : '') }}">
                    <a href="{{ route('att.index') }}" class="menu-link">
                        <div>Acara/Kegiatan</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ Route::currentRouteName()=='mt.index' ? 'active' : (request()->route()->getPrefix() == '/MT' ? 'active' : '') }}">
                    <a href="{{ route('mt.index') }}" class="menu-link">
                        <div>Rapat</div>
                    </a>
                </li>
                @if(Auth::user()->hasRole('AD'))
                <li
                    class="menu-item {{ Route::currentRouteName()=='attendance.index' ? 'active' : (request()->route()->getPrefix() == '/attendance' ? 'active' : '') }}">
                    <a href="{{ route('attendance.index') }}" class="menu-link">
                        <div>Rekap Absensi</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        <li class="menu-item {{ request()->route()->getPrefix() == '/MoM' ? 'open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div>Notulensi</div>
            </a>
            <ul class="menu-sub">
                <li
                    class="menu-item {{ Route::currentRouteName()=='mom.meeting' ? 'active' : '' }}">
                    <a href="{{ route('mom.meeting') }}" class="menu-link">
                        <div>Risalah Rapat</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ Route::currentRouteName()=='mom.PIC' ? 'active' : '' }}">
                    <a href="{{ route('mom.PIC') }}" class="menu-link">
                        <div>PIC Uraian Rapat</div>
                    </a>
                </li>
                <li
                    class="menu-item {{ Route::currentRouteName()=='mom.note-taker' ? 'active' : '' }}">
                    <a href="{{ route('mom.note-taker') }}" class="menu-link">
                        <div>Notulen</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->hasRole('AD'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>
        <li
            class="menu-item {{ Route::currentRouteName()=='setting_account' ? 'active' : (request()->route()->getPrefix() == '/setting' ? 'active' : '') }}">
            <a href="{{ route('setting_account') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Account">Akun</div>
            </a>
        </li>
        @endif

    </ul>



</aside>
<!-- / Menu -->
