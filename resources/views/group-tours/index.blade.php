<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Group Tours - Tourliz</title>
    <meta name="description" content="Explore upcoming Tourliz group departures, dates, prices and available seats.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --tourliz-red: #dc3545; --tourliz-dark: #101828; --tourliz-soft: #fff5f5; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--tourliz-dark); background: #f7f8fb; }
        .navbar { background: #fff; box-shadow: 0 2px 16px rgba(16, 24, 40, .06); padding: 14px 0; }
        .brand { color: var(--tourliz-red); font-weight: 800; font-size: 1.65rem; text-decoration: none; }
        .hero { background: linear-gradient(120deg, rgba(16,24,40,.92), rgba(123,22,35,.84)), url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=2000&q=80') center/cover; color: #fff; padding: 86px 0 100px; border-radius: 0 0 42px 42px; }
        .eyebrow { text-transform: uppercase; letter-spacing: .14em; font-weight: 800; font-size: .74rem; }
        .search-panel { margin-top: -38px; position: relative; z-index: 2; }
        .search-card { background: #fff; padding: 18px; border-radius: 22px; box-shadow: 0 16px 50px rgba(16,24,40,.12); }
        .tour-card { border: 0; border-radius: 24px; overflow: hidden; box-shadow: 0 8px 28px rgba(16,24,40,.07); transition: transform .25s ease, box-shadow .25s ease; height: 100%; background: #fff; }
        .tour-card:hover { transform: translateY(-6px); box-shadow: 0 18px 42px rgba(16,24,40,.13); }
        .tour-image { height: 235px; width: 100%; object-fit: cover; }
        .tour-placeholder { height: 235px; background: linear-gradient(135deg, #7b1623, #f05b67); color: #fff; display: grid; place-items: center; }
        .departure-pill { position: absolute; left: 16px; bottom: 16px; background: rgba(255,255,255,.96); color: var(--tourliz-dark); border-radius: 14px; padding: 9px 13px; font-weight: 800; font-size: .8rem; box-shadow: 0 7px 22px rgba(16,24,40,.15); }
        .status-pill { position: absolute; right: 16px; top: 16px; border-radius: 999px; padding: 7px 11px; font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
        .status-open { background: #d1fadf; color: #067647; }
        .status-announced { background: #e0e7ff; color: #3730a3; }
        .meta-icon { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 10px; background: var(--tourliz-soft); color: var(--tourliz-red); }
        .price { color: var(--tourliz-red); font-size: 1.3rem; font-weight: 800; }
        .btn-tourliz { background: var(--tourliz-red); border-color: var(--tourliz-red); color: #fff; font-weight: 700; }
        .btn-tourliz:hover { background: #bb2d3b; border-color: #bb2d3b; color: #fff; }
        .filter-control { min-height: 48px; border-radius: 13px; border-color: #e4e7ec; }
        .pagination .page-link { color: var(--tourliz-red); border-radius: 10px; margin: 0 3px; }
        .pagination .active > .page-link { background: var(--tourliz-red); border-color: var(--tourliz-red); }
        footer { background: #101828; color: #fff; }
    </style>
</head>
<body>
    @include('components.currency-selector')

    <nav class="navbar navbar-expand-lg sticky-top" aria-label="Main navigation">
        <div class="container">
            <a class="brand" href="{{ route('landing') }}">Tourliz<span class="text-dark">.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('landing') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('packages.index') }}">Packages</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold text-danger" href="{{ route('group-tours.index') }}">Group Tours</a></li>
                    @guest
                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger rounded-pill px-4" href="{{ route('login') }}">Sign In</a></li>
                    @else
                        <li class="nav-item ms-lg-3"><a class="btn btn-tourliz rounded-pill px-4" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container text-center">
            <span class="eyebrow text-warning">Travel together, depart together</span>
            <h1 class="display-4 fw-bold mt-3 mb-3">Upcoming Group Tours</h1>
            <p class="lead opacity-75 mb-0 mx-auto" style="max-width: 720px;">Choose a confirmed travel date, see current availability, and explore the complete day-by-day experience before you enquire.</p>
        </div>
    </header>

    <main>
        <section class="search-panel">
            <div class="container">
                <form action="{{ route('group-tours.index') }}" method="GET" class="search-card">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5">
                            <label for="q" class="form-label small fw-bold">Search a tour or place</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4"><i class="bi bi-search text-muted"></i></span>
                                <input id="q" type="search" name="q" value="{{ request('q') }}" class="form-control filter-control border-start-0" placeholder="Malaysia, Bali, Kashmir...">
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-3">
                            <label for="destination" class="form-label small fw-bold">Destination</label>
                            <select id="destination" name="destination" class="form-select filter-control">
                                <option value="">All destinations</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ (string) request('destination') === (string) $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <label for="sort" class="form-label small fw-bold">Sort by</label>
                            <select id="sort" name="sort" class="form-select filter-control">
                                <option value="departure" {{ request('sort', 'departure') === 'departure' ? 'selected' : '' }}>Next departure</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Lowest price</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Highest price</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2 d-grid">
                            <button class="btn btn-tourliz filter-control"><i class="bi bi-sliders me-2"></i>Find Tours</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="py-5">
            <div class="container py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div>
                        <span class="eyebrow text-danger">Live departures</span>
                        <h2 class="h3 fw-bold mt-2 mb-0">{{ $groupTours->total() }} {{ \Illuminate\Support\Str::plural('tour', $groupTours->total()) }} available</h2>
                    </div>
                    @if(request()->hasAny(['q', 'destination', 'sort']))
                        <a href="{{ route('group-tours.index') }}" class="btn btn-light border rounded-pill px-4">Clear filters</a>
                    @endif
                </div>

                <div class="row g-4">
                    @forelse($groupTours as $tour)
                        @php
                            $seats = $tour->total_pax;
                        @endphp
                        <div class="col-md-6 col-xl-4">
                            <article class="tour-card">
                                <div class="position-relative">
                                    @if($tour->image)
                                        <img src="{{ getImageUrl($tour->image) }}" class="tour-image" alt="{{ $tour->name }}" loading="lazy">
                                    @else
                                        <div class="tour-placeholder" aria-hidden="true"><i class="bi bi-airplane-engines display-5"></i></div>
                                    @endif
                                    <span class="status-pill status-open">open</span>
                                    <span class="departure-pill"><i class="bi bi-calendar-event text-danger me-2"></i>{{ $tour->departure_date->format('d M Y') }}</span>
                                </div>
                                <div class="p-4">
                                    <div class="small text-muted fw-semibold mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ optional($tour->destination)->name ?: 'Multiple destinations' }}</div>
                                    <h3 class="h5 fw-bold mb-3">{{ $tour->name }}</h3>
                                    <p class="text-muted small mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($tour->short_description ?: $tour->description), 105) }}</p>
                                    <div class="d-flex gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="meta-icon"><i class="bi bi-clock"></i></span>
                                            <span class="small fw-semibold">{{ $tour->duration ?: 'Flexible' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="meta-icon"><i class="bi bi-people"></i></span>
                                            <span class="small fw-semibold">{{ $seats ? $seats . ' seats' : 'Enquire for seats' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end gap-3 border-top pt-3">
                                        <div>
                                            <div class="text-muted" style="font-size: .72rem;">FROM / PERSON</div>
                                            <div class="price">{{ \App\Helpers\CurrencyHelper::format($tour->price, $tour->currency ?? 'MYR') }}</div>
                                        </div>
                                        <a href="{{ route('group-tours.show', $tour->slug) }}" class="btn btn-tourliz rounded-pill px-4" aria-label="View {{ $tour->name }}">View Tour</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="bg-white rounded-4 p-5 text-center shadow-sm">
                                <i class="bi bi-calendar-x display-4 text-danger"></i>
                                <h2 class="h4 fw-bold mt-3">No upcoming group tours found</h2>
                                <p class="text-muted">Try clearing the filters, or check again when new departure dates are announced.</p>
                                <a href="{{ route('group-tours.index') }}" class="btn btn-tourliz rounded-pill px-4">View all departures</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($groupTours->hasPages())
                    <div class="d-flex justify-content-center mt-5">{{ $groupTours->links() }}</div>
                @endif
            </div>
        </section>
    </main>

    <footer class="py-5 mt-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div><div class="h4 fw-bold mb-1">Tourliz.</div><div class="small opacity-50">Your travel partner for memorable journeys.</div></div>
            <div class="d-flex gap-4"><a href="{{ route('packages.index') }}" class="text-white text-decoration-none opacity-75">Packages</a><a href="{{ route('group-tours.index') }}" class="text-white text-decoration-none">Group Tours</a></div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
