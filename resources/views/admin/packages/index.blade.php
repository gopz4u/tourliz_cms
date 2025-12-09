@extends('layouts.admin')

@section('title', 'Packages')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0"><i class="bi bi-briefcase"></i> Packages</h1>
        <p class="text-muted mb-0">Manage tour packages</p>
    </div>
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Package
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="packages-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Duration</th>
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
        loadPackages();
    });
    
    function loadPackages() {
        $.get('{{ route("admin.packages.index") }}', function(data) {
            const packages = data.data || data;
            const tbody = $('#packages-table tbody');
            tbody.empty();
            
            if (packages.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center">No packages found</td></tr>');
                return;
            }
            
            packages.forEach(function(pkg) {
                const duration = pkg.duration_days ? `${pkg.duration_days}D/${pkg.duration_nights || 0}N` : '-';
                const row = `
                    <tr>
                        <td>${pkg.id}</td>
                        <td><strong>${pkg.name}</strong></td>
                        <td>${pkg.currency || 'USD'} ${pkg.price}${pkg.discount_price ? ' <small class="text-muted">(Was ${pkg.discount_price})</small>' : ''}</td>
                        <td>${duration}</td>
                        <td>
                            <span class="badge ${pkg.is_active ? 'bg-success' : 'bg-secondary'} badge-status">
                                ${pkg.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>
                            ${pkg.is_featured ? '<span class="badge bg-warning">Featured</span>' : '-'}
                        </td>
                        <td>
                            <a href="/admin/packages/${pkg.id}/edit" class="btn btn-sm btn-primary btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deletePackage(${pkg.id})" class="btn btn-sm btn-danger btn-action">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }).fail(function() {
            $('#packages-table tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading packages</td></tr>');
        });
    }
    
    function deletePackage(id) {
        if (!confirm('Are you sure you want to delete this package?')) return;
        
        $.ajax({
            url: `/admin/packages/${id}`,
            type: 'DELETE',
            success: function() {
                loadPackages();
                alert('Package deleted successfully');
            },
            error: function() {
                alert('Error deleting package');
            }
        });
    }
</script>
@endpush
@endsection

