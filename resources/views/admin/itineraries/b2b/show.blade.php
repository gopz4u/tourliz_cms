@extends('layouts.admin')

@section('title', $itinerary->title . ' - B2B Proposal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary fs-6">{{ $itinerary->duration_days }}D / {{ $itinerary->duration_nights }}N</span>
            <span class="badge bg-{{ $itinerary->status === 'published' ? 'success' : 'secondary' }} fs-6">
                {{ ucfirst($itinerary->status) }}
            </span>
            @if($itinerary->destination)
                <span class="badge bg-info text-white fs-6">
                    <i class="bi bi-geo-alt me-1"></i>{{ $itinerary->destination->name }}
                </span>
            @endif
        </div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-0">{{ $itinerary->title }}</h2>
        <p class="text-muted small mb-0">Partner Agency: {{ $itinerary->b2bDetail->agency->company_name ?? 'Direct B2B' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.itineraries.b2b.edit', $itinerary->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.itineraries.b2b.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Left: Overview & Day by Day -->
    <div class="col-lg-8">
        @if(!empty($itinerary->description))
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-card-text text-primary me-2"></i>Proposal Overview</h5>
                    <div class="text-muted" style="line-height: 1.8;">
                        {!! nl2br(e($itinerary->description)) !!}
                    </div>
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
                    {{ $itinerary->days->count() }} Days Total
                </span>
            </div>
            <div class="card-body p-4">
                @if($itinerary->days->count() > 0)
                    <div class="timeline ps-2">
                        @foreach($itinerary->days->sortBy('day_number') as $day)
                            <div class="mb-4 pb-3 border-bottom last-border-0">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">Day {{ $day->day_number }}</span>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $day->title ?: 'Day ' . $day->day_number }}</h6>
                                </div>

                                @if(!empty($day->description))
                                    <p class="text-muted small mb-3 ps-2">{{ $day->description }}</p>
                                @endif

                                @if($day->items && $day->items->count() > 0)
                                    <div class="ms-2 mb-3">
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($day->items as $item)
                                                @php
                                                    $badgeBg = 'bg-secondary';
                                                    $icon = 'bi-box';
                                                    switch($item->item_type) {
                                                        case 'hotel': $badgeBg = 'bg-primary'; $icon = 'bi-building'; break;
                                                        case 'transport': $badgeBg = 'bg-secondary'; $icon = 'bi-car-front'; break;
                                                        case 'ticket': $badgeBg = 'bg-danger'; $icon = 'bi-ticket-perforated'; break;
                                                        case 'spot': $badgeBg = 'bg-info text-white'; $icon = 'bi-geo-alt'; break;
                                                        case 'activity': $badgeBg = 'bg-warning text-dark'; $icon = 'bi-lightning'; break;
                                                        case 'meal': $badgeBg = 'bg-success'; $icon = 'bi-cup-hot'; break;
                                                    }
                                                @endphp
                                                <div class="p-2 border rounded bg-light d-flex align-items-center gap-2" style="font-size: 13px;">
                                                    <span class="badge {{ $badgeBg }}"><i class="bi {{ $icon }} me-1"></i>{{ strtoupper($item->item_type) }}</span>
                                                    <strong>{{ $item->title }}</strong>
                                                    @if(!empty($item->room_type))
                                                        <span class="text-muted">({{ $item->room_type }})</span>
                                                    @endif
                                                    @if($item->price > 0)
                                                        <span class="text-primary fw-bold">{{ $itinerary->currency_symbol }} {{ number_format($item->price, 2) }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($day->overnight_location))
                                    <div class="ps-2 small text-muted">
                                        <i class="bi bi-moon-stars me-1 text-indigo"></i><strong>Overnight:</strong> {{ $day->overnight_location }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                        No days scheduled yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- Inclusions & Exclusions -->
        @if(!empty($itinerary->inclusions) || !empty($itinerary->exclusions))
            <div class="row g-3 mb-4">
                @if(!empty($itinerary->inclusions))
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm border-start border-4 border-success">
                            <div class="card-body">
                                <h6 class="fw-bold text-success mb-3"><i class="bi bi-check-circle-fill me-2"></i>Inclusions</h6>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach((array)$itinerary->inclusions as $inc)
                                        <li class="mb-1"><i class="bi bi-check text-success me-1"></i>{{ $inc }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                @if(!empty($itinerary->exclusions))
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm border-start border-4 border-danger">
                            <div class="card-body">
                                <h6 class="fw-bold text-danger mb-3"><i class="bi bi-x-circle-fill me-2"></i>Exclusions</h6>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach((array)$itinerary->exclusions as $exc)
                                        <li class="mb-1"><i class="bi bi-x text-danger me-1"></i>{{ $exc }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Sidebar Right: Multi-Currency Pricing & Agency Details -->
    <div class="col-lg-4">
        <!-- Multi-Currency Pricing Card -->
        <div class="card mb-4 border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4 text-center">
                <span class="text-white-50 text-uppercase tracking-wider small fw-bold">Agent Selling Rate</span>
                <div class="display-5 fw-bold my-2 text-white" id="mainDisplayPrice">
                    {{ $itinerary->currency ?: 'MYR' }} {{ number_format($itinerary->b2bDetail->agent_rate ?? 0, 2) }}
                </div>
                <div class="small text-white-75 mb-3">
                    Net Cost: {{ $itinerary->currency_symbol }} {{ number_format($itinerary->b2bDetail->net_rate ?? 0, 2) }}
                    @if(($itinerary->b2bDetail->markup_percentage ?? 0) > 0)
                        | Markup: {{ $itinerary->b2bDetail->markup_percentage }}%
                    @endif
                </div>

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
                <div class="p-2 rounded bg-white bg-opacity-10 small">
                    <div class="d-flex justify-content-around text-white-75 flex-wrap gap-2">
                        @foreach($itinerary->converted_prices as $c)
                            <span><strong>{{ $c['code'] }}:</strong> {{ $c['symbol'] }} {{ number_format($c['amount'], 2) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Agency & Partner Information Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold py-3">
                <i class="bi bi-building text-primary me-2"></i>B2B Partner Details
            </div>
            <div class="card-body">
                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted d-block">Agency</small>
                    <span class="fw-bold">{{ $itinerary->b2bDetail->agency->company_name ?? 'Direct / In-House' }}</span>
                </div>
                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted d-block">Operating Supplier</small>
                    <span class="fw-bold">{{ $itinerary->b2bDetail->supplier->name ?? 'Standard Inventory' }}</span>
                </div>
                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted d-block">Pax Range</small>
                    <span class="fw-bold">{{ $itinerary->b2bDetail->min_pax ?? 1 }} - {{ $itinerary->b2bDetail->max_pax ?? 50 }} Pax</span>
                </div>
                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted d-block">Commission / Margin</small>
                    <span class="fw-bold">{{ $itinerary->currency_symbol }} {{ number_format($itinerary->b2bDetail->commission ?? 0, 2) }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Validity Period</small>
                    <span class="fw-bold">
                        {{ $itinerary->b2bDetail->validity_start ? \Carbon\Carbon::parse($itinerary->b2bDetail->validity_start)->format('d M Y') : 'Open' }}
                        @if($itinerary->b2bDetail->validity_end)
                            - {{ \Carbon\Carbon::parse($itinerary->b2bDetail->validity_end)->format('d M Y') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
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
            document.getElementById('mainDisplayPrice').textContent = `${code} ${symbol} ${amount}`;
        });
    });
</script>
@endpush
@endsection
