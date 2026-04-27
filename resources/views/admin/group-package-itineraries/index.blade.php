@extends('layouts.admin')

@section('title', 'Group Itineraries')

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-calendar-check me-2"></i>Group Itineraries</h2>
                <p class="text-muted mb-0">Manage day-by-day group package itineraries</p>
            </div>
            <a href="{{ route('admin.group-packages.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people me-2"></i>View All Group Packages
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Destination</th>
                            <th>Days</th>
                            <th>Total Cost</th>
                            <th>Last Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            @php
                                $itinerary = $package->itinerary ?? [];
                                $days = count($itinerary);
                                $costs = \App\Helpers\ItineraryHelper::calculateTotalCost($itinerary, $package->currency);
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $package->name }}</strong><br>
                                    <small class="text-muted">{{ $package->duration ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $package->destination->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-info">{{ $days }} Days</span></td>
                                <td>
                                    <strong>{{ $costs['currency'] }} {{ number_format($costs['total'], 2) }}</strong>
                                </td>
                                <td>{{ $package->updated_at->diffForHumans() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.group-package-itineraries.preview', $package->id) }}" class="btn btn-sm btn-info"
                                        title="Preview" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.group-package-itineraries.edit', $package->id) }}" class="btn btn-sm btn-primary"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.group-package-itineraries.pdf', $package->id) }}" class="btn btn-sm btn-success"
                                        title="Download PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('admin.group-package-itineraries.destroy', $package->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this itinerary?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">No itineraries found. Create one from the packages page.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($packages->hasPages())
                <div class="mt-3">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Quick Guide</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="bi bi-1-circle me-2"></i>Create Itinerary</h6>
                    <p class="text-muted">Go to Group Packages → Edit Group Package → Manage Itinerary</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-2-circle me-2"></i>Edit Days</h6>
                    <p class="text-muted">Click Edit to add/remove days and customize details</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-3-circle me-2"></i>Preview & Export</h6>
                    <p class="text-muted">Preview itinerary or download as PDF</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-4-circle me-2"></i>Cost Breakdown</h6>
                    <p class="text-muted">Automatically calculated from hotels, transport, activities</p>
                </div>
            </div>
        </div>
    </div>
@endsection