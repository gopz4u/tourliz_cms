<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tourliz CMS') - Admin Panel</title>

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#334155">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Tourliz">
    <link rel="apple-touch-icon" href="/img/apple-touch-icon.png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #5a52e5;
            /* Purple-indigo from image */
            --primary-hover: #4e46c7;
            --primary-light: #efeffd;
            --sidebar-width: 280px;
            --bg-body: #f4f6fa;
            /* Very light gray/blue background */
            --bg-surface: #ffffff;
            /* White card background */
            --text-main: #0f172a;
            --text-muted: #94a3b8;
            --text-muted-darker: #64748b;
            --app-radius: 2.5rem;
            /* Large tablet-like radius */
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            padding: 1.5rem;
            min-height: 100vh;
            color: var(--text-main);
        }

        /* The floating App container matching the image */
        .app-wrapper {
            display: flex;
            background: var(--bg-surface);
            border-radius: var(--app-radius);
            min-height: calc(100vh - 3rem);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            position: relative;
        }

        /* --- Sidebar Styling --- */
        .sidebar {
            width: var(--sidebar-width);
            background: transparent;
            padding: 2.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(0, 0, 0, 0.04);
            height: calc(100vh - 3rem);
            overflow-y: auto;
            overflow-x: hidden;
            flex-shrink: 0;
            z-index: 10;
        }

        /* Hide scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* Logo Area */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2.5rem;
            padding: 0 10px;
            text-decoration: none !important;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            transform: rotate(-5deg);
            box-shadow: 0 4px 10px rgba(90, 82, 229, 0.3);
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: var(--primary);
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            font-weight: 700;
            margin: 1.5rem 0 0.5rem 1.2rem;
        }

        /* Menu Links */
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
        }

        .sidebar-menu li {
            margin: 4px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: var(--text-muted-darker);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 16px;
            transition: all var(--transition-speed) ease;
            position: relative;
        }

        .sidebar-menu a i.main-icon {
            font-size: 1.1rem;
            margin-right: 16px;
            color: var(--text-muted);
            transition: all var(--transition-speed) ease;
        }

        .sidebar-menu a:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }

        .sidebar-menu a:hover i.main-icon {
            color: var(--primary);
        }

        /* Active State (Purple Pill) */
        .sidebar-menu a.active {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 8px 20px rgba(90, 82, 229, 0.25);
        }

        .sidebar-menu a.active i.main-icon {
            color: white;
        }

        /* Arrow / Chevron for Active State */
        .nav-arrow {
            margin-left: auto;
            font-size: 0.8rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all var(--transition-speed) ease;
        }

        .sidebar-menu a.active .nav-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Badges */
        .nav-badge {
            margin-left: auto;
            background: #ff5e3a;
            /* Orange badge from picture */
            color: white;
            font-size: 0.7rem;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 800;
            box-shadow: 0 4px 8px rgba(255, 94, 58, 0.3);
        }

        .sidebar-menu a.active .nav-badge {
            background: white;
            color: var(--primary);
        }

        /* Dropdown Styling */
        .sidebar-menu .collapse-inner {
            background: transparent !important;
            padding: 5px 0 5px 15px;
            margin-left: 8px;
            border-left: 2px solid var(--primary-light);
        }

        .sidebar-menu .collapse-inner a.dropdown-item {
            padding: 10px 16px;
            font-size: 0.9rem;
            color: var(--text-muted-darker);
            border-radius: 12px;
            margin: 2px 0;
        }

        .sidebar-menu .collapse-inner a.dropdown-item:hover,
        .sidebar-menu .collapse-inner a.dropdown-item.fw-bold {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
        }

        /* Logout Button Area */
        .logout-wrapper {
            margin-top: auto;
            padding-top: 2rem;
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            padding: 12px 18px;
            border: none;
            background: transparent;
            color: var(--text-muted-darker);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 16px;
            transition: all var(--transition-speed) ease;
        }

        .btn-logout:hover {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .btn-logout i {
            font-size: 1.1rem;
            margin-right: 16px;
        }

        /* --- Main Content Area --- */
        .main-content {
            flex: 1;
            padding: 2.5rem 3rem;
            overflow-y: auto;
            position: relative;
            background: transparent;
        }

        .main-content::-webkit-scrollbar {
            width: 6px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* Restyle existing layout elements to blend into the new clean design */
        .page-header {
            background: transparent !important;
            padding: 0 0 2rem 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            border-bottom: 0 !important;
        }

        .page-header h1,
        .page-header h2 {
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        .card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            padding: 1.25rem 1.5rem;
            font-weight: 700;
        }

        /* Buttons matching the "Upgrade Now" pill style */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            box-shadow: 0 6px 15px rgba(90, 82, 229, 0.25);
            transition: all var(--transition-speed);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(90, 82, 229, 0.35);
        }

        .table-responsive {
            background: transparent;
            border-radius: 0;
            overflow: visible;
        }

        .table {
            color: var(--text-muted-darker);
        }

        .table thead th {
            background: transparent;
            border-bottom: 2px solid rgba(0, 0, 0, 0.04);
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.02);
            padding: 1rem;
        }

        /* Search Top Bar mockup */
        .top-search-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 2rem;
            gap: 20px;
        }

        .search-wrapper {
            position: relative;
            width: 250px;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-wrapper input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border-radius: 20px;
            border: 1px solid transparent;
            background: var(--bg-body);
            font-family: inherit;
            font-size: 0.9rem;
            transition: all var(--transition-speed);
        }

        .search-wrapper input:focus {
            outline: none;
            background: white;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1e293b, var(--primary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Mobile Responsive Styles */
        .mobile-header {
            display: none;
            background: var(--bg-surface);
            padding: 15px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 990;
        }

        @media (max-width: 991.98px) {
            body {
                padding: 0;
                /* Remove body padding so it's edge-to-edge */
            }

            .app-wrapper {
                flex-direction: column;
                min-height: 100vh;
                border-radius: 0;
                /* No outside border radius on mobile */
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                background: var(--bg-surface);
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
                border-radius: 0 24px 24px 0;
                z-index: 1000;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                padding: 0.75rem;
                /* Very tight padding for mobile */
            }

            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-radius: 0;
                /* Edge to edge header */
                margin-bottom: 0;
                padding: 10px 15px;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .top-search-bar {
                display: none;
                /* Hide top bar on mobile to save space */
            }

            .card {
                border-radius: 12px;
                /* Smaller radii for phone screen */
                margin-bottom: 1rem;
            }

            .card-header,
            .card-body {
                padding: 1rem;
                /* Compact card padding */
            }

            .page-header {
                padding: 0.5rem 0 1rem 0 !important;
            }

            .table th,
            .table td {
                padding: 0.5rem;
                /* Compact table */
                font-size: 0.85rem;
            }

            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    @stack('styles')
</head>

<body>
    <!-- Mobile Header -->
    <div class="mobile-header d-lg-none">
        <div class="d-flex align-items-center">
            <button class="btn btn-link p-0 me-3" id="sidebarToggle" style="color: var(--text-main);">
                <i class="bi bi-list fs-1"></i>
            </button>
            @php
                $brand = config('tourliz.brand');
                $logoPath = $brand['logo_path'] ?? 'img/tourliz_logo.png';
                $hasLogo = file_exists(public_path($logoPath));
            @endphp
            @if($hasLogo)
                <img src="{{ asset($logoPath) }}" alt="Tourliz" style="height:32px;">
            @else
                <div class="logo-icon me-2" style="width:28px; height:28px; font-size:1rem;">
                    <i class="bi bi-airplane-fill"></i>
                </div>
                <span class="fs-5 fw-bold"
                    style="color: var(--text-main); letter-spacing:-0.5px;">{{ $brand['name'] ?? 'Tourliz' }}</span>
            @endif
        </div>
        <div class="user-avatar" style="width:36px; height:36px; font-size:0.8rem;">
            {{ substr(auth()->user()->name ?? 'Admin', 0, 1) }}
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            @php
                $brand = config('tourliz.brand');
                $logoPath = $brand['logo_path'] ?? 'img/tourliz_logo.png';
                $hasLogo = file_exists(public_path($logoPath));
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                @if($hasLogo)
                    <img src="{{ asset($logoPath) }}" alt="Tourliz" style="height:42px;">
                @else
                    <div class="logo-icon">
                        <i class="bi bi-airplane-fill"></i>
                    </div>
                    <div class="logo-text">
                        {{ $brand['name'] ?? 'Tourliz' }}<span>.</span>
                    </div>
                @endif
            </a>

            <div class="sidebar-section-title">Main Menu</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill main-icon"></i>
                        <span>Dashboard</span>
                        <i class="bi bi-arrow-right nav-arrow"></i>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.calendar.index') }}"
                        class="{{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3 main-icon"></i>
                        <span>Calendar View</span>
                        <i class="bi bi-arrow-right nav-arrow"></i>
                    </a>
                </li>


                @if(auth()->user()->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.countries.index') }}"
                            class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                            <i class="bi bi-globe-americas main-icon"></i>
                            <span>Countries</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.destinations.index') }}"
                            class="{{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt-fill main-icon"></i>
                            <span>Destinations</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.packages.index') }}"
                            class="{{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                            <i class="bi bi-briefcase-fill main-icon"></i>
                            <span>Packages</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.group-packages.index') }}"
                            class="{{ request()->routeIs('admin.group-packages.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill main-icon"></i>
                            <span>Group Packages</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.services.index') }}"
                            class="{{ request()->routeIs('admin.services.*') && !request()->has('category') ? 'active' : '' }}">
                            <i class="bi bi-tools main-icon"></i>
                            <span>Services</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.attractions.index') }}"
                            class="{{ request()->routeIs('admin.attractions.*') ? 'active' : '' }}">
                            <i class="bi bi-camera-fill main-icon"></i>
                            <span>Attractions</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tourist-spots.index') }}"
                            class="{{ request()->routeIs('admin.tourist-spots.*') ? 'active' : '' }}">
                            <i class="bi bi-pin-map-fill main-icon"></i>
                            <span>Tourist Spots</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.website.index') }}"
                            class="{{ request()->routeIs('admin.website.*') ? 'active' : '' }}">
                            <i class="bi bi-layout-text-window-reverse main-icon"></i>
                            <span>Website Management</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.package-offers.index') }}"
                            class="{{ request()->routeIs('admin.package-offers.*') ? 'active' : '' }}">
                            <i class="bi bi-gift main-icon"></i>
                            <span>Package Offers</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                @endif

                <div class="sidebar-section-title mt-4">Operations</div>
                <!-- Itineraries managed directly within Package page -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }} d-flex justify-content-between align-items-center"
                        href="{{ route('admin.reviews.index') }}">
                        <span><i class="bi bi-star me-2"></i>Package Reviews</span>
                        @php
                            $pendingReviews = \App\Models\Review::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingReviews > 0)
                            <span class="badge bg-danger rounded-pill">{{ $pendingReviews }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.group-package-itineraries.index') }}"
                        class="{{ request()->routeIs('admin.group-package-itineraries.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-heart-fill main-icon"></i>
                        <span>Group Itineraries</span>
                        <i class="bi bi-arrow-right nav-arrow"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.fixed-itineraries.index') }}"
                        class="{{ request()->routeIs('admin.fixed-itineraries.*') ? 'active' : '' }}">
                        <i class="bi bi-pin-map-fill main-icon"></i>
                        <span>Fixed Itineraries</span>
                        <i class="bi bi-arrow-right nav-arrow"></i>
                    </a>
                </li>
                @if(auth()->user()->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.currency-rates.index') }}"
                            class="{{ request()->routeIs('admin.currency-rates.*') ? 'active' : '' }}">
                            <i class="bi bi-currency-exchange main-icon"></i>
                            <span>Currency Rates</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.currency-converter') }}"
                            class="{{ request()->routeIs('admin.currency-converter') ? 'active' : '' }}">
                            <i class="bi bi-calculator-fill main-icon"></i>
                            <span>Currency Converter</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}"
                            class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                            <i class="bi bi-tags-fill main-icon"></i>
                            <span>Coupons</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.bookings.index') }}"
                        class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <i class="bi bi-ticket-detailed-fill main-icon"></i>
                        <span>Bookings</span>
                        @php
                            $pendingBookings = \App\Models\Booking::where('status', 'pending')->count();
                        @endphp
                        @if($pendingBookings > 0)
                            <span class="nav-badge">{{ $pendingBookings }}</span>
                        @else
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        @endif
                    </a>
                </li>
                @if(auth()->user()->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-person-fill-lock main-icon"></i>
                            <span>System Users</span>
                            <i class="bi bi-arrow-right nav-arrow"></i>
                        </a>
                    </li>
                @endif

                <div class="sidebar-section-title mt-4">Sales</div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseB2B"
                        aria-expanded="false" aria-controls="collapseB2B">
                        <i class="bi bi-building-fill main-icon"></i>
                        <span>B2B Partners</span>
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>
                    <div id="collapseB2B"
                        class="collapse {{ request()->routeIs('admin.agencies.*') || request()->routeIs('admin.b2b-itineraries.*') || request()->routeIs('admin.b2b-itineraries.kanban') ? 'show' : '' }}">
                        <div class="collapse-inner">
                            <a class="dropdown-item {{ request()->routeIs('admin.agencies.*') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.agencies.index') }}">
                                Manage Agencies
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('admin.b2b-itineraries.index') && !request()->routeIs('admin.b2b-itineraries.kanban') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.b2b-itineraries.index') }}">
                                Custom Proposals
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('admin.b2b-itineraries.kanban') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.b2b-itineraries.kanban') }}">
                                <i class="bi bi-kanban me-1 text-primary" style="font-size:.75rem;"></i>Pipeline Kanban
                            </a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseB2C"
                        aria-expanded="false" aria-controls="collapseB2C">
                        <i class="bi bi-person-badge-fill main-icon"></i>
                        <span>B2C Sales</span>
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>
                    <div id="collapseB2C"
                        class="collapse {{ request()->routeIs('admin.b2c-itineraries.*') || request()->routeIs('admin.b2c-itineraries.kanban') ? 'show' : '' }}">
                        <div class="collapse-inner">
                            <a class="dropdown-item {{ request()->routeIs('admin.b2c-itineraries.create') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.b2c-itineraries.create') }}">
                                New Walk-in Lead
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('admin.b2c-itineraries.index') && !request()->routeIs('admin.b2c-itineraries.create') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.b2c-itineraries.index') }}">
                                Manage Leads
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('admin.b2c-itineraries.kanban') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.b2c-itineraries.kanban') }}">
                                <i class="bi bi-kanban me-1 text-primary" style="font-size:.75rem;"></i>Pipeline Kanban
                            </a>
                        </div>
                    </div>

                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseGroups"
                        aria-expanded="false" aria-controls="collapseGroups">
                        <i class="bi bi-people-fill main-icon"></i>
                        <span>Group Proposals</span>
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>
                    <div id="collapseGroups"
                        class="collapse {{ request()->routeIs('admin.group-itineraries.*') ? 'show' : '' }}">
                        <div class="collapse-inner">
                            <a class="dropdown-item {{ request()->routeIs('admin.group-itineraries.create') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.group-itineraries.create') }}">
                                New Group Lead
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('admin.group-itineraries.index') && !request()->routeIs('admin.group-itineraries.create') ? 'fw-bold' : '' }}"
                                href="{{ route('admin.group-itineraries.index') }}">
                                Manage Groups
                            </a>
                        </div>
                    </div>
                </li>

                <li>
                    <a href="{{ route('admin.site-users.index') }}"
                        class="{{ request()->routeIs('admin.site-users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill main-icon"></i>
                        <span>End Users</span>
                        <i class="bi bi-arrow-right nav-arrow"></i>
                    </a>
                </li>

                @if(auth()->user()->isSuperAdmin())
                    <div class="sidebar-section-title mt-4">Inventory Master</div>
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInventory"
                            aria-expanded="false" aria-controls="collapseInventory">
                            <i class="bi bi-box-seam-fill main-icon"></i>
                            <span>Core Services</span>
                            <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; opacity: 0.5;"></i>
                        </a>
                        <div id="collapseInventory"
                            class="collapse {{ (request()->routeIs('admin.services.*') && request()->has('category')) || request()->routeIs('admin.suppliers.*') ? 'show' : '' }}">
                            <div class="collapse-inner">
                                <a class="dropdown-item {{ request()->routeIs('admin.services.*') && request('category') === 'Hotels' ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.services.index') }}?category=Hotels">Hotels</a>
                                <a class="dropdown-item {{ request()->routeIs('admin.services.*') && request('category') === 'Activities' ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.services.index') }}?category=Activities">Activities</a>
                                <a class="dropdown-item {{ request()->routeIs('admin.services.*') && request('category') === 'Transport' ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.services.index') }}?category=Transport">Transport</a>
                                <a class="dropdown-item {{ request()->routeIs('admin.services.*') && request('category') === 'Entry Tickets' ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.services.index') }}?category=Entry Tickets">Entry Tickets</a>
                                <a class="dropdown-item {{ request()->routeIs('admin.services.*') && request('category') === 'Meals' ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.services.index') }}?category=Meals">Meals Master</a>
                                <a class="dropdown-item {{ request()->routeIs('admin.services.*') && request('category') === 'Other Services' ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.services.index') }}?category=Other Services">Tourist Spots
                                    (Legacy)</a>
                                <a class="dropdown-item {{ request()->routeIs('admin.tourist-spots.*') ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.tourist-spots.index') }}">
                                    <i class="bi bi-geo-alt-fill me-1"></i> Tourist Spots
                                </a>
                                <a class="dropdown-item {{ request()->routeIs('admin.suppliers.*') && !request('type') ? 'fw-bold' : '' }}"
                                    href="{{ route('admin.suppliers.index') }}">
                                    <i class="bi bi-shop me-1"></i> All Suppliers
                                </a>
                                <div class="ps-2 mt-1">
                                    <div
                                        style="font-size:0.68rem; text-transform:uppercase; letter-spacing:1px; font-weight:700; color:#94a3b8; padding:2px 12px;">
                                        By Category</div>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Hotel' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Hotel">
                                        <i class="bi bi-building me-1 text-primary" style="font-size:0.8rem;"></i> Hotel
                                    </a>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Transport' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Transport">
                                        <i class="bi bi-truck me-1 text-warning" style="font-size:0.8rem;"></i> Transport
                                    </a>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Activity' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Activity">
                                        <i class="bi bi-lightning me-1 text-success" style="font-size:0.8rem;"></i> Activity
                                    </a>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Ticket' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Ticket">
                                        <i class="bi bi-ticket-perforated me-1 text-danger" style="font-size:0.8rem;"></i>
                                        Entry Tickets
                                    </a>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Meal' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Meal">
                                        <i class="bi bi-egg-fried me-1 text-danger" style="font-size:0.8rem;"></i> Meals
                                    </a>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Agent' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Agent">
                                        <i class="bi bi-person-badge me-1 text-info" style="font-size:0.8rem;"></i> Agent /
                                        Partner
                                    </a>
                                    <a class="dropdown-item py-1 {{ request('type') == 'Other' ? 'fw-bold' : '' }}"
                                        href="{{ route('admin.suppliers.index') }}?type=Other">
                                        <i class="bi bi-three-dots me-1 text-secondary" style="font-size:0.8rem;"></i> Other
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif
            </ul>

            <div class="logout-wrapper">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Top App Bar -->
            <div class="top-search-bar d-none d-lg-flex">
                <form action="{{ route('admin.search') }}" method="GET" class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search client, title, ID...">
                </form>
                <div class="user-avatar" title="{{ auth()->user()->name ?? 'Admin' }}">
                    {{ substr(auth()->user()->name ?? 'Admin', 0, 1) }}
                </div>
            </div>

            <!-- Page Specific Content -->
            @yield('content')

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Mobile sidebar toggle
            $('#sidebarToggle, #sidebarOverlay').on('click', function () {
                $('.sidebar').toggleClass('show');
                $('#sidebarOverlay').toggleClass('show');
                $('body').toggleClass('overflow-hidden');
            });

            // --- Global Realtime AJAX Delete ---
            // Automatically intercepts any standard Laravel delete form and processes it via AJAX!
            $('form:has(input[name="_method"][value="DELETE"])').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                // The form already has an onsubmit confirm, but if not we can add one
                if (form.attr('onsubmit') === undefined && !confirm('Are you sure you want to delete this item?')) {
                    return;
                }

                let row = form.closest('tr');
                let card = form.closest('.card'); // Fallback if it's not a table
                let elementToRemove = row.length ? row : card;
                let submitBtn = form.find('button[type="submit"]');
                let originalHtml = submitBtn.html();

                submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        elementToRemove.fadeOut(400, function () {
                            $(this).remove();
                            // Optional: Show a little toast or notification
                        });
                    },
                    error: function (xhr) {
                        alert('Error deleting item: ' + (xhr.responseJSON?.message || 'Server error.'));
                        submitBtn.html(originalHtml).prop('disabled', false);
                    }
                });
            });
        });
    </script>
    <!-- Toast Notification Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Action completed successfully.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <!-- Select2 JS (loaded globally after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .ql-container {
            min-height: 200px;
            font-size: 14px;
            border-radius: 0 0 12px 12px;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }

        .ql-toolbar {
            border-radius: 12px 12px 0 0;
            border-color: rgba(0, 0, 0, 0.1) !important;
            background: #f8fafc;
        }

        .ql-editor {
            min-height: 200px;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* --- Floating Action Button (FAB) --- */
        .fab-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050;
            display: flex;
            flex-direction: column-reverse;
            align-items: flex-end;
            gap: 15px;
            pointer-events: none;
        }

        .fab-main {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(90, 82, 229, 0.4);
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            pointer-events: auto;
        }

        .fab-main:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 30px rgba(90, 82, 229, 0.5);
        }

        .fab-main.active {
            transform: scale(1.05) rotate(135deg);
            background: #ef4444;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }

        .fab-menu {
            display: flex;
            flex-direction: column-reverse;
            align-items: flex-end;
            gap: 12px;
            visibility: hidden;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fab-menu.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .fab-item {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none !important;
            pointer-events: auto;
        }

        .fab-label {
            background: white;
            color: var(--text-main);
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            white-space: nowrap;
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .fab-menu.show .fab-label {
            opacity: 1;
            transform: translateX(0);
        }

        .fab-item:hover .fab-label {
            background: var(--primary-light);
            color: var(--primary);
        }

        .fab-icon-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            border: none;
        }

        .fab-icon-btn:hover {
            transform: scale(1.1);
        }

        .fab-icon-btn.b2b {
            background: #06b6d4;
        }

        .fab-icon-btn.b2c {
            background: #10b981;
        }

        .fab-icon-btn.group {
            background: #f59e0b;
        }

        @media (max-width: 991.98px) {
            .fab-container {
                bottom: 20px;
                right: 20px;
            }

            .fab-label {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>


    <script>
        // CSRF Token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Ensure token is included in every request even if meta tag changes
        $(document).ajaxSend(function (e, xhr, options) {
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        });

        // Initialize Quill Editor
        function initQuillEditor(selector, height = 300) {
            if (typeof Quill !== 'undefined') {
                var quill = new Quill(selector, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Start typing...'
                });

                // Handle image upload in Quill
                quill.getModule('toolbar').addHandler('image', function () {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();

                    input.onchange = function () {
                        var file = input.files[0];
                        if (file) {
                            var formData = new FormData();
                            formData.append('image', file);

                            $.ajax({
                                url: '{{ route("admin.upload.image") }}',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    if (response.success && response.url) {
                                        var range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', response.url);
                                    }
                                },
                                error: function () {
                                    alert('Error uploading image');
                                }
                            });
                        }
                    };
                });

                return quill;
            }
            return null;
        }
    </script>

    <!-- Quick Create FAB -->
    <div class="fab-container" id="quickCreateFab">
        <div class="fab-menu" id="fabMenu">
            <!-- New Group Lead -->
            <a href="{{ route('admin.group-itineraries.create') }}" class="fab-item">
                <span class="fab-label">New Group Lead</span>
                <div class="fab-icon-btn group" title="New Group Lead">
                    <i class="bi bi-people-fill"></i>
                </div>
            </a>
            <!-- New Walk-in Lead -->
            <a href="{{ route('admin.b2c-itineraries.create') }}" class="fab-item">
                <span class="fab-label">New Walk-in Lead</span>
                <div class="fab-icon-btn b2c" title="New Walk-in Lead">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </a>
            <!-- New B2B Proposal -->
            <a href="{{ route('admin.b2b-itineraries.create') }}" class="fab-item">
                <span class="fab-label">New B2B Proposal</span>
                <div class="fab-icon-btn b2b" title="New B2B Proposal">
                    <i class="bi bi-building-fill"></i>
                </div>
            </a>
        </div>
        <button class="fab-main" id="fabMainBtn" title="Quick Create">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <script>
        $(document).ready(function () {
            // --- FAB Menu Toggle ---
            $('#fabMainBtn').on('click', function (e) {
                e.stopPropagation();
                $(this).toggleClass('active');
                $('#fabMenu').toggleClass('show');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#quickCreateFab').length) {
                    $('#fabMainBtn').removeClass('active');
                    $('#fabMenu').removeClass('show');
                }
            });
        });
    </script>

    <!-- PWA Scripts & iOS Install Banner -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => console.log('Service Worker registered successfully:', reg.scope))
                    .catch(err => console.error('Service Worker registration failed:', err));
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isStandalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
            const isDismissed = localStorage.getItem('ios-pwa-prompt-dismissed');

            // Only show prompt if iOS Safari and not standalone, and not dismissed in the last 7 days
            if (isIOS && !isStandalone && (!isDismissed || Date.now() > parseInt(isDismissed))) {
                const iosBanner = document.createElement('div');
                iosBanner.id = 'ios-pwa-banner';
                iosBanner.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    left: 20px;
                    right: 20px;
                    background: rgba(15, 23, 42, 0.95);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    color: #f8fafc;
                    padding: 16px 20px;
                    border-radius: 16px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
                    z-index: 99999;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                `;

                iosBanner.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="/img/pwa-icon-192.png" alt="App Icon" style="width: 48px; height: 48px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.2);">
                            <div>
                                <h6 style="margin: 0; font-weight: 600; font-size: 0.95rem; color: #ffffff; text-align: left;">Install Tourliz App</h6>
                                <p style="margin: 0; font-size: 0.75rem; color: #94a3b8; text-align: left;">Add to your Home Screen for a native app experience.</p>
                            </div>
                        </div>
                        <button id="close-ios-banner" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; padding: 0 4px; line-height: 0.8; font-weight: 300;">&times;</button>
                    </div>
                    <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 8px; margin-top: 4px; font-size: 0.8rem; color: #cbd5e1; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; text-align: left;">
                        <span>Tap the share button</span>
                        <i class="bi bi-box-arrow-up" style="color: #3b82f6; font-size: 1rem;"></i>
                        <span>then scroll down and select</span>
                        <strong style="color: #ffffff; font-weight: 600;">Add to Home Screen</strong>
                    </div>
                `;

                document.body.appendChild(iosBanner);

                document.getElementById('close-ios-banner').addEventListener('click', () => {
                    iosBanner.style.display = 'none';
                    // Suppress prompt for 7 days
                    localStorage.setItem('ios-pwa-prompt-dismissed', Date.now() + 7 * 24 * 60 * 60 * 1000);
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>