<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - {{ $itinerary->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .hero { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; padding: 40px 0; border-radius: 0 0 30px 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: -50px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 30px; overflow: hidden; }
        .card-header-custom { background: white; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; }
        .day-badge { background-color: #e9ecef; color: #495057; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; }
        .timeline-item { border-left: 2px solid #e9ecef; padding-left: 25px; position: relative; margin-bottom: 25px; margin-left: 20px; }
        .timeline-item::before { content: ''; position: absolute; left: -9px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: #0d6efd; border: 3px solid white; }
        .timeline-title { font-weight: bold; font-size: 1.1rem; color: #343a40; margin-bottom: 10px; }
        .price-box { background: #f0fdf4; border: 2px dashed #86efac; border-radius: 15px; padding: 30px; text-align: center; }
        .total-price { font-size: 2.5rem; font-weight: 800; color: #166534; margin: 15px 0; }
        .brand-footer { text-align: center; padding: 40px 0; color: #6c757d; }
    </style>
</head>
<body>

    <div class="hero">
        <div class="container text-center">
            <h5 class="text-white-50 text-uppercase tracking-wider mb-3">Tourliz Holidays - Custom Quotation</h5>
            <h1 class="display-4 fw-bold mb-3">{{ $itinerary->title }}</h1>
            <p class="lead opacity-75">Presented to {{ $itinerary->b2cDetail->customer_name ?? 'Valued Customer' }}</p>
        </div>
    </div>

    <div class="container" style="position: relative; z-index: 10; margin-top: 80px;">
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Overview Card -->
                <div class="card card-custom">
                    <div class="card-body p-4">
                        @if($itinerary->b2cDetail->image)
                            <img src="{{ $itinerary->b2cDetail->image_url }}" class="img-fluid rounded mb-4 w-100" style="max-height: 400px; object-fit: cover;" alt="{{ $itinerary->title }}">
                        @endif
                        
                        <h4 class="fw-bold mb-3">Overview</h4>
                        <p class="text-muted" style="line-height: 1.8;">{{ $itinerary->description }}</p>

                        <div class="row g-4 mt-2">
                            <div class="col-sm-4">
                                <div class="d-flex align-items-center">
                                    <div class="text-primary me-3"><i class="bi bi-clock-history fs-3"></i></div>
                                    <div>
                                        <div class="text-muted small">Duration</div>
                                        <div class="fw-bold">{{ $itinerary->duration_days }} Days, {{ $itinerary->duration_nights }} Nights</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="d-flex align-items-center">
                                    <div class="text-primary me-3"><i class="bi bi-geo-alt fs-3"></i></div>
                                    <div>
                                        <div class="text-muted small">Destination</div>
                                        <div class="fw-bold">{{ $itinerary->destination->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="d-flex align-items-center">
                                    <div class="text-primary me-3"><i class="bi bi-people fs-3"></i></div>
                                    <div>
                                        <div class="text-muted small">Travelers</div>
                                        <div class="fw-bold">{{ $itinerary->b2cDetail->adults ?? 2 }} Adults, {{ $itinerary->b2cDetail->children ?? 0 }} Children</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Itinerary Card -->
                @if($itinerary->days->count() > 0)
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <h4 class="fw-bold mb-0">Daily Itinerary</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="mt-3">
                            @foreach($itinerary->days->sortBy('day_number') as $day)
                                <div class="timeline-item">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-primary me-2">Day {{ $day->day_number }}</span>
                                        <div class="timeline-title mb-0">{{ $day->title }}</div>
                                    </div>
                                    <p class="text-muted mt-2" style="line-height: 1.7;">{!! nl2br(e($day->description)) !!}</p>
                                    @if($day->overnight_location)
                                        <div class="text-primary small mt-2"><i class="bi bi-moon-stars me-1"></i> Overnight: {{ $day->overnight_location }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                
                <!-- Price Card -->
                <div class="price-box mb-4">
                    <div class="text-uppercase text-muted fw-bold tracking-wider mb-2">Total Package Price</div>
                    <div class="total-price">RM {{ number_format($itinerary->b2cDetail->selling_price ?? 0, 2) }}</div>
                    
                    @if($itinerary->b2cDetail->travel_date_start)
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <span class="text-muted">Travel Date:</span>
                        <span class="fw-bold">
                            {{ \Carbon\Carbon::parse($itinerary->b2cDetail->travel_date_start)->format('d M Y') }}
                        </span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <span class="text-muted">Quotation Ref:</span>
                        <span class="fw-bold">{{ $itinerary->b2cDetail->quotation_reference ?? 'N/A' }}</span>
                    </div>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', env('COMPANY_WHATSAPP', '60123456789')) }}?text={{ urlencode('Hi Tourliz, I want to proceed with my quotation for ' . $itinerary->title) }}" class="btn btn-success btn-lg w-100 mt-4 rounded-pill fw-bold">
                        <i class="bi bi-whatsapp me-2"></i> Book via WhatsApp
                    </a>
                </div>

                <!-- Details Card -->
                <div class="card card-custom">
                    <div class="card-header-custom bg-light">
                        <h5 class="fw-bold mb-0">Package Inclusions</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($itinerary->b2cDetail->inclusions)
                            <div class="text-muted">
                                {!! nl2br(e($itinerary->b2cDetail->inclusions)) !!}
                            </div>
                        @else
                            <p class="text-muted fst-italic">As per detailed itinerary.</p>
                        @endif
                    </div>
                    
                    <div class="card-header-custom bg-light border-top">
                        <h5 class="fw-bold mb-0">Exclusions</h5>
                    </div>
                    <div class="card-body p-4">
                        @if($itinerary->b2cDetail->exclusions)
                            <div class="text-muted">
                                {!! nl2br(e($itinerary->b2cDetail->exclusions)) !!}
                            </div>
                        @else
                            <p class="text-muted fst-italic">Standard exclusions apply.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="brand-footer">
        <p class="mb-1">&copy; {{ date('Y') }} Tourliz Holidays. All Rights Reserved.</p>
        <p class="small text-muted">This is an automated quotation. Prices and availability are subject to change until confirmed.</p>
    </div>

</body>
</html>
