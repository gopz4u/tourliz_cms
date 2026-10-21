<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $groupTour->meta_title ?: $groupTour->name . ' - Tourliz Group Tour' }}</title>
    <meta name="description" content="{{ $groupTour->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($groupTour->short_description ?: $groupTour->description), 155) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --tourliz-red: #dc3545; --tourliz-dark: #101828; --tourliz-soft: #fff5f5; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--tourliz-dark); background: #fff; }
        .navbar { background: #fff; box-shadow: 0 2px 16px rgba(16,24,40,.06); padding: 14px 0; }
        .brand { color: var(--tourliz-red); font-weight: 800; font-size: 1.65rem; text-decoration: none; }
        .btn-tourliz { background: var(--tourliz-red); border-color: var(--tourliz-red); color: #fff; font-weight: 700; }
        .btn-tourliz:hover { background: #bb2d3b; border-color: #bb2d3b; color: #fff; }
        .hero-wrap { background: #f8f9fb; padding: 36px 0 46px; }
        .main-photo, .gallery-photo { width: 100%; object-fit: cover; border-radius: 22px; }
        .main-photo { height: 500px; }
        .gallery-photo { height: 242px; }
        .gallery-column { position: relative; }
        .gallery-button { position: absolute; right: 18px; bottom: 18px; background: rgba(255,255,255,.96); box-shadow: 0 8px 22px rgba(16,24,40,.18); font-weight: 800; }
        .photo-placeholder { height: 500px; display: grid; place-items: center; border-radius: 22px; color: #fff; background: linear-gradient(135deg, #7b1623, #f05b67); }
        .eyebrow { text-transform: uppercase; letter-spacing: .13em; font-weight: 800; font-size: .73rem; }
        .content-section { padding: 60px 0; }
        .booking-card { border: 0; border-radius: 24px; box-shadow: 0 16px 50px rgba(16,24,40,.12); position: sticky; top: 95px; overflow: hidden; }
        .departure-row { border: 1px solid #e4e7ec; border-radius: 16px; padding: 16px; }
        .departure-row + .departure-row { margin-top: 12px; }
        .departure-row.featured { border-color: #f1aeb5; background: #fff8f8; }
        .seat-badge { background: #d1fadf; color: #067647; border-radius: 999px; padding: 5px 9px; font-weight: 800; font-size: .7rem; }
        .seat-badge.sold { background: #fee4e2; color: #b42318; }
        .day-block { position: relative; padding-left: 66px; padding-bottom: 32px; }
        .day-block:not(:last-child)::before { content: ''; position: absolute; left: 24px; top: 50px; bottom: -4px; width: 2px; background: #f1c0c5; }
        .day-number { position: absolute; left: 0; top: 0; width: 50px; height: 50px; display: grid; place-items: center; border-radius: 16px; background: var(--tourliz-red); color: #fff; font-weight: 800; box-shadow: 0 8px 20px rgba(220,53,69,.25); }
        .day-card { border: 1px solid #eaecf0; border-radius: 20px; padding: 24px; box-shadow: 0 6px 20px rgba(16,24,40,.04); }
        .item-badge { display: inline-flex; align-items: center; gap: 7px; padding: 8px 11px; border-radius: 10px; background: #f8f9fb; border: 1px solid #eaecf0; font-size: .82rem; font-weight: 700; }
        .item-badge.spot, .item-badge.tourist_spot { background: #ecfdff; color: #0e7090; border-color: #a5f0fc; }
        .item-badge.hotel { background: #eff8ff; color: #175cd3; border-color: #b2ddff; }
        .item-badge.meal { background: #fffaeb; color: #b54708; border-color: #fedf89; }
        .item-badge.transport { background: #f4f3ff; color: #5925dc; border-color: #d9d6fe; }
        .check-list { list-style: none; padding: 0; margin: 0; }
        .check-list li { display: flex; gap: 10px; padding: 8px 0; }
        .related-card { border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 7px 24px rgba(16,24,40,.07); height: 100%; }
        footer { background: #101828; color: #fff; }
        @media (max-width: 767.98px) { .main-photo, .photo-placeholder { height: 340px; } .gallery-photo { height: 170px; } .day-block { padding-left: 55px; } .day-number { width: 42px; height: 42px; } }
    </style>
</head>
<body>
    @include('components.currency-selector')
    <nav class="navbar navbar-expand-lg sticky-top" aria-label="Main navigation">
        <div class="container">
            <a class="brand" href="{{ route('landing') }}">Tourliz<span class="text-dark">.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
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

    <header class="hero-wrap">
        <div class="container">
            <a href="{{ route('group-tours.index') }}" class="text-decoration-none text-muted small fw-semibold"><i class="bi bi-arrow-left me-2"></i>All group tours</a>
            <div class="row align-items-end g-4 mt-1 mb-4">
                <div class="col-lg-8">
                    <div class="eyebrow text-danger mb-2"><i class="bi bi-people-fill me-2"></i>Scheduled group departure</div>
                    <h1 class="display-5 fw-bold mb-3">{{ $groupTour->name }}</h1>
                    <div class="d-flex flex-wrap gap-3 text-muted fw-semibold">
                        <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ optional($groupTour->destination)->name ?: 'Multiple destinations' }}</span>
                        <span><i class="bi bi-clock text-danger me-1"></i>{{ $groupTour->duration ?: 'Flexible duration' }}</span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="#departures" class="btn btn-tourliz rounded-pill px-4 py-3">View departure dates <i class="bi bi-arrow-down ms-2"></i></a>
                </div>
            </div>

            <div class="row g-3">
                <div class="{{ count($groupTour->gallery ?: []) ? 'col-lg-8' : 'col-12' }}">
                    @if($groupTour->image)
                        <img src="{{ getImageUrl($groupTour->image) }}" class="main-photo" alt="{{ $groupTour->name }}">
                    @else
                        <div class="photo-placeholder"><i class="bi bi-airplane-engines display-3"></i></div>
                    @endif
                </div>
                @if(count($groupTour->gallery ?: []))
                    <div class="col-lg-4 gallery-column">
                        <div class="row g-3">
                            @foreach(array_slice($groupTour->gallery, 0, 4) as $photo)
                                <div class="col-6 col-lg-12 {{ $loop->iteration > 2 ? 'd-lg-none' : '' }}">
                                    <img src="{{ getImageUrl($photo) }}" class="gallery-photo" alt="{{ $groupTour->name }} gallery photo" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn gallery-button rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tourGalleryModal"><i class="bi bi-images me-2"></i>View all {{ count($groupTour->gallery) }} photos</button>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <main class="content-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <section class="mb-5">
                        <div class="eyebrow text-danger mb-2">The experience</div>
                        <h2 class="h3 fw-bold mb-3">About this group tour</h2>
                        <div class="text-muted lh-lg">{!! $groupTour->description ?: e($groupTour->short_description ?: 'Contact Tourliz for the full tour description.') !!}</div>
                    </section>

                    @php
                        $source = $groupTour->fixedItinerary;
                        $days = $source ? ($source->days ?: []) : [];
                    @endphp
                    @if(count($days))
                        <section class="mb-5" id="itinerary">
                            <div class="eyebrow text-danger mb-2">Day by day</div>
                            <h2 class="h3 fw-bold mb-4">Tour itinerary</h2>
                            @foreach($days as $index => $day)
                                <article class="day-block">
                                    <div class="day-number">{{ $day['day_number'] ?? $index + 1 }}</div>
                                    <div class="day-card">
                                        <div class="small text-danger fw-bold text-uppercase mb-1">Day {{ $day['day_number'] ?? $index + 1 }}</div>
                                        <h3 class="h5 fw-bold mb-2">{{ $day['title'] ?? 'Tour day' }}</h3>
                                        @if(!empty($day['description']))<p class="text-muted mb-3">{{ $day['description'] }}</p>@endif
                                        @if(!empty($day['items']) && is_array($day['items']))
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($day['items'] as $item)
                                                    @php
                                                        $type = strtolower($item['item_type'] ?? 'activity');
                                                        $icons = ['spot' => 'bi-geo-alt-fill', 'tourist_spot' => 'bi-geo-alt-fill', 'hotel' => 'bi-building', 'meal' => 'bi-cup-hot', 'transport' => 'bi-bus-front', 'activity' => 'bi-camera'];
                                                        $labels = ['spot' => 'Tourist Spot', 'tourist_spot' => 'Tourist Spot', 'hotel' => 'Hotel', 'meal' => 'Meal', 'transport' => 'Transport', 'activity' => 'Activity'];
                                                    @endphp
                                                    <span class="item-badge {{ $type }}" title="{{ $item['description'] ?? '' }}"><i class="bi {{ $icons[$type] ?? 'bi-star' }}"></i><span><small class="d-block text-uppercase opacity-75">{{ $labels[$type] ?? ucfirst($type) }}</small>{{ $item['title'] ?? $item['name'] ?? 'Included item' }}</span></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </section>
                    @endif

                    @php
                        $included = $groupTour->included_services ?: ($source ? $source->inclusions : []);
                        $excluded = $groupTour->excluded_services ?: ($source ? $source->exclusions : []);
                    @endphp
                    @if(count($included ?: []) || count($excluded ?: []))
                        <section class="mb-5">
                            <div class="eyebrow text-danger mb-2">Good to know</div>
                            <h2 class="h3 fw-bold mb-4">What is included</h2>
                            <div class="row g-4">
                                @if(count($included ?: []))
                                    <div class="col-md-6"><div class="p-4 rounded-4 bg-success bg-opacity-10 h-100"><h3 class="h6 fw-bold text-success mb-3">Included</h3><ul class="check-list">@foreach($included as $item)<li><i class="bi bi-check-circle-fill text-success"></i><span>{{ is_array($item) ? ($item['name'] ?? json_encode($item)) : $item }}</span></li>@endforeach</ul></div></div>
                                @endif
                                @if(count($excluded ?: []))
                                    <div class="col-md-6"><div class="p-4 rounded-4 bg-danger bg-opacity-10 h-100"><h3 class="h6 fw-bold text-danger mb-3">Not included</h3><ul class="check-list">@foreach($excluded as $item)<li><i class="bi bi-x-circle-fill text-danger"></i><span>{{ is_array($item) ? ($item['name'] ?? json_encode($item)) : $item }}</span></li>@endforeach</ul></div></div>
                                @endif
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="col-lg-4" id="departures">
                    <div class="booking-card card">
                        <div class="p-4 border-bottom">
                            <div class="eyebrow text-danger mb-2">Upcoming dates</div>
                            <h2 class="h4 fw-bold mb-1">Choose your departure</h2>
                            <p class="small text-muted mb-0">Rates shown are per adult.</p>
                        </div>
                        <div class="p-4">
                            <div class="departure-row featured">
                                <div class="d-flex justify-content-between gap-2 mb-2">
                                    <div class="fw-bold"><i class="bi bi-calendar-event text-danger me-2"></i>{{ $groupTour->departure_date->format('d M Y') }}</div>
                                    <span class="seat-badge">{{ $groupTour->total_pax ? $groupTour->total_pax . ' seats' : 'Enquire for seats' }}</span>
                                </div>
                                @if($groupTour->return_date)<div class="small text-muted mb-2">Returns {{ $groupTour->return_date->format('d M Y') }}</div>@endif
                                <div class="fw-bold text-danger fs-5">{{ \App\Helpers\CurrencyHelper::format($groupTour->price, $groupTour->currency ?: 'MYR') }}</div>
                                @if($groupTour->child_price !== null)<div class="small text-muted mt-1">Child: {{ \App\Helpers\CurrencyHelper::format($groupTour->child_price, $groupTour->currency ?: 'MYR') }}</div>@endif
                            </div>
                            @php $message = urlencode('Hello Tourliz, I am interested in the group tour: ' . $groupTour->name . ' ' . route('group-tours.show', $groupTour->slug)); @endphp
                            <a href="https://wa.me/?text={{ $message }}" target="_blank" rel="noopener" class="btn btn-success w-100 rounded-pill py-3 fw-bold mt-4"><i class="bi bi-whatsapp me-2"></i>Enquire on WhatsApp</a>
                            <div class="text-center small text-muted mt-3"><i class="bi bi-shield-check me-1"></i>Tourliz will confirm final availability.</div>
                        </div>
                    </div>
                </aside>
            </div>

            @if($relatedTours->count())
                <section class="pt-5 border-top mt-4">
                    <div class="eyebrow text-danger mb-2">More departures</div>
                    <h2 class="h3 fw-bold mb-4">You may also like</h2>
                    <div class="row g-4">
                        @foreach($relatedTours as $related)
                            <div class="col-md-4"><article class="related-card bg-white">@if($related->image)<img src="{{ getImageUrl($related->image) }}" alt="{{ $related->name }}" class="w-100" style="height: 190px; object-fit: cover;" loading="lazy">@endif<div class="p-4"><div class="small text-danger fw-bold mb-2">{{ $related->departure_date->format('d M Y') }}</div><h3 class="h6 fw-bold">{{ $related->name }}</h3><a href="{{ route('group-tours.show', $related->slug) }}" class="text-danger fw-bold text-decoration-none small">View group tour <i class="bi bi-arrow-right"></i></a></div></article></div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>

    <footer class="py-5 mt-4"><div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"><div><div class="h4 fw-bold mb-1">Tourliz.</div><div class="small opacity-50">Your travel partner for memorable journeys.</div></div><a href="{{ route('group-tours.index') }}" class="text-white text-decoration-none">Explore all group tours <i class="bi bi-arrow-right ms-1"></i></a></div></footer>

    @if(count($groupTour->gallery ?: []))
        <div class="modal fade" id="tourGalleryModal" tabindex="-1" aria-labelledby="tourGalleryTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 overflow-hidden">
                    <div class="modal-header border-0"><h2 class="modal-title h5 fw-bold" id="tourGalleryTitle">{{ $groupTour->name }} album</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body p-0">
                        <div id="tourGalleryCarousel" class="carousel slide">
                            <div class="carousel-inner">
                                @foreach($groupTour->gallery as $photo)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}"><img src="{{ getImageUrl($photo) }}" class="d-block w-100" style="height: min(70vh, 720px); object-fit: contain; background: #101828;" alt="{{ $groupTour->name }} album photo {{ $loop->iteration }}"></div>
                                @endforeach
                            </div>
                            @if(count($groupTour->gallery) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#tourGalleryCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>
                                <button class="carousel-control-next" type="button" data-bs-target="#tourGalleryCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
