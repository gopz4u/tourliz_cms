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

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="filter-destination" class="form-label"><i class="bi bi-funnel"></i> Filter by
                        Destination</label>
                    <select class="form-select" id="filter-destination">
                        <option value="">All Destinations</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter-package" class="form-label"><i class="bi bi-funnel"></i> Filter by Package</label>
                    <select class="form-select" id="filter-package">
                        <option value="">All Packages</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="services-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Destination</th>
                            <th>Package</th>
                            <th>Category</th>
                            <th>Amenities</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" class="text-center">
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
            $(document).ready(function () {
                loadDestinations();
                loadPackages();
                loadServices();

                // Filter by destination
                $('#filter-destination').on('change', function () {
                    loadServices();
                });

                // Filter by package
                $('#filter-package').on('change', function () {
                    loadServices();
                });
            });

            function loadDestinations() {
                $.ajax({
                    url: '{{ route("admin.destinations.index") }}?per_page=1000',
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        const destinationSelect = $('#filter-destination');
                        destinationSelect.find('option:not(:first)').remove();
                        let destinations = [];

                        // Handle paginated response
                        if (response && response.data && Array.isArray(response.data)) {
                            destinations = response.data;
                        } else if (Array.isArray(response)) {
                            destinations = response;
                        }

                        if (destinations.length > 0) {
                            destinations.forEach(function (destination) {
                                destinationSelect.append(`<option value="${destination.id}">${destination.name}</option>`);
                            });
                        } else {
                            console.warn('No destinations found');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Failed to load destinations:', xhr, status, error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            function loadPackages() {
                $.get('{{ route("admin.packages.index") }}', function (data) {
                    const packages = data.data || data;
                    const packageSelect = $('#filter-package');
                    packageSelect.find('option:not(:first)').remove();

                    if (Array.isArray(packages)) {
                        packages.forEach(function (pkg) {
                            packageSelect.append(`<option value="${pkg.id}">${pkg.name}</option>`);
                        });
                    }
                }).fail(function () {
                    console.error('Failed to load packages');
                });
            }

            function loadServices() {
                const destinationId = $('#filter-destination').val();
                const packageId = $('#filter-package').val();
                let url = '{{ route("admin.services.index") }}';
                const params = [];
                if (destinationId) params.push('destination_id=' + destinationId);
                if (packageId) params.push('package_id=' + packageId);
                if (params.length > 0) url += '?' + params.join('&');

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (data) {
                        const tbody = $('#services-table tbody');
                        tbody.empty();

                        // Handle paginated response
                        let services = [];
                        if (data && data.data && Array.isArray(data.data)) {
                            services = data.data;
                        } else if (Array.isArray(data)) {
                            services = data;
                        }

                        if (services.length === 0) {
                            tbody.append('<tr><td colspan="10" class="text-center">No services found</td></tr>');
                            return;
                        }

                        services.forEach(function (service) {
                            // Build amenities display based on category
                            let amenitiesHtml = '-';
                            if (service.category === 'Hotels') {
                                const amenities = [];
                                if (service.star_rating) {
                                    amenities.push(`<i class="bi bi-star-fill text-warning"></i> ${service.star_rating} Star${service.star_rating > 1 ? 's' : ''}`);
                                }
                                if (service.accommodation_type) {
                                    amenities.push(service.accommodation_type);
                                }
                                amenitiesHtml = amenities.length > 0 ? amenities.join(' | ') : '-';
                            } else if (service.category === 'Transport' || service.category === 'Airport Pickup' || service.category === 'Airport Drop') {
                                amenitiesHtml = service.vehicle_type || '-';
                            } else if (service.category === 'Entry Tickets') {
                                const ticketInfo = [];
                                if (service.ticket_name) {
                                    ticketInfo.push(service.ticket_name);
                                }
                                if (service.ticket_count) {
                                    ticketInfo.push(`Count: ${service.ticket_count}`);
                                }
                                amenitiesHtml = ticketInfo.length > 0 ? ticketInfo.join(' - ') : '-';
                            }

                            const destinationName = (service.destination && service.destination.name) ? service.destination.name : '<span class="text-muted">-</span>';
                            const packageName = (service.package && service.package.name) ? service.package.name : '<span class="text-muted">-</span>';
                            const currency = service.currency || 'INR';
                            const currencySymbols = {
                                'USD': '$',
                                'INR': '₹',
                                'MYR': 'RM',
                                'SGD': 'S$',
                                'AED': 'AED'
                            };
                            const currencySymbol = currencySymbols[currency] || currency;

                            const row = `
                                <tr>
                                    <td>${service.id}</td>
                                    <td><strong>${service.name}</strong></td>
                                    <td>${destinationName}</td>
                                    <td>${packageName}</td>
                                    <td>
                                        <span class="badge bg-info">${service.category || '-'}</span>
                                    </td>
                                    <td><small>${amenitiesHtml}</small></td>
                                    <td>${service.price ? currencySymbol + ' ' + service.price : 'Free'}</td>
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
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading services:', xhr, status, error);
                        let errorMsg = 'Error loading services';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#services-table tbody').html(`<tr><td colspan="10" class="text-center text-danger">${errorMsg}</td></tr>`);
                    }
                });
            }

            function deleteService(id) {
                if (!confirm('Are you sure you want to delete this service?')) return;

                $.ajax({
                    url: `/admin/services/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        loadServices();
                        alert('Service deleted successfully');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error deleting service:', xhr, status, error);
                        let errorMsg = 'Error deleting service';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMsg = 'Service not found';
                        } else if (xhr.status === 419) {
                            errorMsg = 'Session expired. Please refresh the page and try again.';
                        } else if (xhr.status === 403) {
                            errorMsg = 'You do not have permission to delete this service';
                        }
                        alert(errorMsg);
                    }
                });
            }
        </script>
    @endpush
@endsection