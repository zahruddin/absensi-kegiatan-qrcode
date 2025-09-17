<!doctype html>
<html lang="id" data-bs-theme="auto">
  <!--begin::Head-->
    <head>
        <script>
            (function() {
              let storedTheme = localStorage.getItem("theme") || "auto";
              
              if (storedTheme === "auto") {
                storedTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
              }
          
              document.documentElement.setAttribute("data-bs-theme", storedTheme);
            })();
        </script>   
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>@yield('title', 'ITBADLA ABSENSI')</title>
        <!--begin::Primary Meta Tags-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="title" content="ITBADLA" />
        <meta name="author" content="Zahruddin Fanani" />
        <meta
        name="description"
        content="Absensi Kegiatan ITBADLA"
        />
        <meta
        name="keywords"
        content="kegiatan, itbadla, absensi, absensi itbadla, itbadla absensi"
        />
        <!--end::Primary Meta Tags-->
        <!--begin::Fonts-->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous"
        />
        <!--end::Fonts-->
        <!--begin::Third Party Plugin(OverlayScrollbars)-->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
        crossorigin="anonymous"
        />
        <!--end::Third Party Plugin(OverlayScrollbars)-->
        <!--begin::Third Party Plugin(Bootstrap Icons)-->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
        crossorigin="anonymous"
        />
        <!--end::Third Party Plugin(Bootstrap Icons)-->
        <!--begin::Required Plugin(AdminLTE)-->
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}" />
        <!--end::Required Plugin(AdminLTE)-->
        <!-- apexcharts -->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
        crossorigin="anonymous"
        />
        <!-- jsvectormap -->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4="
        crossorigin="anonymous"
        />
        <!-- CSS Bootstrap -->
        {{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"> --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        @yield('style')
    </head>
    <!--end::Head-->
    <!--begin::Body-->
<audio id="notif-sound" src="{{ asset('audio/notif.wav') }}" preload="auto"></audio>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <!--begin::App Wrapper-->
        <div class="app-wrapper">
            <!--begin::Header-->
            <nav class="app-header navbar navbar-expand bg-body">
                <!--begin::Container-->
                <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->
                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <!--end::Notifications Dropdown Menu-->
                    <!--begin::Fullscreen Toggle-->
                    <li class="nav-item">
                    <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                        <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                        <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                    </a>
                    </li>
                    <!--end::Fullscreen Toggle-->
                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown">
                        <button class="btn btn-link nav-link dropdown-toggle d-flex align-items-center"
                            id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static">
                            <span class="theme-icon-active">
                                <i class="bi bi-circle-half"></i>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                                    <i class="bi bi-sun-fill me-2"></i> Light
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                                    <i class="bi bi-moon-fill me-2"></i> Dark
                                </button>
                            </li>
                            {{-- <li>
                                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto">
                                    <i class="bi bi-circle-half me-2"></i> Auto
                                </button>
                            </li> --}}
                        </ul>
                    </li>  
                    <li class="nav-item dropdown user-menu">
                        @auth
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img
                                src="{{ asset('dist/assets/img/user2-160x160.jpg') }}"
                                class="user-image rounded-circle shadow"
                                alt="User Image"
                            />
                            
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        @else
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
                               
                            @endauth
                        @auth
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-primary">
                            <img
                                src="{{ asset('dist/assets/img/user2-160x160.jpg')}}"
                                class="rounded-circle shadow"
                                alt="User Image"
                            />
                            <p>
                                {{ Auth::user()->name }}
                                <small>Member since {{ Auth::user()->created_at->format('M. Y') }}</small>
                            </p>
                            </li>
                            <!--end::User Image-->
                            <!--begin::Menu Body-->
                            {{-- <li class="user-body">
                            <!--begin::Row-->
                            <div class="row">
                                <div class="col-4 text-center"><a href="#">Followers</a></div>
                                <div class="col-4 text-center"><a href="#">Sales</a></div>
                                <div class="col-4 text-center"><a href="#">Friends</a></div>
                            </div>
                            <!--end::Row-->
                            </li> --}}
                            <!--end::Menu Body-->
                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                {{-- @if(Auth::user()->role == 'admin')
                                    <a href="{{ route('admin.profile') }}" class="btn btn-default btn-flat">Profile</a>
                                @endif
                                <!-- Menu untuk operator -->
                                @if(Auth::user()->role == 'operator')
                                    <a href="{{ route('operator.profile') }}" class="btn btn-default btn-flat">Profile</a>
                                @endif
                                @if(Auth::user()->role == 'customer')
                                    <a href="{{ route('customer.profile') }}" class="btn btn-default btn-flat">Profile</a>
                                @endif --}}
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-default btn-flat float-end">Sign Out</button>
                                </form>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                        @endauth
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
                </div>
                <!--end::Container-->
            </nav>
            <!--end::Header-->
            <!--begin::Sidebar-->
            <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
                <!--begin::Sidebar Brand-->
                <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="{{ route('login') }}" class="brand-link">
                    <!--begin::Brand Image-->
                    {{-- <img
                    src="{{ asset('dist/assets/img/AdminLTELogo.png') }}"
                    alt="AdminLTE Logo"
                    class="brand-image opacity-75 shadow"
                    /> --}}
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">ADMIN KEGIATAN</span>
                    <!--end::Brand Text-->
                    {{-- <b>ADMIN KEGIATAN</b> --}}
                </a>
                <!--end::Brand Link-->
                </div>
                <!--end::Sidebar Brand-->
                <!--begin::Sidebar Wrapper-->
                <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    @auth
                    <ul
                    class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="menu"
                    data-accordion="false"
                    >
                        <!-- Menu untuk Admin -->
                        @if(Auth::user()->role == 'admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-speedometer2"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            
                            {{-- Menu Kelola Operator --}}
                            <li class="nav-item">
                                {{-- routeIs('admin.operator.*') akan aktif di semua halaman operator (index, create, edit) --}}
                                <a href="{{ route('admin.operator.index') }}" class="nav-link {{ request()->routeIs('admin.operator.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-person-badge"></i>
                                    <p>Kelola Operator</p>
                                </a>
                            </li>

                            {{-- Menu Kelola Peserta --}}
                            <li class="nav-item">
                                <a href="{{ route('admin.peserta.index') }}" class="nav-link {{ request()->routeIs('admin.peserta.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Kelola Peserta</p>
                                </a>
                            </li>

                            {{-- Menu Kelola Kegiatan --}}
                            <li class="nav-item">
                                <a href="{{ route('admin.kegiatan.index') }}" class="nav-link {{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-calendar-event"></i>
                                    <p>Kelola Kegiatan</p>
                                </a>
                            </li>
                        @endif


                        <!-- Menu untuk operator -->
                        @if(Auth::user()->role == 'operator')
                            <li class="nav-item">
                                <a href="{{ route('operator.dashboard') }}" class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-speedometer"></i>
                                    <p>
                                        Dashboard
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('operator.kegiatan.index') }}" class="nav-link {{ request()->routeIs('operator.kegiatan.index') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-speedometer"></i>
                                    <p>
                                        Kegiatan
                                    </p>
                                </a>
                            </li>
                        @endif
                        {{-- Menu untuk peserta --}}
                        @if(Auth::user()->role == 'peserta')
                            <li class="nav-item {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                                <a href="{{ route('peserta.dashboard') }}" class="nav-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-card-list"></i>
                                <p>
                                    Menu
                                </p>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100">
                                    <i class="nav-icon bi bi-box-arrow-left"></i>
                                    <p>Sign Out</p>
                                </button>
                            </form>
                        </li>
                    </ul>
                    @endauth
                    <!--end::Sidebar Menu-->
                </nav>
                </div>
                <!--end::Sidebar Wrapper-->
            </aside>
            <!--end::Sidebar-->
            <!--begin::App Main-->
            <main class="app-main">
                <!--begin::App Content Header-->
                <div class="app-content-header">
                    <!--begin::Container-->
                    <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">@yield('page', 'Dashboard')</h3></div>
                        <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end bg-transparent ">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('page', 'Dashboard')@stack('outlet')</li>
                        </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                    </div>
                    <!--end::Container-->
                </div>
                @yield('content')
            </main>
            <!--end::App Main-->
            <!--begin::Footer-->
            <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                Copyright &copy; 2025&nbsp;
                <a href="#" class="text-decoration-none">ITBADLA</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
            </footer>
            <!--end::Footer-->
        </div>
        <!--end::App Wrapper-->

        <!--begin::Script-->
        <!--begin::Third Party Plugin(OverlayScrollbars)-->
        <script src="{{ asset('dist/js/adminlte.js') }}"></script>
        <script
            src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
            integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
            crossorigin="anonymous"
        ></script>
        <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>
        <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
        {{-- <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"
        ></script> --}}
        <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
        <!-- JS Bootstrap -->
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!--end::Script-->
        @yield('scripts')
        @stack('scripts')
        <script>
            (() => {
                "use strict";
            
                const storedTheme = localStorage.getItem("theme");
            
                const getPreferredTheme = () => {
                    if (storedTheme) {
                        return storedTheme;
                    }
                    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                };
            
                const setTheme = (theme) => {
                    if (theme === "auto") {
                        theme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                    }
                    document.documentElement.setAttribute("data-bs-theme", theme);
                    localStorage.setItem("theme", theme);
                    updateActiveIcon(theme);
                };
            
                const updateActiveIcon = (theme) => {
                    const activeIcon = document.querySelector(".theme-icon-active i");
                    if (!activeIcon) return;
            
                    const iconMap = {
                        "light": "bi bi-sun-fill",
                        "dark": "bi bi-moon-fill",
                        "auto": "bi bi-circle-half"
                    };
            
                    activeIcon.setAttribute("class", iconMap[theme] || "bi bi-circle-half");
                };
            
                // Terapkan tema saat halaman dimuat
                setTheme(getPreferredTheme());
            
                // Event listener untuk setiap tombol tema
                document.querySelectorAll("[data-bs-theme-value]").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const theme = btn.getAttribute("data-bs-theme-value");
                        setTheme(theme);
                    });
                });
            })();        
        </script>            
    </body>
<!--end::Body-->
</html>

