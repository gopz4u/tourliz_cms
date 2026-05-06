@extends('layouts.admin')

@section('title', 'Manage Activities')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-bicycle me-2"></i>Activities Master</h2>
                <p class="text-muted mb-0">Manage sightseeing and tour options</p>
            </div>
            <a href="{{ route('admin.activities.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Activity
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
                            <th>Activity Name</th>
                            <th>Supplier</th>
                            <th>Destination</th>
                            <th>Duration</th>
                            <th>Base Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $activity->name }}</strong></td>
                                <td>
                                    <span class="small fw-bold text-primary">{{ $activity->supplier->name ?? 'None' }}</span>
                                </td>
                                <td>{{ $activity->destination->name ?? 'N/A' }}</td>
                                <td>{{ $activity->duration ?? 'N/A' }}</td>
                                <td>$ {{ number_format($activity->base_price, 2) }}</td>
                                <td>
                                    @if($activity->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.activities.edit', $activity->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.activities.destroy', $activity->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this activity?');">
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
                                <td colspan="7" class="text-center py-5 text-muted">No activities found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $activities->links() }}
        </div>
    </div>
@endsection