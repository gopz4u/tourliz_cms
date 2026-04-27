@extends('layouts.admin')

@section('title', 'Destinations')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="bi bi-geo-alt"></i> Destinations</h1>
            <p class="text-muted mb-0">Manage countries, locations, and cities</p>
        </div>
        <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Destination
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Display Name</th>
                            <th>Country</th>
                            <th>Location</th>
                            <th>City</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($destinations as $destination)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $destination->name }}</strong>
                                </td>
                                <td>{{ $destination->country ?? '-' }}</td>
                                <td>{{ $destination->location ?? '-' }}</td>
                                <td>{{ $destination->city ?? '-' }}</td>
                                <td>
                                    @if($destination->rating)
                                        @for($i = 1; $i <= 5; $i++)
                                            <i
                                                class="bi bi-star-fill {{ $i <= $destination->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                        @endfor
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($destination->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.destinations.edit', $destination->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.destinations.destroy', $destination->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this destination?\n\nNote: Any packages associated with this destination will have their reference removed.');">
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No destinations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $destinations->links() }}
            </div>
        </div>
    </div>
@endsection