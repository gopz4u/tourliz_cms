@extends('layouts.admin')

@section('title', 'Manage Tourist Spots')

@push('styles')
    <style>
        .spot-filters { background: #f8fafc; border: 1px solid #e8edf3; border-radius: .8rem; }
        .spot-results-meta { color: #64748b; font-size: .82rem; }
        .spot-table th { white-space: nowrap; }
        .spot-table .spot-description { max-width: 320px; }
        .pagination { margin-bottom: 0; }
        @media (max-width: 767.98px) {
            .spot-page-header { align-items: flex-start !important; gap: .75rem; }
            .spot-page-header .btn { width: 100%; }
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap spot-page-header">
            <div>
                <h2><i class="bi bi-geo-alt me-2"></i>Tourist Spots Master</h2>
                <p class="text-muted mb-0">Manage key attractions for each destination</p>
            </div>
            <a href="{{ route('admin.tourist-spots.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Tourist Spot
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tourist-spots.index') }}" class="spot-filters p-3 mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-xl-4 col-md-6">
                        <label for="spot-search" class="form-label small fw-semibold mb-1">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="search" id="spot-search" name="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Spot name or description">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-3 col-6">
                        <label for="country-filter" class="form-label small fw-semibold mb-1">Country</label>
                        <select id="country-filter" name="country_id" class="form-select form-select-sm">
                            <option value="">All countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ (string) request('country_id') === (string) $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3 col-6">
                        <label for="destination-filter" class="form-label small fw-semibold mb-1">Destination</label>
                        <select id="destination-filter" name="destination_id" class="form-select form-select-sm">
                            <option value="">All destinations</option>
                            @foreach($destinations as $destination)
                                <option value="{{ $destination->id }}" {{ (string) request('destination_id') === (string) $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-3 col-6">
                        <label for="status-filter" class="form-label small fw-semibold mb-1">Status</label>
                        <select id="status-filter" name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-3 col-6">
                        <label for="per-page" class="form-label small fw-semibold mb-1">Per page</label>
                        <select id="per-page" name="per_page" class="form-select form-select-sm">
                            @foreach([15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 15) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-funnel me-1"></i>Apply</button>
                        <a href="{{ route('admin.tourist-spots.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear filters"><i class="bi bi-x-lg"></i></a>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 spot-results-meta">
                <span>
                    @if($spots->total())
                        Showing <strong>{{ $spots->firstItem() }}</strong>–<strong>{{ $spots->lastItem() }}</strong>
                        of <strong>{{ number_format($spots->total()) }}</strong> tourist spots
                    @else
                        No tourist spots match the selected filters
                    @endif
                </span>
                @if(request()->hasAny(['search', 'country_id', 'destination_id', 'status']))
                    <span class="badge bg-light text-dark border">Filtered results</span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle spot-table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Spot Name</th>
                            <th>Country</th>
                            <th>Destination</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($spots as $spot)
                            <tr>
                                <td>{{ $spots->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $spot->name }}</strong></td>
                                <td><span class="badge bg-primary">{{ $spot->country->name ?? '—' }}</span></td>
                                <td>{{ $spot->destination->name ?? 'Country-level' }}</td>
                                <td class="spot-description"><small class="text-muted">{{ Str::limit($spot->description, 65) }}</small></td>
                                <td>
                                    @if($spot->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.tourist-spots.edit', $spot->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.tourist-spots.destroy', $spot->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this spot?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No tourist spots found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($spots->hasPages())
                <div class="d-flex justify-content-center justify-content-md-end mt-3">
                    {{ $spots->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
