@extends('layouts.admin')

@section('title', 'Manage Tourist Spots')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
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
            <div class="table-responsive">
                <table class="table table-hover align-middle">
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
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $spot->name }}</strong></td>
                                <td><span class="badge bg-primary">{{ $spot->country->name ?? '—' }}</span></td>
                                <td>{{ $spot->destination->name ?? 'Country-level' }}</td>
                                <td><small class="text-muted">{{ Str::limit($spot->description, 50) }}</small></td>
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
            {{ $spots->links() }}
        </div>
    </div>
@endsection