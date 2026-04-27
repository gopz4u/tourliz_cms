@extends('layouts.admin')

@section('title', 'Preview Itinerary')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-eye me-2"></i>Preview Itinerary</h2>
            <p class="text-muted mb-0">Previewing itinerary for: <strong>{{ $package->name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('admin.itineraries.edit', $package->id) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-2"></i>Continue Editing
            </a>
            <a href="{{ route('admin.itineraries.pdf', $package->id) }}" class="btn btn-success">
                <i class="bi bi-file-pdf me-2"></i>Download PDF
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Itinerary Timeline -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="timeline">
                    @forelse($enrichedItinerary as $day)
                        <div class="timeline-item mb-5 position-relative ps-4" style="border-left: 3px solid #e9ecef;">
                            <div class="position-absolute bg-primary rounded-circle" style="width: 16px; height: 16px; left: -9.5px; top: 5px;"></div>
                            
                            <h4 class="mb-3">
                                <span class="badge bg-primary me-2">Day {{ $day['day'] }}</span>
                                {{ $day['title'] }}
                            </h4>
                            
                            <!-- Places -->
                            @if(!empty($day['places']))
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="text-uppercase text-muted fs-7 fw-bold mb-2"><i class="bi bi-geo-alt me-2"></i>Destinations to Visit</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($day['places'] as $place)
                                        <li class="mb-2 d-flex justify-content-between">
                                            <span>
                                                <strong>{{ $place['name'] }}</strong>
                                                @if(isset($place['visit_duration']))
                                                    <small class="text-muted">({{ $place['visit_duration'] }})</small>
                                                @endif
                                            </span>
                                            @if(isset($place['entry_ticket']) && $place['entry_ticket']['required'] && $place['entry_ticket']['price'] > 0)
                                                <span class="badge bg-secondary">{{ $place['entry_ticket']['currency'] }} {{ number_format($place['entry_ticket']['price'], 2) }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            <div class="row">
                                <!-- Activities -->
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm bg-body-tertiary">
                                        <div class="card-body">
                                            <h6 class="text-uppercase text-muted fs-7 fw-bold mb-2"><i class="bi bi-activity me-2"></i>Activities</h6>
                                            @if(!empty($day['activities']))
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($day['activities'] as $activity)
                                                        <li class="mb-3 pb-2 border-bottom last-no-border">
                                                            <div class="d-flex justify-content-between">
                                                                <strong>{{ $activity['name'] }}</strong>
                                                                @if(isset($activity['entry_ticket']) && $activity['entry_ticket']['price'] > 0)
                                                                    <span class="badge bg-secondary">{{ $activity['entry_ticket']['currency'] }} {{ number_format($activity['entry_ticket']['price'], 2) }}</span>
                                                                @endif
                                                            </div>
                                                            @if(isset($activity['time']))
                                                                <small class="d-block text-muted"><i class="bi bi-clock me-1"></i>{{ $activity['time'] }} ({{ $activity['duration'] ?? '' }})</small>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted small fst-italic mb-0">No specific activities scheduled.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Transport -->
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm bg-body-tertiary">
                                        <div class="card-body">
                                            <h6 class="text-uppercase text-muted fs-7 fw-bold mb-2"><i class="bi bi-car-front me-2"></i>Transport</h6>
                                            @if(!empty($day['transport']))
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($day['transport'] as $transport)
                                                        <li class="mb-2">
                                                            <div class="d-flex justify-content-between">
                                                                <span>{{ $transport['type'] }}</span>
                                                                @if(isset($transport['price']) && $transport['price'] > 0)
                                                                    <span class="badge bg-secondary">{{ $transport['currency'] ?? 'USD' }} {{ number_format($transport['price'], 2) }}</span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted d-block">
                                                                {{ $transport['from'] }} <i class="bi bi-arrow-right mx-1"></i> {{ $transport['to'] }}
                                                            </small>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted small fst-italic mb-0">No transport details.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hotel & Meals -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="p-3 bg-info bg-opacity-10 rounded border border-info border-opacity-25">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="text-uppercase text-info fs-7 fw-bold mb-2"><i class="bi bi-building me-2"></i>Accommodation</h6>
                                                @if(!empty($day['hotel']))
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $day['hotel']['name'] ?? 'Hotel' }}</strong>
                                                            @if(isset($day['hotel']['type']))
                                                                <span class="badge bg-info text-dark ms-2">{{ $day['hotel']['type'] }}</span>
                                                            @endif
                                                        </div>
                                                        @if(isset($day['hotel']['price_per_night']) && $day['hotel']['price_per_night'] > 0)
                                                            <span class="fw-bold text-info">{{ $day['hotel']['currency'] ?? 'USD' }} {{ number_format($day['hotel']['price_per_night'], 2) }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">No hotel details.</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6 border-start border-info border-opacity-25 ps-md-3 mt-3 mt-md-0">
                                                <h6 class="text-uppercase text-info fs-7 fw-bold mb-2"><i class="bi bi-cup-hot me-2"></i>Meals</h6>
                                                @if(!empty($day['meals']))
                                                    <div class="small">
                                                        <span class="me-3"><strong class="text-muted">B:</strong> {{ $day['meals']['breakfast'] ?? 'No' }}</span>
                                                        <span class="me-3"><strong class="text-muted">L:</strong> {{ $day['meals']['lunch'] ?? 'No' }}</span>
                                                        <span><strong class="text-muted">D:</strong> {{ $day['meals']['dinner'] ?? 'No' }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">No meal details.</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!empty($day['notes']))
                            <div class="mt-3 text-muted small fst-italic">
                                <i class="bi bi-info-circle me-1"></i> {{ $day['notes'] }}
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="lead text-muted">No itinerary data defined yet.</p>
                            <a href="{{ route('admin.itineraries.edit', $package->id) }}" class="btn btn-primary">Create Itinerary</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card mb-4 sticky-top" style="top: 20px; z-index: 1;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Cost Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Accommodation</span>
                    <strong>{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['hotels'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Transport</span>
                    <strong>{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['transport'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Activities</span>
                    <strong>{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['activities'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Entry Tickets</span>
                    <strong>{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['entry_tickets'], 2) }}</strong>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">TOTAL</h5>
                    <h4 class="text-primary mb-0">{{ $costBreakdown['currency'] }} {{ number_format($costBreakdown['total'], 2) }}</h4>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h6>Package Details</h6>
                <dl class="row mb-0 small">
                    <dt class="col-sm-4">Duration</dt>
                    <dd class="col-sm-8">{{ $package->duration }}</dd>
                    
                    <dt class="col-sm-4">Destination (Country)</dt>
                    <dd class="col-sm-8">{{ $package->destination->name ?? 'N/A' }}</dd>
                    
                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8">{{ $package->updated_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<style>
    .last-no-border:last-child {
        border-bottom: 0 !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endsection
