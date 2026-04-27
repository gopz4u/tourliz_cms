@extends('layouts.admin')

@section('title', 'Countries')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="bi bi-globe"></i> Countries</h1>
            <p class="text-muted mb-0">Manage countries master list</p>
        </div>
        <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Country
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $country)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $country->name }}</strong>
                                </td>
                                <td>{{ $country->code ?? '-' }}</td>
                                <td>
                                    @if($country->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.countries.edit', $country->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.countries.destroy', $country->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this country?');">
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
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No countries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $countries->links() }}
            </div>
        </div>
    </div>
@endsection