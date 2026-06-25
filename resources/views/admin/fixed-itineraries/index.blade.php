@extends('layouts.admin')

@section('title', 'Fixed Itineraries')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2><i class="bi bi-pin-map-fill me-2"></i>Fixed Itineraries</h2>
            <p class="text-muted mb-0">Multi-country, fixed-price packages for single-vendor scenarios</p>
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

<div class="card mb-3">
    <div class="card-body bg-light border-bottom">
        <form action="{{ route('admin.fixed-itineraries.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Title..."
                    value="{{ request('search') }}">
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

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Countries</th>
                    <th>Supplier</th>
                    <th>Fixed Price</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itineraries as $item)
                <tr>
                    <td class="fw-bold">{{ $item->title }}</td>
                    <td>
                        @foreach($item->countries as $c)
                            <span class="badge bg-info me-1">{{ $c->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $item->supplier->name ?? '—' }}</td>
                    <td class="fw-bold">{{ $item->currency }} {{ number_format($item->fixed_price, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $item->updated_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.fixed-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.fixed-itineraries.destroy', $item->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete this itinerary?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
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
    <div class="card-footer">
        {{ $itineraries->links() }}
    </div>
</div>
@endsection
