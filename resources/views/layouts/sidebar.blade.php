<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{asset('assets/img/logo.png')}}" height="50">
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
            <span class="menu-header-text">Dashboard</span>
        </li>
        <li class="menu-item {{ Route::currentRouteName()=='home' ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">Home</div>
            </a>
        </li>
        <li class="menu-item {{ Route::currentRouteName()=='url.index' ? 'active' : '' }}">
            <a href="{{ route('url.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-link"></i>
                <div>URL Shortener</div>
            </a>
        </li>
        <li class="menu-item ">
            <a href="" class="menu-link">
                <i class="menu-icon tf-icons bx bx-qr"></i>
                <div>QR Generator</div>
            </a>
        </li>
        <li class="menu-item ">
            <a href="" class="menu-link">
                <i class="menu-icon tf-icons bx bx-task"></i>
                <div>Event Attendance</div>
            </a>
        </li>

     </ul>



</aside>
<!-- / Menu -->
