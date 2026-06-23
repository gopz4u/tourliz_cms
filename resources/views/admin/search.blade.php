@extends('layouts.admin')
@section('title', 'Search: ' . $q)

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex align-items-center mb-4">
        <h4 class="fw-bold mb-0">Search Results</h4>
        <span class="ms-3 text-muted">"{{ $q }}" — {{ $totalCount }} result(s)</span>
    </div>

    @if($totalCount === 0 && strlen($q) >= 2)
        <div class="text-center py-5 text-muted">
            <i class="bi bi-search fs-1 d-block mb-3"></i>
            No results found for "{{ $q }}"
        </div>
    @endif

    {{-- B2B Itineraries --}}
    @if($b2bResults->isNotEmpty())
    <h6 class="text-uppercase text-muted fw-bold small mb-2">B2B Proposals ({{ $b2bResults->count() }})</h6>
    <div class="list-group mb-4">
        @foreach($b2bResults as $item)
        <a href="{{ route('admin.b2b-itineraries.edit', $item->id) }}" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $item->quote_id }}</strong> —
                    {{ $item->title }}
                    <small class="text-muted ms-2">{{ $item->client_name }}</small>
                </div>
                <span class="badge bg-primary">{{ $item->destination->name ?? 'N/A' }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- B2C Leads --}}
    @if($b2cResults->isNotEmpty())
    <h6 class="text-uppercase text-muted fw-bold small mb-2">B2C Leads ({{ $b2cResults->count() }})</h6>
    <div class="list-group mb-4">
        @foreach($b2cResults as $item)
        <a href="{{ route('admin.b2c-itineraries.edit', $item->id) }}" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $item->quote_id }}</strong> —
                    {{ $item->title }}
                    <small class="text-muted ms-2">{{ $item->client_name }} | {{ $item->phone }}</small>
                </div>
                <span class="badge bg-success">{{ $item->lead_source ?? 'walk-in' }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Group Itineraries --}}
    @if($groupResults->isNotEmpty())
    <h6 class="text-uppercase text-muted fw-bold small mb-2">Group Proposals ({{ $groupResults->count() }})</h6>
    <div class="list-group mb-4">
        @foreach($groupResults as $item)
        <a href="{{ route('admin.group-itineraries.edit', $item->id) }}" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $item->quote_id }}</strong> —
                    {{ $item->title }}
                    <small class="text-muted ms-2">{{ $item->client_name }}</small>
                </div>
                <span class="badge bg-warning text-dark">Group</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Bookings --}}
    @if($bookingResults->isNotEmpty())
    <h6 class="text-uppercase text-muted fw-bold small mb-2">Bookings ({{ $bookingResults->count() }})</h6>
    <div class="list-group mb-4">
        @foreach($bookingResults as $item)
        <a href="{{ route('admin.bookings.show', $item->id) }}" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>BK-{{ $item->id }}</strong> —
                    {{ $item->name ?? $item->customer_name }}
                    <small class="text-muted ms-2">{{ $item->email ?? $item->customer_email }}</small>
                </div>
                <span class="badge bg-info">{{ $item->package->name ?? 'Package' }}</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Agencies --}}
    @if($agencyResults->isNotEmpty())
    <h6 class="text-uppercase text-muted fw-bold small mb-2">Agencies ({{ $agencyResults->count() }})</h6>
    <div class="list-group mb-4">
        @foreach($agencyResults as $item)
        <a href="{{ route('admin.agencies.edit', $item->id) }}" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $item->company_name }}</strong>
                    <small class="text-muted ms-2">{{ $item->primary_contact_name }} | {{ $item->whatsapp_number }}</small>
                </div>
                <span class="badge bg-dark">Agency</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
