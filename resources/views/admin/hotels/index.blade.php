@extends('layouts.admin')

@section('title', 'Manage Hotels')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-building me-2"></i>Hotels Master</h2>
                <p class="text-muted mb-0">Manage hotel inventory and room rates</p>
            </div>
            <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Hotel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Hotel Name</th>
                            <th>Destination</th>
                            <th>Rating</th>
                            <th>Room Types</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotels as $hotel)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $hotel->name }}</strong>
                                    <br><small class="text-muted">{{ Str::limit($hotel->address, 30) }}</small>
                                </td>
                                <td>{{ $hotel->destination->name ?? 'N/A' }}</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i
                                            class="bi bi-star-fill {{ $i <= $hotel->star_rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                    @endfor
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $hotel->rooms->count() }} Rooms</span>
                                </td>
                                <td>
                                    @if($hotel->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.hotels.edit', $hotel->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.hotels.destroy', $hotel->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this hotel?');">
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    No hotels found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $hotels->links() }}
        </div>
    </div>
@endsection