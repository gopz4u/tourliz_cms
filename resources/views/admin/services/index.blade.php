@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0"><i class="bi bi-tools"></i> Services</h1>
        <p class="text-muted mb-0">Manage tourism services</p>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Service
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="services-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">
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
        loadServices();
    });
    
    function loadServices() {
        $.get('{{ route("admin.services.index") }}', function(data) {
            const services = data.data || data;
            const tbody = $('#services-table tbody');
            tbody.empty();
            
            if (services.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center">No services found</td></tr>');
                return;
            }
            
            services.forEach(function(service) {
                const row = `
                    <tr>
                        <td>${service.id}</td>
                        <td><strong>${service.name}</strong></td>
                        <td>${service.category || '-'}</td>
                        <td>${service.price ? (service.currency || 'USD') + ' ' + service.price : 'Free'}</td>
                        <td>
                            <span class="badge ${service.is_active ? 'bg-success' : 'bg-secondary'} badge-status">
                                ${service.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>
                            ${service.is_featured ? '<span class="badge bg-warning">Featured</span>' : '-'}
                        </td>
                        <td>
                            <a href="/admin/services/${service.id}/edit" class="btn btn-sm btn-primary btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteService(${service.id})" class="btn btn-sm btn-danger btn-action">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }).fail(function() {
            $('#services-table tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading services</td></tr>');
        });
    }
    
    function deleteService(id) {
        if (!confirm('Are you sure you want to delete this service?')) return;
        
        $.ajax({
            url: `/admin/services/${id}`,
            type: 'DELETE',
            success: function() {
                loadServices();
                alert('Service deleted successfully');
            },
            error: function() {
                alert('Error deleting service');
            }
        });
    }
</script>
@endpush
@endsection

