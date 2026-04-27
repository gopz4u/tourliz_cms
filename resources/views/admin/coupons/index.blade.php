@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="bi bi-tag"></i> Coupons</h1>
            <p class="text-muted mb-0">Manage discount coupons</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Coupon
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('admin.coupons.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by code..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td><strong>{{ $coupon->code }}</strong></td>
                                <td>
                                    @if($coupon->type == 'fixed')
                                        <span class="badge bg-primary">Fixed</span>
                                    @else
                                        <span class="badge bg-info text-dark">Percentage</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->type == 'fixed')
                                        {{ number_format($coupon->value, 2) }}
                                    @else
                                        {{ $coupon->value }}%
                                    @endif
                                </td>
                                <td>
                                    {{ $coupon->used_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->start_date)
                                        <small class="d-block text-muted">Start: {{ $coupon->start_date->format('Y-m-d') }}</small>
                                    @endif
                                    @if($coupon->end_date)
                                        <small class="d-block text-muted">End: {{ $coupon->end_date->format('Y-m-d') }}</small>
                                    @endif
                                    @if(!$coupon->start_date && !$coupon->end_date)
                                        <small class="text-muted">No date limits</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                        class="d-inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this coupon?');">
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
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted mb-0">No coupons found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
@endsection