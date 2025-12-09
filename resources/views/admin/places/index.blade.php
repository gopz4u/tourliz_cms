@extends('layouts.admin')

@section('title', 'Places')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0"><i class="bi bi-geo-alt"></i> Places</h1>
        <p class="text-muted mb-0">Manage tourist places</p>
    </div>
    <a href="{{ route('admin.places.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Place
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="places-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Region</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        loadPlaces();
    });
    
    function loadPlaces() {
        $.get('{{ route("admin.places.index") }}', function(data) {
            const places = data.data || data;
            const tbody = $('#places-table tbody');
            tbody.empty();
            
            if (places.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center">No places found</td></tr>');
                return;
            }
            
                places.forEach(function(place) {
                const row = `
                    <tr>
                        <td>${place.id}</td>
                        <td><strong>${place.name}</strong></td>
                        <td>${place.location || '-'}</td>
                        <td>${place.region || '-'}</td>
                        <td>${place.rating ? '⭐ '.repeat(place.rating) : '-'}</td>
                        <td>
                            <span class="badge ${place.status ? 'bg-success' : 'bg-secondary'} badge-status">
                                ${place.status ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>
                            ${place.featured ? '<span class="badge bg-warning">Featured</span>' : '-'}
                        </td>
                        <td>
                            <a href="/admin/places/${place.id}/edit" class="btn btn-sm btn-primary btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deletePlace(${place.id})" class="btn btn-sm btn-danger btn-action">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }).fail(function() {
            $('#places-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading places</td></tr>');
        });
    }
    
    function deletePlace(id) {
        if (!confirm('Are you sure you want to delete this place?')) return;
        
        $.ajax({
            url: `/admin/places/${id}`,
            type: 'DELETE',
            success: function() {
                loadPlaces();
                alert('Place deleted successfully');
            },
            error: function() {
                alert('Error deleting place');
            }
        });
    }
</script>
@endpush
@endsection

