@extends('layouts.admin')

@section('title', $itinerary->title . ' - Fixed Itinerary')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary fs-6">{{ $itinerary->duration }}</span>
            <span class="badge bg-{{ $itinerary->status === 'active' ? 'success' : 'secondary' }} fs-6">
                {{ ucfirst($itinerary->status) }}
            </span>
            @if($itinerary->destination)
                <span class="badge bg-info text-white fs-6">
                    <i class="bi bi-geo-alt me-1"></i>{{ $itinerary->destination->name }}
                </span>
            @endif
        </div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-0">{{ $itinerary->title }}</h2>
        <p class="text-muted small mb-0">
            Suppliers: 
            @if($itinerary->suppliers->count() > 0)
                {{ $itinerary->suppliers->map(function($s) { return $s->name . ' (' . ($s->category_label ?? 'Vendor') . ')'; })->join(', ') }}
            @else
                {{ $itinerary->supplier->name ?? 'Direct / In-House' }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fixed-itineraries.edit', $itinerary->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form action="{{ route('admin.fixed-itineraries.duplicate', $itinerary->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-copy me-1"></i> Duplicate &amp; Customize
            </button>
        </form>
        <a href="{{ route('admin.fixed-itineraries.pdf', $itinerary->id) }}" target="_blank" class="btn btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> Printable PDF
        </a>
        <a href="{{ route('admin.fixed-itineraries.whatsapp', $itinerary->id) }}" class="btn btn-outline-success">
            <i class="bi bi-whatsapp me-1"></i> WhatsApp
        </a>
        <a href="{{ route('admin.fixed-itineraries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Left: Overview & Day by Day -->
    <div class="col-lg-8">
        @if($itinerary->image)
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <img src="{{ asset('storage/' . $itinerary->image) }}" class="img-fluid w-100" style="max-height: 380px; object-fit: cover;" alt="{{ $itinerary->title }}">
            </div>
        @endif

        @if($itinerary->short_description || $itinerary->itinerary_description)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-card-text text-primary me-2"></i>Package Overview</h5>
                    @if($itinerary->short_description)
                        <p class="lead fs-6 text-dark font-italic mb-3">{{ $itinerary->short_description }}</p>
                    @endif
                    @if($itinerary->itinerary_description)
                        <div class="text-muted" style="line-height: 1.8;">
                            {!! nl2br(e($itinerary->itinerary_description)) !!}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Day by Day Schedule -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-calendar3 me-2"></i>Day-by-Day Schedule
                </h5>
                <span class="badge bg-light text-dark border">
                    {{ is_array($itinerary->days) ? count($itinerary->days) : 0 }} Days Total
                </span>
            </div>
            <div class="card-body p-4">
                @if(is_array($itinerary->days) && count($itinerary->days) > 0)
                    <div class="timeline ps-2">
                        @foreach($itinerary->days as $index => $rawDay)
                            @php
                                $day = (array) $rawDay;
                                $dayNum = $day['day_number'] ?? ($index + 1);
                                $dayItems = is_array($day['items'] ?? null) ? $day['items'] : [];
                            @endphp
                            <div class="border-start border-3 border-primary ps-3 pb-4 mb-3 position-relative">
                                <div class="d-flex align-items-center mb-1 flex-wrap gap-2">
                                    <span class="badge bg-primary px-2 py-1">Day {{ $dayNum }}</span>
                                    <h6 class="fw-bold mb-0 text-dark fs-6">{{ $day['title'] ?? 'Day ' . $dayNum }}</h6>
                                    @if(!empty($day['overnight_location']))
                                        <span class="badge bg-light text-secondary border small">
                                            <i class="bi bi-moon-stars me-1"></i>Overnight: {{ $day['overnight_location'] }}
                                        </span>
                                    @endif
                                </div>

                                @if(!empty($day['description']))
                                    <p class="text-muted small mt-2 mb-2" style="line-height: 1.7;">
                                        {!! nl2br(e($day['description'])) !!}
                                    </p>
                                @endif

                                <!-- Operations Inventory Chips -->
                                @if(count($dayItems) > 0)
                                    <div class="d-flex flex-wrap gap-2 my-2 p-2 bg-light rounded border">
                                        @foreach($dayItems as $rawItem)
                                            @php
                                                $item = (array) $rawItem;
                                                $itemType = $item['item_type'] ?? 'activity';
                                                $bClass = 'bg-dark';
                                                $bIcon = 'bi-box';
                                                switch($itemType) {
                                                    case 'hotel': $bClass = 'bg-primary'; $bIcon = 'bi-building'; break;
                                                    case 'transport': $bClass = 'bg-secondary'; $bIcon = 'bi-car-front'; break;
                                                    case 'ticket': $bClass = 'bg-danger'; $bIcon = 'bi-ticket-perforated'; break;
                                                    case 'spot': $bClass = 'bg-info text-white'; $bIcon = 'bi-geo-alt'; break;
                                                    case 'activity': $bClass = 'bg-warning text-dark'; $bIcon = 'bi-lightning'; break;
                                                    case 'meal': $bClass = 'bg-success'; $bIcon = 'bi-cup-hot'; break;
                                                }
                                            @endphp
                                            <span class="badge {{ $bClass }} py-2 px-2 d-inline-flex align-items-center font-monospace" style="font-size: 0.82rem;">
                                                <i class="bi {{ $bIcon }} me-1"></i>
                                                <strong>{{ $item['title'] ?? '' }}</strong>
                                                @if(!empty($item['room_type']))
                                                    <span class="ms-1 opacity-75">({{ $item['room_type'] }})</span>
                                                @endif
                                                @if(!empty($item['price']) && $item['price'] > 0)
                                                    <span class="ms-1 border-start ps-1 opacity-75">{{ $itinerary->currency_symbol }}{{ number_format($item['price'], 2) }}</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if(!empty($day['highlights']) && is_array($day['highlights']))
                                    <div class="small text-warning mt-1">
                                        <i class="bi bi-star-fill me-1"></i><strong>Highlights:</strong> {{ implode(' &bull; ', $day['highlights']) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted fst-italic mb-0">No structured days configured yet. Click 'Edit' to add day-by-day activities and inventory bookings.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Right: Price, Inclusions, Exclusions -->
    <div class="col-lg-4">
        <!-- Price Card -->
        <div class="card mb-4 border-0 shadow-sm bg-gradient text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div class="card-body p-4 text-center">
                <span class="text-white-50 text-uppercase tracking-wider small fw-bold">Fixed Package Price</span>
                <div class="display-5 fw-bold my-2 text-white" id="mainDisplayPrice">
                    {{ $itinerary->currency ?: 'MYR' }} {{ number_format($itinerary->fixed_price, 2) }}
                </div>
                <div class="small text-white-75 mb-3">All-inclusive package price</div>

                <!-- Multi-Currency Quick Switcher -->
                <div class="d-flex justify-content-center gap-1 mb-3">
                    @foreach($itinerary->converted_prices as $c)
                        <button type="button" class="btn btn-sm {{ $c['code'] === ($itinerary->currency ?: 'MYR') ? 'btn-light text-primary fw-bold' : 'btn-outline-light' }} show-currency-btn"
                            data-code="{{ $c['code'] }}" data-symbol="{{ $c['symbol'] }}" data-amount="{{ number_format($c['amount'], 2) }}">
                            {{ $c['code'] }} ({{ $c['symbol'] }})
                        </button>
                    @endforeach
                </div>

                <!-- Multi-Currency Summary -->
                <div class="p-2 rounded bg-white bg-opacity-10 small mb-3">
                    <div class="d-flex justify-content-around text-white-75 flex-wrap gap-2">
                        @foreach($itinerary->converted_prices as $c)
                            <span><strong>{{ $c['code'] }}:</strong> {{ $c['symbol'] }} {{ number_format($c['amount'], 2) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.fixed-itineraries.pdf', $itinerary->id) }}" target="_blank" class="btn btn-light fw-bold text-primary">
                        <i class="bi bi-printer me-2"></i> Print Quotation PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Supplier & Countries Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold py-3">
                <i class="bi bi-info-square text-primary me-2"></i>Vendor & Destinations
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">Operating Suppliers / Vendors</small>
                    @if($itinerary->suppliers->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($itinerary->suppliers as $sup)
                                <div class="badge {{ $sup->category_badge_class }} border px-2 py-1 d-inline-flex align-items-center">
                                    <i class="bi {{ $sup->category_icon }} me-1"></i>
                                    <span>{{ $sup->name }}</span>
                                    <span class="badge bg-white text-dark border ms-1" style="font-size: 0.68rem;">{{ $sup->category_label }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span class="fw-bold">{{ $itinerary->supplier->name ?? 'Direct Inventory' }}</span>
                    @endif
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block">Primary Destination</small>
                    <span class="fw-bold">{{ $itinerary->destination->name ?? 'Multiple Destinations' }}</span>
                </div>
                <div>
                    <small class="text-muted d-block mb-1">Countries Covered</small>
                    @if($itinerary->countries->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($itinerary->countries as $c)
                                <span class="badge bg-light text-dark border">{{ $c->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted small">Not specified</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Highlights & Inclusions -->
        @if(!empty($itinerary->highlights) || !empty($itinerary->inclusions) || !empty($itinerary->exclusions))
            <div class="card mb-4 border-0 shadow-sm">
                @if(!empty($itinerary->highlights))
                    <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                        <i class="bi bi-star-fill text-warning me-2"></i>Package Highlights
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            @foreach($itinerary->highlights as $hl)
                                <li class="mb-1 d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-warning me-2 mt-1"></i>
                                    <span>{{ $hl }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($itinerary->inclusions))
                    <div class="card-header bg-white font-weight-bold py-3 border-top border-bottom">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>Inclusions
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            @foreach($itinerary->inclusions as $inc)
                                <li class="mb-1 d-flex align-items-start">
                                    <i class="bi bi-check-lg text-success me-2 mt-1 fw-bold"></i>
                                    <span>{{ $inc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($itinerary->exclusions))
                    <div class="card-header bg-white font-weight-bold py-3 border-top border-bottom">
                        <i class="bi bi-x-circle-fill text-danger me-2"></i>Exclusions
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            @foreach($itinerary->exclusions as $exc)
                                <li class="mb-1 d-flex align-items-start text-muted">
                                    <i class="bi bi-x-lg text-danger me-2 mt-1 fw-bold"></i>
                                    <span>{{ $exc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @if(!empty($itinerary->terms_conditions))
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                    <i class="bi bi-shield-check text-secondary me-2"></i>Terms & Conditions
                </div>
                <div class="card-body small text-muted">
                    {!! nl2br(e($itinerary->terms_conditions)) !!}
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.show-currency-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.show-currency-btn').forEach(b => {
                b.classList.remove('btn-light', 'text-primary', 'fw-bold');
                b.classList.add('btn-outline-light');
            });
            this.classList.remove('btn-outline-light');
            this.classList.add('btn-light', 'text-primary', 'fw-bold');

            const code = this.getAttribute('data-code');
            const symbol = this.getAttribute('data-symbol');
            const amount = this.getAttribute('data-amount');
            const mainPrice = document.getElementById('mainDisplayPrice');
            if (mainPrice) {
                mainPrice.textContent = `${code} ${amount}`;
            }
        });
    });
</script>
@endpush
@endsection
