<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourliz | Experience Luxury Travel</title>

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#334155">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Tourliz">
    <link rel="apple-touch-icon" href="/img/apple-touch-icon.png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #5a52e5;
            --primary-dark: #4e46c7;
            --secondary: #ff5e3a;
            --accent: #00d2ff;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-body: #fdfdfd;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background-color: var(--bg-body);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        .display-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.2rem 0;
            transition: all 0.4s ease;
            z-index: 1000;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.9);
            padding: 0.8rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: white !important;
            transition: color 0.3s;
        }

        .navbar.scrolled .navbar-brand {
            color: var(--text-main) !important;
        }

        .navbar-brand span {
            color: var(--secondary);
        }

        .nav-link {
            font-weight: 600;
            color: white !important;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .navbar.scrolled .nav-link {
            color: var(--text-main) !important;
        }

        .nav-link:hover {
            color: var(--secondary) !important;
        }

        /* Hero Section - Immersive */
        .hero {
            height: 100vh;
            min-height: 800px;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)),
                url('https://images.unsplash.com/photo-1506929562872-bb421503ef21?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat;
            display: flex;
            align-items: center;
            position: relative;
            color: white;
            padding-top: 80px;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 150px;
            background: linear-gradient(to top, var(--bg-body), transparent);
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 2rem;
            letter-spacing: -2px;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            font-weight: 400;
            margin-bottom: 3rem;
            opacity: 0.9;
            max-width: 600px;
        }

        /* Glass Search Box */
        .search-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            padding: 15px;
            max-width: 900px;
            display: flex;
            align-items: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .search-group {
            flex: 1;
            padding: 0 25px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .search-group:last-child {
            border-right: none;
            padding-right: 10px;
        }

        .search-label {
            display: block;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 5px;
            opacity: 0.7;
        }

        .search-input {
            background: transparent;
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-input:focus {
            outline: none;
        }

        .btn-search {
            height: 60px;
            width: 60px;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(255, 94, 58, 0.3);
        }

        .btn-search:hover {
            transform: scale(1.05) rotate(5deg);
            background: #ff7b5e;
        }

        /* Section Styling */
        .section-padding {
            padding: 100px 0;
        }

        .section-tag {
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--primary);
            margin-bottom: 15px;
            display: block;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 3rem;
            letter-spacing: -1px;
            line-height: 1.1;
        }

        /* Luxury Package Cards */
        .package-card {
            border-radius: 40px;
            overflow: hidden;
            background: white;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .package-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 40px 80px rgba(90, 82, 229, 0.1);
        }

        .package-img-box {
            height: 320px;
            position: relative;
            overflow: hidden;
        }

        .package-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .package-card:hover .package-img {
            transform: scale(1.1);
        }

        .package-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6) 0%, transparent 60%);
            display: flex;
            align-items: flex-end;
            padding: 25px;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .package-card:hover .package-overlay {
            opacity: 1;
        }

        .package-duration {
            position: absolute;
            top: 25px;
            right: 25px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 8px 18px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 0.8rem;
            color: var(--primary);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .package-body {
            padding: 35px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .package-loc {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .package-name {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 25px;
            line-height: 1.2;
        }

        .package-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 25px;
            border-top: 1px solid #f1f5f9;
        }

        .package-price-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
        }

        .package-price {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .package-price span {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Stats Section */
        .stats-box {
            background: var(--primary);
            border-radius: 50px;
            padding: 80px 40px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(90, 82, 229, 0.3);
        }

        .stat-item h3 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Floating Animation */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float-anim {
            animation: float 4s ease-in-out infinite;
        }

        /* Testimonials */
        .testi-card {
            background: white;
            padding: 40px;
            border-radius: 30px;
            border: 1px solid #f1f5f9;
            position: relative;
        }

        .testi-quote {
            font-size: 3rem;
            color: var(--primary-light);
            position: absolute;
            top: 20px;
            right: 30px;
            opacity: 0.1;
        }

        /* Footer - Luxury Navy */
        footer {
            background: #0b0f19;
            color: white;
            padding: 100px 0 50px;
        }

        .footer-logo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 30px;
            display: block;
            text-decoration: none;
            color: white !important;
        }

        .footer-title {
            font-weight: 800;
            margin-bottom: 30px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 2px;
            color: var(--primary);
        }

        .footer-link {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .footer-link:hover {
            color: white;
            transform: translateX(5px);
        }

        .social-circle {
            width: 50px;
            height: 50px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.05);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: all 0.3s;
            color: white;
            text-decoration: none;
        }

        .social-circle:hover {
            background: var(--primary);
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title {
                font-size: 3.5rem;
            }

            .search-container {
                flex-direction: column;
                border-radius: 30px;
                padding: 30px;
            }

            .search-group {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
                width: 100%;
                padding: 15px 0;
            }

            .btn-search {
                width: 100%;
                margin-top: 20px;
            }

            .navbar-brand,
            .nav-link {
                color: var(--text-main) !important;
            }

            .navbar {
                background: white !important;
            }
        }
    </style>
</head>

<body>
    <!-- Removed currency selector from landing page -->

    <!-- Premium Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-airplane-engines-fill me-2"
                    style="color: var(--secondary); transform: rotate(-15deg); display: inline-block;"></i>Tourliz<span>.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-1 text-white" id="navIcon"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#">Discover</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Destinations</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Special Deals</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Support</a></li>
                    @guest
                        <li class="nav-item ms-lg-4">
                            <a class="btn btn-light fw-800 rounded-pill px-4 py-2 shadow-sm" href="{{ route('login') }}"
                                style="font-weight: 800; font-size: 0.9rem;">Sign In</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-4 text-center">
                            <a class="btn btn-primary fw-800 rounded-pill px-4 py-2 border-0 shadow-lg"
                                href="{{ route('admin.dashboard') }}"
                                style="background: var(--primary); font-weight: 800; font-size: 0.9rem;">Dashboard</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Immersive Hero -->
    <section class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-xl-8">
                    <div data-aos="fade-up" data-aos-duration="1000">
                        <span class="badge rounded-pill bg-white text-primary px-3 py-2 mb-4 fw-800"><i
                                class="bi bi-stars me-2"></i>PREMIUM TRAVEL PARTNER</span>
                        <h1 class="hero-title">Escape the Ordinary. <br>Explore the <span
                                style="background: linear-gradient(to right, #6dd5ed, #2193b0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Extraordinary</span>.
                        </h1>
                        <p class="hero-subtitle">Curated travel experiences for the discerning nomad. Discover the
                            world's most breathtaking hidden gems with Tourliz.</p>

                        <div class="search-container">
                            <div class="search-group">
                                <label class="search-label">Location</label>
                                <input type="text" class="search-input" placeholder="Where to next?">
                            </div>
                            <div class="search-group">
                                <label class="search-label">Travel Date</label>
                                <input type="text" class="search-input" placeholder="Add date">
                            </div>
                            <div class="search-group">
                                <label class="search-label">Add Guests</label>
                                <input type="text" class="search-input" placeholder="2 Adults">
                            </div>
                            <button class="btn btn-search">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Destinations Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <span class="section-tag">Destination Gallery</span>
                    <h2 class="section-title">Trending Locations</h2>
                </div>
            </div>
            <div class="row g-4">
                @foreach($topDestinations->take(4) as $index => $dest)
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <a href="/packages?country={{ $dest->id }}" class="text-decoration-none group">
                            <div class="position-relative overflow-hidden rounded-[40px] shadow-lg"
                                style="height: 450px; border-radius: 40px;">
                                <img src="https://images.unsplash.com/photo-{{ 1500000000000 + $index * 1234567 }}?auto=format&fit=crop&w=800&q=80"
                                    alt="{{ $dest->name }}"
                                    class="w-100 h-100 object-fit-cover transition-transform duration-700 hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-5"
                                    style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px; background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                    <h4 class="text-white fw-800 m-0 fs-3">{{ $dest->name }}</h4>
                                    <span class="text-white/60 small font-semibold">12+ Packages</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Luxury Packages -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-5" data-aos="fade-up">
                <div>
                    <span class="section-tag">Exclusive Offers</span>
                    <h2 class="section-title mb-md-0">Featured Experiences<span>.</span></h2>
                </div>
                <button class="btn btn-outline-primary rounded-pill px-5 py-3 fw-800">Browse All Packages <i
                        class="bi bi-arrow-right ms-2"></i></button>
            </div>

            <div class="row g-5">
                @forelse($featuredPackages as $index => $package)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}">
                        <div class="package-card"
                            onclick="window.location.href='{{ route('packages.show', $package->slug) }}'">
                            <div class="package-img-box">
                                <img src="{{ getImageUrl($package->image) }}" class="package-img"
                                    alt="{{ $package->name }}">
                                <div class="package-duration">
                                    <i class="bi bi-clock-fill me-1"></i> {{ $package->duration }}
                                </div>
                                <div class="package-overlay">
                                    <button class="btn btn-light rounded-pill w-100 fw-800 py-3 mb-3 shadow">Quick View
                                        Details</button>
                                </div>
                            </div>
                            <div class="package-body">
                                <div class="package-loc">
                                    <i class="bi bi-geo-alt-fill text-secondary"></i>
                                    {{ $package->destination->name ?? 'Global' }}
                                </div>
                                <h3 class="package-name">{{ $package->name }}</h3>
                                <div class="package-footer">
                                    <div>
                                        <span class="package-price-label">Starting Price</span>
                                        <div class="package-price" data-price="{{ $package->price }}"
                                            data-currency="{{ $package->currency ?? 'MYR' }}">
                                            {{ \App\Helpers\CurrencyHelper::format($package->price, $package->currency ?? 'MYR') }}
                                            <span>/ guest</span>
                                        </div>
                                    </div>
                                    <div class="btn btn-primary rounded-2xl p-0 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px; background: var(--primary); border-radius: 18px;">
                                        <i class="bi bi-arrow-right-short fs-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="glass-alert p-5 rounded-[40px] bg-white border border-gray-100 shadow-sm"
                            style="border-radius: 40px;">
                            <i class="bi bi-cloud-unfill text-primary display-1 opacity-20"></i>
                            <h4 class="fw-800 mt-4">Exploring Destinations...</h4>
                            <p class="text-muted">New curated adventures are being uploaded. Check back shortly!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Statistics & Social Proof -->
    <section class="section-padding">
        <div class="container">
            <div class="stats-box" data-aos="zoom-in">
                <div class="row g-5">
                    <div class="col-md-4 stat-item">
                        <h3>12k+</h3>
                        <p>Happy Travelers</p>
                    </div>
                    <div class="col-md-4 stat-item">
                        <h3>450+</h3>
                        <p>Luxury Spots</p>
                    </div>
                    <div class="col-md-4 stat-item">
                        <h3>99%</h3>
                        <p>Positive Ratings</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Premium Footer -->
    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a href="/" class="footer-logo">
                        <i class="bi bi-airplane-engines-fill text-primary"></i> Tourliz.
                    </a>
                    <p class="text-white opacity-60 mb-4 leading-relaxed">Redefining luxury travel for the modern
                        explorer. Our mission is to transform your travel dreams into reality with uncompromised comfort
                        and authentic experiences.</p>
                    <div class="d-flex">
                        <a href="#" class="social-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-tiktok"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 offset-lg-1">
                    <h5 class="footer-title">Navigation</h5>
                    <a href="#" class="footer-link">Travel Deals</a>
                    <a href="#" class="footer-link">Hidden Gems</a>
                    <a href="#" class="footer-link">Popular Spots</a>
                    <a href="#" class="footer-link">Blog & News</a>
                </div>
                <div class="col-lg-2">
                    <h5 class="footer-title">Help Centre</h5>
                    <a href="#" class="footer-link">Support Desk</a>
                    <a href="#" class="footer-link">Booking Guide</a>
                    <a href="#" class="footer-link">Terms & Safety</a>
                    <a href="#" class="footer-link">Work With Us</a>
                </div>
                <div class="col-lg-3">
                    <h5 class="footer-title">Stay Inspired</h5>
                    <p class="small opacity-60 mb-4">Subscribe to our newsletter for exclusive travel tips and secret
                        offers.</p>
                    <div class="input-group">
                        <input type="text" class="form-control bg-white/5 border-0 text-white rounded-start-pill px-4"
                            placeholder="Your email here" style="background: rgba(255,255,255,0.05);">
                        <button class="btn btn-primary rounded-end-pill px-4">Join</button>
                    </div>
                </div>
            </div>
            <hr class="my-5 opacity-10">
            <div class="text-center">
                <p class="small opacity-40">&copy; 2026 Tourliz CMS. Inspired by High-End Travel Design. All rights
                    reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true });

        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
                $('#navIcon').removeClass('text-white').addClass('text-dark');
            } else {
                $('.navbar').removeClass('scrolled');
                $('#navIcon').addClass('text-white').removeClass('text-dark');
            }
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
</body>

</html>