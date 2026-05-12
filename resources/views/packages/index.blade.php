<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Holiday Packages - Tourliz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --primary-dark: #c82333;
            --secondary: #6c757d;
            --bg-light: #f8f9fa;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #1a1a1a;
        }
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .search-hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1506929113674-b779480ac218?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
            padding: 80px 0;
            color: #fff;
            border-radius: 0 0 40px 40px;
        }
        .filter-sidebar {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            position: sticky;
            top: 20px;
        }
        .package-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .package-img-container {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        .package-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .package-card:hover .package-img {
            transform: scale(1.1);
        }
        .badge-category {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255,255,255,0.9);
            color: var(--primary);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .price-tag {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.25rem;
        }
        .duration-tag {
            font-size: 0.85rem;
            color: var(--secondary);
        }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .pagination .page-link {
            color: var(--primary);
            border-radius: 10px;
            margin: 0 3px;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }
    </style>
</head>
<body>

    @include('components.currency-selector')

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger fs-3" href="/">Tourliz</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link fw-semibold mx-2" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link active fw-semibold mx-2 text-danger" href="/packages">Packages</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold mx-2" href="#">Destinations</a></li>
                    @guest
                        <li class="nav-item ms-3"><a class="btn btn-outline-danger rounded-pill px-4" href="/login">Login</a></li>
                    @else
                        <li class="nav-item ms-3"><a class="btn btn-danger rounded-pill px-4" href="/admin">Dashboard</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Hero -->
    <div class="search-hero mb-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">Discover Your Next Adventure</h1>
                    <p class="lead mb-4 opacity-75">Explore hand-picked holiday packages at the best prices.</p>
                    <form action="/packages" method="GET" class="bg-white p-2 rounded-pill shadow-lg d-flex">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 rounded-pill px-4" placeholder="Search destinations, packages...">
                        <button type="submit" class="btn btn-danger rounded-pill px-4 ms-2">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <form action="/packages" method="GET" id="filterForm">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <div class="filter-sidebar">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Filters</h5>
                            <a href="/packages" class="text-danger small text-decoration-none">Reset</a>
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Duration</h6>
                            @foreach(['1-3' => '1-3 Days', '4-6' => '4-6 Days', '7-9' => '7-9 Days', '10+' => '10+ Days'] as $val => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="duration" value="{{ $val }}" id="dur_{{ $val }}" {{ request('duration') == $val ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label small" for="dur_{{ $val }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Category</h6>
                            @foreach($categories as $cat)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="category" value="{{ $cat }}" id="cat_{{ $cat }}" {{ request('category') == $cat ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label small" for="cat_{{ $cat }}">{{ $cat }}</label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Destination -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Destination</h6>
                            <select name="destination" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                                <option value="">All Destinations</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ request('destination') == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Package Results -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">{{ $packages->total() }} Packages Found</h5>
                    <div class="d-flex align-items-center">
                        <span class="small text-muted me-2">Sort by:</span>
                        <select class="form-select form-select-sm rounded-3" style="width: auto;" onchange="window.location.href='/packages?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)), sort: this.value})">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4">
                    @forelse($packages as $pkg)
                        <div class="col-md-6 col-xl-4">
                            <div class="package-card">
                                <div class="package-img-container">
                                    <img src="{{ getImageUrl($pkg->image) }}" class="package-img" alt="{{ $pkg->name }}">
                                    @if($pkg->category)
                                        <span class="badge-category">{{ $pkg->category }}</span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt-fill text-danger me-1 small"></i>
                                        <span class="small text-muted">{{ $pkg->destination->name ?? 'Global' }}</span>
                                    </div>
                                    <h6 class="fw-bold mb-3 text-dark">{{ Str::limit($pkg->name, 45) }}</h6>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="duration-tag">
                                            <i class="bi bi-clock me-1"></i> {{ $pkg->duration_days }}D / {{ $pkg->duration_nights }}N
                                        </div>
                                        <div class="text-end">
                                            <div class="price-tag">{{ \App\Helpers\CurrencyHelper::format($pkg->price, $pkg->currency ?? 'MYR') }}</div>
                                            <div class="small text-muted" style="font-size: 0.7rem;">per person</div>
                                        </div>
                                    </div>
                                    <a href="/packages/{{ $pkg->slug }}" class="btn btn-outline-danger w-100 rounded-pill mt-4 fw-bold small">View Details</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                            <h4 class="text-muted">No packages found</h4>
                            <p class="text-muted">Try adjusting your filters or search query.</p>
                            <a href="/packages" class="btn btn-danger rounded-pill px-4">View All Packages</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $packages->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <h3 class="fw-bold text-danger mb-4">Tourliz</h3>
            <p class="opacity-50 small mb-4">Your premium travel partner for unforgettable experiences.</p>
            <div class="d-flex justify-content-center gap-3 mb-4">
                <a href="#" class="text-white opacity-75"><i class="bi bi-facebook fs-5"></i></a>
                <a href="#" class="text-white opacity-75"><i class="bi bi-instagram fs-5"></i></a>
                <a href="#" class="text-white opacity-75"><i class="bi bi-twitter fs-5"></i></a>
            </div>
            <p class="extra-small opacity-50 mb-0">&copy; {{ date('Y') }} Tourliz Travel CMS. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
