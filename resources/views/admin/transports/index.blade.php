@extends('layouts.admin')

@section('title', 'Manage Transport')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-bus-front me-2"></i>Transport Master</h2>
                <p class="text-muted mb-0">Manage fleet and transport rates</p>
            </div>
            <a href="{{ route('admin.transports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Transport
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Base Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transports as $transport)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $transport->name }}</strong></td>
                                <td>{{ $transport->vehicle_type }}</td>
                                <td><i class="bi bi-person"></i> {{ $transport->capacity }} Pax</td>
                                <td>$ {{ number_format($transport->base_price, 2) }}</td>
                                <td>
                                    @if($transport->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.transports.edit', $transport->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.transports.destroy', $transport->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this vehicle?');">
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
                                <td colspan="7" class="text-center py-5 text-muted">No transport options found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transports->links() }}
        </div>
    </div>
@endsection