@extends('layouts.admin')

@section('title', 'Fixed Itineraries')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2><i class="bi bi-pin-map-fill text-primary me-2"></i>Fixed Itineraries</h2>
            <p class="text-muted mb-0">Multi-country, fixed-price packages with Operations day-by-day inventory integration</p>
        </div>
        <a href="{{ route('admin.fixed-itineraries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>New Fixed Itinerary
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body bg-light border-bottom">
        <form action="{{ route('admin.fixed-itineraries.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Title, description..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Destination</label>
                <select name="destination_id" class="form-select form-select-sm">
                    <option value="">All Destinations</option>
                    @foreach($destinations as $d)
                        <option value="{{ $d->id }}" {{ request('destination_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('admin.fixed-itineraries.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Package Details</th>
                    <th>Schedule</th>
                    <th>Countries</th>
                    <th>Supplier</th>
                    <th>Fixed Price</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itineraries as $item)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->title }}</div>
                        <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="bi bi-clock me-1"></i>{{ $item->duration }}
                            </span>
                            @if($item->destination)
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $item->destination->name }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $daysCount = is_array($item->days) ? count($item->days) : 0;
                        @endphp
                        @if($daysCount > 0)
                            <span class="badge bg-info text-white">
                                <i class="bi bi-calendar3 me-1"></i>{{ $daysCount }} Days Planned
                            </span>
                        @else
                            <span class="badge bg-secondary">Overview Only</span>
                        @endif
                    </td>
                    <td>
                        @forelse($item->countries as $c)
                            <span class="badge bg-light text-dark border me-1">{{ $c->name }}</span>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse
                    </td>
                    <td>
                        @if($item->suppliers->count() > 1)
                            <span class="badge bg-light text-dark border" title="{{ $item->suppliers->pluck('name')->join(', ') }}">
                                <i class="bi bi-buildings me-1"></i>{{ $item->suppliers->first()->name }} +{{ $item->suppliers->count() - 1 }}
                            </span>
                        @elseif($item->suppliers->count() === 1)
                            <span class="text-dark small">{{ $item->suppliers->first()->name }}</span>
                        @else
                            <span class="text-muted small">{{ $item->supplier->name ?? 'Direct' }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-success fs-6">
                            {{ $item->currency ?: 'MYR' }} {{ number_format($item->fixed_price, 2) }}
                        </div>
                        <div class="small text-muted fw-normal" style="font-size: 0.72rem;">
                            @php $conv = $item->converted_prices; @endphp
                            @if(($item->currency ?: 'MYR') === 'MYR')
                                ≈ ${{ number_format($conv['USD']['amount'], 0) }} &bull; ₹{{ number_format($conv['INR']['amount'], 0) }}
                            @elseif(($item->currency ?: 'MYR') === 'USD')
                                ≈ RM{{ number_format($conv['MYR']['amount'], 0) }} &bull; ₹{{ number_format($conv['INR']['amount'], 0) }}
                            @else
                                ≈ RM{{ number_format($conv['MYR']['amount'], 0) }} &bull; ${{ number_format($conv['USD']['amount'], 0) }}
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.fixed-itineraries.show', $item->id) }}" class="btn btn-outline-info" title="Preview Proposal">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.fixed-itineraries.pdf', $item->id) }}" target="_blank" class="btn btn-outline-danger" title="Printable PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <a href="{{ route('admin.fixed-itineraries.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.fixed-itineraries.duplicate', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary" title="Duplicate and customize">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.fixed-itineraries.destroy', $item->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this itinerary?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No fixed itineraries found. <a href="{{ route('admin.fixed-itineraries.create') }}">Create one</a>.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $itineraries->links() }}
    </div>
</div>
@endsection
