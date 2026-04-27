@extends('layouts.admin')

@section('title', 'Manage Agencies')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-briefcase-fill me-2"></i>B2B Agencies</h2>
                <p class="text-muted mb-0">Manage partner agencies and destination specialists</p>
            </div>
            <a href="{{ route('admin.agencies.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add Agency
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
                            <th>Company</th>
                            <th>Contact info</th>
                            <th>Specializations</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agencies as $agency)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $agency->company_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $agency->primary_contact_name }}</small>
                                </td>
                                <td>
                                    @if($agency->whatsapp_number)
                                        <div><i class="bi bi-whatsapp me-1 text-success"></i> {{ $agency->whatsapp_number }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agency->destinations->count() > 0)
                                        @foreach($agency->destinations as $destination)
                                            <span class="badge bg-info text-dark mb-1">{{ $destination->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted fst-italic">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agency->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.b2b-itineraries.create', ['agency_id' => $agency->id]) }}"
                                        class="btn btn-sm btn-outline-success me-1" title="Create Proposal">
                                        <i class="bi bi-file-earmark-plus"></i>
                                    </a>
                                    <a href="{{ route('admin.agencies.edit', $agency->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.agencies.destroy', $agency->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this agency?');">
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-briefcase fs-1 d-block mb-3"></i>
                                    No B2B agencies found. Click "Add Agency" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $agencies->links() }}
            </div>
        </div>
    </div>
@endsection