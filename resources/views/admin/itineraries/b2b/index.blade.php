@extends('layouts.admin')

@section('title', 'B2B Itineraries Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">B2B Partner & Agency Proposals</h2>
        <p class="text-muted small mb-0">Manage net rates, agency rates, supplier assignments, and operational notes.</p>
    </div>
    <div>
        <a href="{{ route('admin.itineraries.b2b.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create B2B Proposal
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.itineraries.b2b.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search title, agency, slug..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="destination_id" class="form-select">
                    <option value="">All Destinations</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ ($filters['destination_id'] ?? '') == $dest->id ? 'selected' : '' }}>
                            {{ $dest->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="published" {{ ($filters['status'] ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ ($filters['status'] ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('admin.itineraries.b2b.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- B2B Itineraries Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title / Agency</th>
                        <th>Destination</th>
                        <th>Duration</th>
                        <th>Net Rate</th>
                        <th>Agent Rate</th>
                        <th>Status</th>
                        <th>Updated Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itineraries as $itinerary)
                        <tr>
                            <td>#{{ $itinerary->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $itinerary->title }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-building me-1"></i>{{ $itinerary->b2bDetail->agency->name ?? 'Direct B2B' }}
                                </small>
                            </td>
                            <td>{{ $itinerary->destination->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $itinerary->duration_days }}D / {{ $itinerary->duration_nights }}N
                                </span>
                            </td>
                            <td class="fw-bold text-secondary">
                                ₹{{ number_format($itinerary->b2bDetail->net_rate ?? 0, 2) }}
                            </td>
                            <td class="fw-bold text-primary">
                                ₹{{ number_format($itinerary->b2bDetail->agent_rate ?? 0, 2) }}
                            </td>
                            <td>
                                @if($itinerary->status === 'published')
                                    <span class="badge bg-success">Active</span>
                                @elseif($itinerary->status === 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @else
                                    <span class="badge bg-secondary">Archived</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $itinerary->updated_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.itineraries.b2b.edit', $itinerary->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.itineraries.b2b.destroy', $itinerary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this B2B itinerary?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No B2B itineraries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($itineraries->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $itineraries->links() }}
        </div>
    @endif
</div>
@endsection
