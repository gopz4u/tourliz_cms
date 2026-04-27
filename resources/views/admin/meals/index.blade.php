@extends('layouts.admin')

@section('title', 'Manage Meals')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-egg-fried me-2"></i>Meals Master</h2>
                <p class="text-muted mb-0">Manage meal plans and individual menu rates</p>
            </div>
            <a href="{{ route('admin.meals.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Meal Option
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
                            <th>Meal Name</th>
                            <th>Type</th>
                            <th>Destination</th>
                            <th>Base Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meals as $meal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $meal->name }}</strong></td>
                                <td><span class="badge bg-info text-dark">{{ $meal->type }}</span></td>
                                <td>{{ $meal->destination->name ?? 'Global' }}</td>
                                <td>$ {{ number_format($meal->price, 2) }}</td>
                                <td>
                                    @if($meal->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.meals.edit', $meal->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.meals.destroy', $meal->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this meal option?');">
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
                                <td colspan="7" class="text-center py-5 text-muted">No meal options found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $meals->links() }}
        </div>
    </div>
@endsection