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
        .gallery-grid .main-image-container img {
            border-radius: 8px 0 0 8px;
        }
        .gallery-grid .col-md-4 .row .col-6:nth-child(2) .thumbnail {
            border-radius: 0 8px 0 0;
        }
        .gallery-grid .col-md-4 .row .col-6:last-child .thumbnail {
            border-radius: 0 0 8px 0;
        }
        .thumbnail-item {
            overflow: hidden;
            border-radius: 4px;
        }
        .thumbnail-item img {
            transition: transform 0.3s;
            cursor: pointer;
        }
        .thumbnail-item img:hover {
            transform: scale(1.05);
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            color: #fff;
            border-radius: 0 0 8px 0;
            cursor: pointer;
            transition: background 0.3s;
        }
        .gallery-overlay:hover {
            background: rgba(0,0,0,0.6);
        }
        .timeline-container {
            position: relative;
            padding-left: 20px;
        }
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 39px;
            top: 20px;
            bottom: 20px;
            width: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        .timeline-day-marker {
            position: relative;
            z-index: 1;
            width: 40px;
            height: 40px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 0 0 5px #fff;
        }
        .itinerary-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #f0f0f0;
        }
        .itinerary-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
        }
        /* Dynamic OTA Customizer Styles */
        .transport-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            background: #fff;
        }
        .transport-card:hover {
            border-color: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.08);
        }
        .btn-check:checked + .transport-card {
            background-color: #fffafb;
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
        }
        .btn-check:checked + .transport-card .transport-icon {
            color: #dc3545 !important;
        }
        .btn-check:checked + .transport-card .transport-name {
            color: #dc3545 !important;
        }
        .form-select-custom {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }
        .form-select-custom:focus {
            border-color: #dc3545;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
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
                <!-- Premium Gallery Grid -->
                <div class="row g-2 mb-4 gallery-grid">
                    <div class="col-md-8">
                        <div class="main-image-container h-100 mb-0">
                            <img id="main-image" src="{{ getImageUrl($package->image) }}" alt="{{ $package->name }}" class="main-image h-100" style="min-height: 400px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row g-2 h-100">
                            @php
                                $galleryItems = array_slice($package->gallery ?? [], 0, 4);
                            @endphp
                            @foreach($galleryItems as $index => $img)
                                <div class="col-6">
                                    <div class="thumbnail-item h-100 position-relative">
                                        <img src="{{ getImageUrl($img) }}" alt="Gallery" class="thumbnail w-100 h-100" style="object-fit: cover; min-height: 196px;" onclick="openGallery({{ $index + 1 }})">
                                        @if($index === 3 && count($package->gallery ?? []) > 4)
                                            <div class="gallery-overlay d-flex align-items-center justify-content-center" onclick="openGallery(4)">
                                                <span class="text-white fw-bold">+{{ count($package->gallery) - 4 }} Photos</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if(count($galleryItems) < 4)
                                @for($i=0; $i < (4 - count($galleryItems)); $i++)
                                    <div class="col-6">
                                        <div class="thumbnail-item h-100 bg-light d-flex align-items-center justify-content-center border rounded" style="min-height: 196px;">
                                            <i class="bi bi-image text-muted fs-2"></i>
                                        </div>
                                    </div>
                                @endfor
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
                        <div class="timeline-container">
                            @foreach($package->formatted_itinerary as $index => $day)
                                <div class="d-flex mb-4">
                                    <div class="timeline-day-marker me-4">
                                        {{ $day['day'] }}
                                    </div>
                                    <div class="itinerary-card card border-0 shadow-sm rounded-4 flex-grow-1 overflow-hidden">
                                        <div class="card-header bg-white border-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="text-muted small text-uppercase fw-bold">Day {{ $day['day'] }}</span>
                                                    <h5 class="fw-bold mb-0 text-dark">{{ $day['title'] }}</h5>
                                                </div>
                                                <i class="bi bi-chevron-down text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="ps-1 py-2">
                                                <!-- Places/Attractions -->
                                                @if(isset($day['places']) && count($day['places']) > 0)
                                                    <div class="mb-3">
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($day['places'] as $p)
                                                                <span class="badge bg-light text-dark border py-2 px-3 rounded-pill">
                                                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                                                    {{ $p['place_name'] ?? $p['name'] ?? 'Attraction' }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Tourist Spots -->
                                                @if(isset($day['spots']) && count($day['spots']) > 0)
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold small text-uppercase text-danger mb-2">Tourist Spots:</h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($day['spots'] as $spot)
                                                                <span class="badge bg-light text-dark border py-2 px-3 rounded-pill" title="{{ $spot['description'] ?? '' }}">
                                                                    <i class="bi bi-geo-alt-fill text-info me-1"></i>
                                                                    <strong>{{ $spot['name'] ?? 'Spot' }}</strong>
                                                                    @if(!empty($spot['hours']))
                                                                        <span class="text-muted small">({{ $spot['hours'] }}h)</span>
                                                                    @endif
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Activities -->
                                                @if(isset($day['activities']) && count($day['activities']) > 0)
                                                    <div class="mb-3">
                                                        <h6 class="fw-bold small text-uppercase text-danger mb-2">Activities & Highlights:</h6>
                                                        @foreach($day['activities'] as $act)
                                                            <div class="d-flex mb-2">
                                                                <div class="bg-danger-subtle rounded-circle p-1 me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="bi bi-camera-fill text-danger small"></i>
                                                                </div>
                                                                <div>
                                                                    <strong class="small">{{ $act['attraction_name'] ?? $act['name'] ?? 'Activity' }}</strong>
                                                                    @if(isset($act['description'])) <p class="extra-small text-muted mb-0">{{ $act['description'] }}</p>@endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="row g-3">
                                                    <!-- Accommodation -->
                                                    @if(isset($day['hotel']) && !empty($day['hotel']['name']))
                                                        <div class="col-md-6">
                                                            <div class="p-3 border rounded-4 bg-light h-100">
                                                                <h6 class="small fw-bold mb-2"><i class="bi bi-building me-2 text-primary"></i> Accommodation</h6>
                                                                <div class="small text-dark fw-medium">{{ $day['hotel']['name'] }}</div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Meals -->
                                                    @php $m = []; 
                                                        if(($day['meals']['breakfast'] ?? '') == 'Included' || ($day['meals']['breakfast'] ?? '') != '') $m[] = 'Breakfast';
                                                        if(($day['meals']['lunch'] ?? '') == 'Included' || ($day['meals']['lunch'] ?? '') != '') $m[] = 'Lunch';
                                                        if(($day['meals']['dinner'] ?? '') == 'Included' || ($day['meals']['dinner'] ?? '') != '') $m[] = 'Dinner';
                                                    @endphp
                                                    @if(count($m) > 0)
                                                        <div class="col-md-6">
                                                            <div class="p-3 border rounded-4 bg-light h-100">
                                                                <h6 class="small fw-bold mb-2"><i class="bi bi-cup-hot me-2 text-warning"></i> Meals Included</h6>
                                                                <div class="small text-dark fw-medium">{{ implode(', ', $m) }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if(isset($day['notes']) && $day['notes'])
                                                    <div class="mt-3 p-3 rounded-4 bg-info bg-opacity-10 border-start border-4 border-info">
                                                        <div class="small text-dark"><i class="bi bi-info-circle-fill me-2 text-info"></i>{{ $day['notes'] }}</div>
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
                <div class="booking-card shadow-sm border rounded-4 p-4">
                    <div class="mb-4">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Adults</label>
                                <select id="booking_adults" class="form-select form-select-custom form-select-sm" onchange="updateBookingPricing()">
                                    @php
                                        $minPax = $package->min_pax ?? 1;
                                        $maxPax = $package->max_pax ?? 20;
                                        $defaultAdults = max(2, $minPax);
                                        if ($defaultAdults > $maxPax) $defaultAdults = $maxPax;
                                    @endphp
                                    @for($i=$minPax; $i<=$maxPax; $i++)
                                        <option value="{{ $i }}" {{ $i == $defaultAdults ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Adult' : 'Adults' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Kids (2-12)</label>
                                <select id="booking_children" class="form-select form-select-custom form-select-sm" onchange="updateBookingPricing()">
                                    @for($i=0; $i<=10; $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Kid' : 'Kids' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        @php
                            $associatedHotels = $package->associated_hotels;
                        @endphp

                        @if($associatedHotels && $associatedHotels->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 text-muted">
                                    <i class="bi bi-building text-danger me-1"></i>Select Hotel & Stay
                                </label>
                                <select id="booking_hotel_id" class="form-select form-select-custom form-select-sm mb-2" onchange="filterRoomsByHotel()">
                                    @foreach($associatedHotels as $hotel)
                                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                    @endforeach
                                </select>
                                
                                <label class="form-label small fw-bold mb-1 text-muted">
                                    <i class="bi bi-door-open text-danger me-1"></i>Select Room Type
                                </label>
                                <select id="booking_room_id" class="form-select form-select-custom form-select-sm" onchange="updateBookingPricing()">
                                    <!-- Populated dynamically by JS -->
                                </select>
                            </div>
                        @else
                            <label class="form-label small fw-bold mb-2 text-muted">Room Configuration</label>
                            <div class="room-options mb-3">
                                <div class="form-check small mb-1">
                                    <input class="form-check-input room-config" type="radio" name="room_config" id="config_double" value="double" checked onchange="updateBookingPricing()">
                                    <label class="form-check-label text-dark fw-medium" for="config_double">Double/Twin Sharing</label>
                                </div>
                                <div class="form-check small mb-1" id="triple_option_container">
                                    <input class="form-check-input room-config" type="radio" name="room_config" id="config_triple" value="triple" onchange="updateBookingPricing()">
                                    <label class="form-check-label text-dark fw-medium" for="config_triple">Triple Sharing (Extra Bed)</label>
                                </div>
                                <div class="form-check small mb-1" id="quad_option_container">
                                    <input class="form-check-input room-config" type="radio" name="room_config" id="config_quad" value="quad" onchange="updateBookingPricing()">
                                    <label class="form-check-label text-dark fw-medium" for="config_quad">Quad/Family Sharing</label>
                                </div>
                                <div class="form-check small">
                                    <input class="form-check-input room-config" type="radio" name="room_config" id="config_single" value="single" onchange="updateBookingPricing()">
                                    <label class="form-check-label text-dark fw-medium" for="config_single">Single Occupancy</label>
                                </div>
                            </div>
                        @endif

                        <!-- Transport Selection -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-2 text-muted">
                                <i class="bi bi-car-front-fill text-danger me-1"></i>Select Transport (Private Transfer)
                            </label>
                            <div class="transport-options">
                                <!-- Sedan Option -->
                                <div class="position-relative mb-2">
                                    <input type="radio" class="btn-check" name="transport_type" id="transport_sedan" value="sedan" checked autocomplete="off" onchange="userSelectedTransportOption('sedan')">
                                    <label class="transport-card p-3 w-100 d-flex align-items-center justify-content-between" for="transport_sedan">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-car-front fs-4 me-3 text-secondary transport-icon"></i>
                                            <div>
                                                <div class="fw-bold text-dark transport-name">Sedan Vehicle</div>
                                                <div class="text-muted extra-small">1-4 Pax Capacity • Recommended</div>
                                            </div>
                                        </div>
                                        <span class="badge bg-success-subtle text-success py-2 px-3 rounded-pill fw-bold">Included</span>
                                    </label>
                                </div>
                                
                                <!-- SUV Option -->
                                <div class="position-relative mb-2">
                                    <input type="radio" class="btn-check" name="transport_type" id="transport_suv" value="suv" autocomplete="off" onchange="userSelectedTransportOption('suv')">
                                    <label class="transport-card p-3 w-100 d-flex align-items-center justify-content-between" for="transport_suv">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-truck-flatbed fs-4 me-3 text-secondary transport-icon"></i>
                                            <div>
                                                <div class="fw-bold text-dark transport-name">SUV (MPV) Vehicle</div>
                                                <div class="text-muted extra-small">4-6 Pax Capacity • Comfort</div>
                                            </div>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger py-2 px-3 rounded-pill fw-bold">+{{ $package->currency ?? 'MYR' }} 150</span>
                                    </label>
                                </div>
                                
                                <!-- Van Option -->
                                <div class="position-relative">
                                    <input type="radio" class="btn-check" name="transport_type" id="transport_van" value="van" autocomplete="off" onchange="userSelectedTransportOption('van')">
                                    <label class="transport-card p-3 w-100 d-flex align-items-center justify-content-between" for="transport_van">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-bus-front-fill fs-4 me-3 text-secondary transport-icon"></i>
                                            <div>
                                                <div class="fw-bold text-dark transport-name">10-Seater Van</div>
                                                <div class="text-muted extra-small">6-8 Pax Capacity • Group</div>
                                            </div>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger py-2 px-3 rounded-pill fw-bold">+{{ $package->currency ?? 'MYR' }} 300</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-summary bg-light p-3 rounded-3 mb-3 border-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-medium">Per Person:</span>
                            <span class="h3 mb-0 text-danger fw-bold" id="display_per_pax">
                                {{ \App\Helpers\CurrencyHelper::format($package->price, $package->currency ?? 'MYR') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="fw-bold text-muted small">Total Price:</span>
                            <span class="h5 mb-0 text-dark fw-bold" id="display_total">
                                {{ \App\Helpers\CurrencyHelper::format($package->price * 2, $package->currency ?? 'MYR') }}
                            </span>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('book.package.show', $package->id) }}" class="btn btn-book text-white py-3 rounded-3 mb-2 shadow-sm">
                            <i class="bi bi-book-fill me-2"></i>BOOK THIS NOW
                        </a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('book.package.show', $package->id)) }}"
                            class="btn btn-book text-white py-3 rounded-3 mb-2 shadow-sm">
                            <i class="bi bi-book-fill me-2"></i>BOOK THIS NOW
                        </a>
                    @endauth

                    <a href="https://wa.me/?text={{ urlencode('I am interested in booking: ' . $package->name) }}"
                        target="_blank" class="btn btn-whatsapp text-white py-3 rounded-3 mb-2 shadow-sm">
                        <i class="bi bi-whatsapp me-2"></i>Book via WhatsApp
                    </a>

                    <button class="btn btn-quote py-3 rounded-3 mb-3" onclick="getQuote()">
                        Get a Quote
                    </button>

                    <p class="text-muted small text-center mb-4"><i class="bi bi-shield-check text-success me-1"></i>Free Cancellation Options Available</p>

                    <div class="mt-2 border-top pt-3">
                        <h5 class="section-title" style="font-size: 16px;"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $package->destination->name ?? 'Location not specified' }}</h5>
                        <p class="location-info mt-1 mb-2 text-muted small"><i class="bi bi-info-circle me-1"></i>1 km drive to nearest key attraction</p>
                        <a href="#" class="text-decoration-none small fw-bold">See Interactive Map</a>
                    </div>

                    @guest
                        <div class="login-prompt mt-4 border rounded-3 p-3 bg-light">
                            <p class="mb-2 text-muted small fw-medium">Login to unlock secret deals & manage your trips!</p>
                            <a href="{{ route('login') }}" class="btn btn-danger btn-sm text-white w-100 py-2 rounded-2">
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

        const hotelRooms = {
            @if($associatedHotels && $associatedHotels->isNotEmpty())
                @foreach($associatedHotels as $hotel)
                    "{{ $hotel->id }}": [
                        @foreach($hotel->rooms as $room)
                            {
                                id: "{{ $room->id }}",
                                type: "{{ $room->room_type }}",
                                capacity: {{ $room->capacity }},
                                price: {{ $room->base_price }}
                            },
                        @endforeach
                    ],
                @endforeach
            @endif
        };

        function filterRoomsByHotel() {
            const hotelId = $('#booking_hotel_id').val();
            const rooms = hotelRooms[hotelId] || [];
            const $roomSelect = $('#booking_room_id');
            $roomSelect.empty();
            
            rooms.forEach(room => {
                $roomSelect.append(`<option value="${room.id}">${room.type} (${room.capacity} Pax)</option>`);
            });
            
            updateBookingPricing();
        }

        let userManuallySelectedTransport = false;

        function userSelectedTransportOption(option) {
            userManuallySelectedTransport = true;
            updateBookingPricing();
        }

        function updateBookingPricing() {
            const adults = parseInt($('#booking_adults').val()) || 1;
            const children = parseInt($('#booking_children').val()) || 0;
            const roomId = $('#booking_room_id').val() || null;
            
            // Auto-select room configuration based on number of adults (only if room select is not present)
            if (!$('#booking_room_id').length) {
                if (adults === 1) {
                    $('#config_single').prop('checked', true);
                } else if (adults === 2) {
                    $('#config_double').prop('checked', true);
                } else if (adults === 3) {
                    $('#config_triple').prop('checked', true);
                } else if (adults >= 4) {
                    $('#config_quad').prop('checked', true);
                }
            }

            const roomConfig = $('input[name="room_config"]:checked').val() || 'double';
            
            // Toggle room options based on pax count (only if room select is not present)
            if (!$('#booking_room_id').length) {
                if (adults < 3) {
                    $('#triple_option_container').addClass('opacity-50');
                    if (roomConfig === 'triple') $('#config_double').prop('checked', true);
                } else {
                    $('#triple_option_container').removeClass('opacity-50');
                }
                
                if (adults < 4) {
                    $('#quad_option_container').addClass('opacity-50');
                    if (roomConfig === 'quad') $('#config_double').prop('checked', true);
                } else {
                    $('#quad_option_container').removeClass('opacity-50');
                }
            }

            // Auto-recommend vehicle based on guest capacity
            const totalGuests = adults + children;
            if (!userManuallySelectedTransport) {
                if (totalGuests <= 4) {
                    $('#transport_sedan').prop('checked', true);
                } else if (totalGuests <= 6) {
                    $('#transport_suv').prop('checked', true);
                } else {
                    $('#transport_van').prop('checked', true);
                }
            }

            const transportType = $('input[name="transport_type"]:checked').val() || 'sedan';

            // Build the query string for checkout redirect
            let queryParams = `?adults=${adults}&children=${children}`;
            if (roomId) queryParams += `&room_id=${roomId}`;
            queryParams += `&transport_type=${transportType}`;

            // Update the BOOK THIS NOW buttons
            $('.btn-book').each(function() {
                let baseHref = $(this).data('base-href') || $(this).attr('href').split('?')[0];
                $(this).data('base-href', baseHref); // store it
                
                // If it's a login redirect, wrap it correctly
                if (baseHref.includes('login')) {
                    let dest = '{{ route("book.package.show", $package->id) }}' + queryParams;
                    $(this).attr('href', '{{ route("login") }}?redirect=' + encodeURIComponent(dest));
                } else {
                    $(this).attr('href', baseHref + queryParams);
                }
            });

            // Update WhatsApp link with dynamic details
            const selectedRoomText = $('#booking_room_id option:selected').text().trim() || ($('#booking_room_id').length ? 'Selected Room' : roomConfig.toUpperCase() + ' Sharing');
            const whatsappText = `I am interested in booking: {{ $package->name }} for ${adults} Adults, ${children} Kids.\nStay Room: ${selectedRoomText}.\nTransport: ${transportType.toUpperCase()} vehicle.`;
            $('.btn-whatsapp').attr('href', `https://wa.me/?text=${encodeURIComponent(whatsappText)}`);

            // AJAX call to calculate price
            $.ajax({
                url: '{{ route("packages.calculate-price", $package->slug) }}',
                method: 'POST',
                data: {
                    adults: adults,
                    children: children,
                    room_config: roomConfig,
                    room_id: roomId,
                    transport_type: transportType,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#display_per_pax').text(response.per_pax);
                        $('#display_total').text(response.total);
                    }
                },
                error: function() {
                    console.error('Failed to update pricing');
                }
            });
        }

        $(document).ready(function() {
            // Initial price calculation
            updateBookingPricing();
            
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