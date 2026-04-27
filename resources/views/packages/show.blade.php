<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $package->name }} - Package Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }

        .package-header {
            background: #fff;
            padding: 20px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .package-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .main-image-container {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        .thumbnail-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .thumbnail {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .thumbnail:hover {
            border-color: #dc3545;
            transform: scale(1.02);
        }

        .thumbnail.active {
            border-color: #dc3545;
        }

        .gallery-link {
            color: #dc3545;
            text-decoration: none;
            font-weight: 500;
            margin-top: 10px;
            display: inline-block;
        }

        .gallery-link:hover {
            text-decoration: underline;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1a1a1a;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .amenity-icon {
            color: #dc3545;
            margin-right: 10px;
            font-size: 18px;
        }

        .package-details-item {
            margin-bottom: 10px;
            font-size: 15px;
        }

        .package-details-label {
            font-weight: 600;
            color: #666;
        }

        .booking-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            position: sticky;
            top: 20px;
        }

        .price-strikethrough {
            text-decoration: line-through;
            color: #999;
            font-size: 16px;
        }

        .price-current {
            font-size: 32px;
            font-weight: 700;
            color: #dc3545;
            margin: 5px 0;
        }

        .price-label {
            color: #666;
            font-size: 14px;
        }

        .btn-book {
            background: #dc3545;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-book:hover {
            background: #c82333;
        }

        .btn-whatsapp {
            background: #25D366;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-whatsapp:hover {
            background: #20ba5a;
        }

        .btn-quote {
            border: 2px solid #007bff;
            color: #007bff;
            background: transparent;
            padding: 10px 24px;
            font-weight: 600;
            width: 100%;
        }

        .btn-quote:hover {
            background: #007bff;
            color: #fff;
        }

        .category-btn {
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            padding: 8px 16px;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }

        .category-btn:hover {
            border-color: #dc3545;
            color: #dc3545;
        }

        .related-package {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .related-package:hover {
            transform: translateY(-5px);
        }

        .related-package img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .favorite-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc3545;
            cursor: pointer;
        }

        .location-info {
            color: #666;
            font-size: 14px;
        }

        .login-prompt {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }

        .free-cancellation {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .free-cancellation input[type="checkbox"] {
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }
    </style>
</head>

<body>
    @include('components.currency-selector')

    <div class="package-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="package-title">{{ $package->name }}</h1>
                <a href="#" onclick="window.history.back(); return false;" class="text-decoration-none">
                    <i class="bi bi-arrow-up-circle fs-4 text-secondary"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Image Gallery -->
                <div class="row mb-4">
                    <div class="col-md-9">
                        <div class="main-image-container">
                            <img id="main-image" src="{{ getImageUrl($package->image) }}" alt="{{ $package->name }}"
                                class="main-image">
                        </div>
                        <a href="#" class="gallery-link" onclick="openGallery(); return false;">
                            {{ count($package->gallery ?? []) + 1 }} Property & Guest Photos →
                        </a>
                    </div>
                    <div class="col-md-3">
                        <div class="thumbnail-container">
                            @if($package->image)
                                <img src="{{ getImageUrl($package->image) }}" alt="Main" class="thumbnail active"
                                    onclick="changeMainImage('{{ getImageUrl($package->image) }}', this)">
                            @endif
                            @if($package->gallery)
                                @foreach(array_slice($package->gallery, 0, 3) as $img)
                                    <img src="{{ getImageUrl($img) }}" alt="Gallery" class="thumbnail"
                                        onclick="changeMainImage('{{ getImageUrl($img) }}', this)">
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- About Package -->
                <div class="mb-4">
                    <h2 class="section-title">About Package</h2>
                    <div class="mb-3">
                        {!! $package->description ?? $package->short_description ?? 'No description available.' !!}
                    </div>
                    <div>
                        <a href="#food-dining" class="category-btn">
                            <i class="bi bi-utensils"></i> Food and Dining
                        </a>
                        <a href="#location" class="category-btn">
                            <i class="bi bi-geo-alt"></i> Location & Surroundings
                        </a>
                    </div>
                </div>

                <!-- Amenities -->
                @if($package->addon_amenities && count($package->addon_amenities) > 0)
                    <div class="mb-4">
                        <h2 class="section-title">Amenities</h2>
                        <div class="row">
                            @foreach($package->addon_amenities as $amenity)
                                <div class="col-md-6">
                                    <div class="amenity-item">
                                        <i class="bi bi-check-circle-fill amenity-icon"></i>
                                        <span>{{ $amenity['name'] }}{!! isset($amenity['value']) && $amenity['value'] ? ' - ' . $amenity['value'] : '' !!}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Package Details -->
                <div class="mb-4">
                    <h2 class="section-title">Package Details</h2>
                    <div class="package-details-item">
                        <span class="package-details-label">Duration:</span>
                        @if($package->duration_days || $package->duration_nights)
                            {{ $package->duration_days ?? 0 }} days, {{ $package->duration_nights ?? 0 }} nights
                        @elseif($package->duration)
                            {{ $package->duration }}
                        @else
                            Not specified
                        @endif
                    </div>
                    @if($package->destination)
                        <div class="package-details-item">
                            <span class="package-details-label">Location:</span>
                            {{ $package->destination->name }}
                        </div>
                    @endif
                    @if($package->category)
                        <div class="package-details-item">
                            <span class="package-details-label">Category:</span>
                            {{ $package->category }}
                        </div>
                    @endif
                </div>

                <!-- Itinerary -->
                @if($package->itinerary && count($package->itinerary) > 0)
                    <div class="mb-5">
                        <h2 class="section-title mb-4"><i class="bi bi-calendar3 me-2 text-danger"></i>Detailed Itinerary</h2>
                        <div class="accordion" id="itineraryAccordion">
                            @foreach($package->formatted_itinerary as $index => $day)
                                <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#day{{ $day['day'] }}">
                                            <div class="d-flex align-items-center w-100">
                                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="min-width: 40px; height: 40px; font-weight: bold;">
                                                    {{ $day['day'] }}
                                                </div>
                                                <div>
                                                    <div class="text-muted small text-uppercase">Day {{ $day['day'] }}</div>
                                                    <div class="fw-bold">{{ $day['title'] }}</div>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="day{{ $day['day'] }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#itineraryAccordion">
                                        <div class="accordion-body bg-white pt-0">
                                            <div class="ps-5 ms-2 border-start py-3">
                                                <!-- Places/Attractions -->
                                                @if(isset($day['places']) && count($day['places']) > 0)
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold small text-uppercase text-danger mb-2">Places to Visit:</h6>
                                                        <div class="row g-2">
                                                            @foreach($day['places'] as $p)
                                                                <div class="col-md-6">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                                                        <span>{{ $p['place_name'] ?? $p['name'] ?? 'Attraction' }} @if(isset($p['visit_duration'])) <small class="text-muted">({{ $p['visit_duration'] }})</small>@endif</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Activities -->
                                                @if(isset($day['activities']) && count($day['activities']) > 0)
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold small text-uppercase text-danger mb-2">Activities:</h6>
                                                        @foreach($day['activities'] as $act)
                                                            <div class="d-flex mb-2">
                                                                <i class="bi bi-camera-fill text-danger me-2 mt-1"></i>
                                                                <div>
                                                                    <strong>{{ $act['attraction_name'] ?? $act['name'] ?? 'Activity' }}</strong>
                                                                    @if(isset($act['time'])) <small class="text-muted text-uppercase ms-1">{{ $act['time'] }}</small>@endif
                                                                    @if(isset($act['description'])) <p class="small text-muted mb-0">{{ $act['description'] }}</p>@endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="row g-3">
                                                    <!-- Accommodation -->
                                                    @if(isset($day['hotel']))
                                                        <div class="col-md-6">
                                                            <div class="p-2 border rounded bg-light">
                                                                <h6 class="small fw-bold mb-1"><i class="bi bi-building me-1"></i> Stay</h6>
                                                                <div class="small">{{ $day['hotel']['name'] ?? 'Selected Hotel' }}</div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Meals -->
                                                    @if(isset($day['meals']))
                                                        <div class="col-md-6">
                                                            <div class="p-2 border rounded bg-light">
                                                                <h6 class="small fw-bold mb-1"><i class="bi bi-cup-hot me-1"></i> Meals</h6>
                                                                <div class="small">
                                                                    @php $m = []; 
                                                                        if(($day['meals']['breakfast'] ?? '') == 'Included' || ($day['meals']['breakfast'] ?? '') != '') $m[] = 'Breakfast';
                                                                        if(($day['meals']['lunch'] ?? '') == 'Included' || ($day['meals']['lunch'] ?? '') != '') $m[] = 'Lunch';
                                                                        if(($day['meals']['dinner'] ?? '') == 'Included' || ($day['meals']['dinner'] ?? '') != '') $m[] = 'Dinner';
                                                                    @endphp
                                                                    {{ count($m) > 0 ? implode(', ', $m) : 'Not Specified' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if(isset($day['notes']) && $day['notes'])
                                                    <div class="mt-3 p-2 border-start border-4 border-info bg-info bg-opacity-10 small">
                                                        <i class="bi bi-info-circle me-1 text-info"></i> {{ $day['notes'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Related Packages -->
                @if($relatedPackages->count() > 0)
                    <div class="mb-4">
                        <h2 class="section-title">Related Packages</h2>
                        <div class="row">
                            @foreach($relatedPackages as $related)
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('packages.show', $related->slug) }}" class="text-decoration-none">
                                        <div class="related-package">
                                            <img src="{{ getImageUrl($related->image) }}" alt="{{ $related->name }}">
                                            <div class="favorite-icon">
                                                <i class="bi bi-heart"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Booking Card -->
            <div class="col-lg-4">
                <div class="booking-card">
                    <h3 class="section-title mb-3">Standard Package</h3>
                    <p class="mb-3">Fits {{ $package->min_persons ?? 1 }}-{{ $package->max_persons ?? 2 }} Adults</p>

                    <div class="free-cancellation">
                        <input type="checkbox" id="free-cancellation" checked disabled>
                        <label for="free-cancellation">Free Cancellation before travel date</label>
                    </div>

                    <div class="mb-3">
                        @if($package->discount_price)
                            <div class="price-strikethrough" data-price="{{ $package->discount_price }}"
                                data-currency="{{ $package->currency ?? 'INR' }}">
                                {{ \App\Helpers\CurrencyHelper::format($package->discount_price, $package->currency ?? 'MYR') }}
                            </div>
                        @endif
                        <div class="price-current" data-price="{{ $package->price }}"
                            data-currency="{{ $package->currency ?? 'INR' }}">
                            {{ \App\Helpers\CurrencyHelper::format($package->price, $package->currency ?? 'MYR') }}
                        </div>
                        <div class="price-label">Per Person:</div>
                        <div class="price-label mt-1">+ Taxes & fees included</div>
                    </div>

                    @auth
                        <a href="{{ route('book.package.show', $package->id) }}" class="btn btn-book text-white">
                            <i class="bi bi-book"></i> BOOK THIS NOW
                        </a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('book.package.show', $package->id)) }}"
                            class="btn btn-book text-white">
                            <i class="bi bi-book"></i> BOOK THIS NOW
                        </a>
                    @endauth

                    <a href="https://wa.me/?text={{ urlencode('I am interested in booking: ' . $package->name) }}"
                        target="_blank" class="btn btn-whatsapp text-white">
                        <i class="bi bi-whatsapp"></i> Book via WhatsApp
                    </a>

                    <button class="btn btn-quote" onclick="getQuote()">
                        Get a Quote
                    </button>

                    <p class="text-muted small mt-3">More options available with Free Cancellation</p>

                    <div class="mt-4">
                        <h4 class="section-title" style="font-size: 18px;">No ratings yet</h4>
                        <div class="location-info">
                            <i class="bi bi-geo-alt"></i> {{ $package->destination->name ?? 'Location not specified' }}
                        </div>
                        <p class="location-info mt-2">1 km drive to nearest attraction</p>
                        <a href="#" class="text-decoration-none">See on Map</a>
                    </div>

                    @guest
                        <div class="login-prompt">
                            <p class="mb-2">Login to unlock deals & manage your bookings!</p>
                            <a href="{{ route('login') }}" class="btn btn-book text-white">
                                LOGIN NOW
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="gallery-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @if($package->image)
                                <div class="carousel-item active">
                                    <img src="{{ getImageUrl($package->image) }}" class="d-block w-100" alt="Main">
                                </div>
                            @endif
                            @if($package->gallery)
                                @foreach($package->gallery as $img)
                                    <div class="carousel-item">
                                        <img src="{{ getImageUrl($img) }}" class="d-block w-100" alt="Gallery">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#gallery-carousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#gallery-carousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Get a Quote Modal -->
    <div class="modal fade" id="getQuoteModal" tabindex="-1" aria-labelledby="getQuoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold" id="getQuoteModalLabel">
                        <i class="bi bi-chat-dots-fill text-primary me-2"></i> Get a Personal Quote
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quoteForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="quote_name" class="form-label small fw-bold text-muted">Full Name *</label>
                            <input type="text" class="form-control rounded-3" id="quote_name" name="name" required placeholder="John Doe">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="quote_email" class="form-label small fw-bold text-muted">Email Address *</label>
                                <input type="email" class="form-control rounded-3" id="quote_email" name="email" required placeholder="john@example.com">
                            </div>
                            <div class="col-md-6">
                                <label for="quote_phone" class="form-label small fw-bold text-muted">Phone Number *</label>
                                <input type="tel" class="form-control rounded-3" id="quote_phone" name="phone" required placeholder="+1 234 567 890">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="quote_travel_date" class="form-label small fw-bold text-muted">Travel Date *</label>
                                <input type="date" class="form-control rounded-3" id="quote_travel_date" name="travel_date" required min="{{ date('Y-m-d', strtotime('+3 days')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="quote_adults" class="form-label small fw-bold text-muted">Adults *</label>
                                <input type="number" class="form-control rounded-3" id="quote_adults" name="adults" min="1" value="1" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="quote_message" class="form-label small fw-bold text-muted">Message (Optional)</label>
                            <textarea class="form-control rounded-3" id="quote_message" name="message" rows="3" placeholder="Any special requirements?"></textarea>
                        </div>
                        <div id="quoteResponse" class="mt-3" style="display: none;"></div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="submitQuoteBtn">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeMainImage(src, element) {
            document.getElementById('main-image').src = src;
            document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
            element.classList.add('active');
        }

        function openGallery() {
            const modal = new bootstrap.Modal(document.getElementById('galleryModal'));
            modal.show();
        }

        function getQuote() {
            const modal = new bootstrap.Modal(document.getElementById('getQuoteModal'));
            modal.show();
        }

        $(document).ready(function() {
            $('#quoteForm').on('submit', function(e) {
                e.preventDefault();
                
                const $btn = $('#submitQuoteBtn');
                const originalText = $btn.html();
                const $response = $('#quoteResponse');
                
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
                $response.hide().removeClass('alert alert-success alert-danger');

                $.ajax({
                    url: '{{ route("bookings.get-quote", $package->slug) }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            $response.addClass('alert alert-success').html(data.message).fadeIn();
                            $('#quoteForm')[0].reset();
                            setTimeout(() => {
                                bootstrap.Modal.getInstance(document.getElementById('getQuoteModal')).hide();
                                $response.hide();
                            }, 5000);
                        } else {
                            $response.addClass('alert alert-danger').html(data.message || 'Something went wrong.').fadeIn();
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        let errorMsg = 'An error occurred. Please try again.';
                        if (errors) {
                            errorMsg = Object.values(errors).flat().join('<br>');
                        }
                        $response.addClass('alert alert-danger').html(errorMsg).fadeIn();
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
</body>

</html>