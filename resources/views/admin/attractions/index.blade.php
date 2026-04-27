@extends('layouts.admin')

@section('title', 'Attractions')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="bi bi-camera"></i> Attractions</h1>
            <p class="text-muted mb-0">Manage tourist attractions</p>
        </div>
        <a href="{{ route('admin.attractions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Attraction
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
                <table class="table table-hover" id="attractions-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Destination</th>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Offer Price</th>
                            <th>Status</th>
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
            $(document).ready(function () {
                loadDestinations();
                loadPackages();
                loadAttractions();

                // Filter by destination
                $('#filter-destination').on('change', function () {
                    loadAttractions();
                });

                // Filter by package
                $('#filter-package').on('change', function () {
                    loadAttractions();
                });
            });

            function loadDestinations() {
                $.get('{{ route("admin.destinations.index") }}', function (data) {
                    const destinations = data.data || data;
                    const destinationSelect = $('#filter-destination');
                    destinationSelect.find('option:not(:first)').remove();

                    if (Array.isArray(destinations)) {
                        destinations.forEach(function (dest) {
                            destinationSelect.append(`<option value="${dest.id}">${dest.city} (${dest.name || dest.country})</option>`);
                        });
                    }
                }).fail(function () {
                    console.error('Failed to load destinations');
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

            function loadAttractions() {
                const destinationId = $('#filter-destination').val();
                const packageId = $('#filter-package').val();
                let url = '{{ route("admin.attractions.index") }}?';
                const params = [];
                if (destinationId) params.push('destination_id=' + destinationId);
                if (packageId) params.push('package_id=' + packageId);
                if (params.length > 0) url += params.join('&');

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        const tbody = $('#attractions-table tbody');
                        tbody.empty();

                        // Handle paginated response
                        let attractions = [];
                        if (response && response.data && Array.isArray(response.data)) {
                            attractions = response.data;
                        } else if (Array.isArray(response)) {
                            attractions = response;
                        }

                        if (attractions.length === 0) {
                            tbody.append('<tr><td colspan="8" class="text-center">No attractions found</td></tr>');
                            return;
                        }

                        attractions.forEach(function (attraction) {
                            const destinationName = (attraction.destination && attraction.destination.city) ? attraction.destination.city : (attraction.destination && attraction.destination.name ? attraction.destination.name : '<span class="text-muted">-</span>');
                            const packageName = (attraction.package && attraction.package.name) ? attraction.package.name : '<span class="text-muted">-</span>';
                            const price = attraction.price || 0;
                            const offerPrice = attraction.offer_price || null;
                            const currency = attraction.currency || 'INR';
                            const currencySymbols = {
                                'USD': '$',
                                'INR': '₹',
                                'MYR': 'RM',
                                'SGD': 'S$',
                                'AED': 'AED'
                            };
                            const currencySymbol = currencySymbols[currency] || currency;
                            const isActive = attraction.is_active !== undefined ? attraction.is_active : (attraction.status !== undefined ? attraction.status : true);

                            const row = `
                                <tr>
                                    <td>${attraction.id}</td>
                                    <td><strong>${attraction.name || 'Unnamed'}</strong></td>
                                    <td>${destinationName}</td>
                                    <td>${packageName}</td>
                                    <td>${currencySymbol} ${price}</td>
                                    <td>${offerPrice ? currencySymbol + ' ' + offerPrice : '<span class="text-muted">-</span>'}</td>
                                    <td>
                                        <span class="badge ${isActive ? 'bg-success' : 'bg-secondary'} badge-status">
                                            ${isActive ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/attractions/${attraction.id}/edit" class="btn btn-sm btn-primary btn-action">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="deleteAttraction(${attraction.id})" class="btn btn-sm btn-danger btn-action">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading attractions:', xhr, status, error);
                        let errorMsg = 'Error loading attractions';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                errorMsg = errorData.message || errorMsg;
                            } catch (e) {
                                errorMsg = xhr.responseText.substring(0, 100);
                            }
                        }
                        $('#attractions-table tbody').html(`<tr><td colspan="8" class="text-center text-danger">${errorMsg}</td></tr>`);
                    }
                });
            }

            function deleteAttraction(id) {
                if (!confirm('Are you sure you want to delete this attraction?')) return;

                $.ajax({
                    url: `/admin/attractions/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        loadAttractions();
                        alert(response.message || 'Attraction deleted successfully');
                    },
                    error: function (xhr) {
                        let errorMsg = 'Error deleting attraction';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        } else if (xhr.responseText) {
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                errorMsg = errorData.error || errorData.message || errorMsg;
                            } catch (e) {
                                errorMsg = xhr.responseText.substring(0, 100);
                            }
                        }
                        console.error('Error deleting attraction:', xhr);
                        alert(errorMsg);
                    }
                });
            }
        </script>
    @endpush
@endsection