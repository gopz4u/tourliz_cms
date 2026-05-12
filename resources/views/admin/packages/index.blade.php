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

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="filter-destination" class="form-label"><i class="bi bi-funnel"></i> Filter by Destination</label>
                    <select class="form-select" id="filter-destination">
                        <option value="">All Destinations</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="packages-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Destination</th>
                            <th>Category</th>
                            <th>Amenities</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Rating</th>
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

                // Filter by destination
                $('#filter-destination').on('change', function () {
                    loadPackages();
                });
            });

            function loadDestinations() {
                $.get('{{ route("admin.destinations.index") }}', function (data) {
                    const destinations = data.data || data;
                    const destinationSelect = $('#filter-destination');
                    destinationSelect.find('option:not(:first)').remove();

                    if (Array.isArray(destinations)) {
                        destinations.forEach(function (destination) {
                            destinationSelect.append(`<option value="${destination.id}">${destination.name}</option>`);
                        });
                    }
                }).fail(function () {
                    console.error('Failed to load destinations');
                });
            }

            function loadPackages() {
                const destinationId = $('#filter-destination').val();
                const url = destinationId ? '{{ route("admin.packages.index") }}?destination_id=' + destinationId : '{{ route("admin.packages.index") }}';

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        const tbody = $('#packages-table tbody');
                        tbody.empty();

                        // Handle paginated response
                        let packages = [];
                        if (response && response.data && Array.isArray(response.data)) {
                            packages = response.data;
                        } else if (Array.isArray(response)) {
                            packages = response;
                        }

                        if (packages.length === 0) {
                            tbody.append('<tr><td colspan="10" class="text-center">No packages found</td></tr>');
                            return;
                        }

                        packages.forEach(function (pkg) {
                            // Parse duration from string or use days/nights
                            let duration = '-';
                            if (pkg.duration_days || pkg.duration_nights) {
                                duration = `${pkg.duration_days || 0}D/${pkg.duration_nights || 0}N`;
                            } else if (pkg.duration) {
                                duration = pkg.duration;
                            }

                            // Build amenities display based on category
                            let amenitiesHtml = '-';
                            let categoryHtml = pkg.category ? `<span class="badge bg-info">${pkg.category}</span>` : '-';
                            let packageCategoryHtml = pkg.package_category ? `<span class="badge bg-primary ms-1">${pkg.package_category}</span>` : '';
                            categoryHtml = categoryHtml + packageCategoryHtml;

                            if (pkg.category === 'Hotels') {
                                const amenities = [];
                                if (pkg.star_rating) {
                                    amenities.push(`<i class="bi bi-star-fill text-warning"></i> ${pkg.star_rating} Star${pkg.star_rating > 1 ? 's' : ''}`);
                                }
                                if (pkg.accommodation_type) {
                                    amenities.push(pkg.accommodation_type);
                                }
                                amenitiesHtml = amenities.length > 0 ? amenities.join(' | ') : '-';
                            } else if (pkg.category === 'Transport' || pkg.category === 'Airport Pickup' || pkg.category === 'Airport Drop') {
                                amenitiesHtml = pkg.vehicle_type || '-';
                            } else if (pkg.category === 'Entry Tickets') {
                                const ticketInfo = [];
                                if (pkg.ticket_name) {
                                    ticketInfo.push(pkg.ticket_name);
                                }
                                if (pkg.ticket_count) {
                                    ticketInfo.push(`Count: ${pkg.ticket_count}`);
                                }
                                amenitiesHtml = ticketInfo.length > 0 ? ticketInfo.join(' - ') : '-';
                            }

                            const placeName = (pkg.place && pkg.place.name) ? pkg.place.name : '<span class="text-muted">-</span>';
                            const price = pkg.price || 0;
                            const discountPrice = pkg.discount_price || null;
                            const currency = pkg.currency || 'INR';
                            const currencySymbols = {
                                'USD': '$',
                                'INR': '₹',
                                'MYR': 'RM',
                                'SGD': 'S$',
                                'AED': 'AED'
                            };
                            const currencySymbol = currencySymbols[currency] || currency;
                            const isActive = pkg.is_active !== undefined ? pkg.is_active : (pkg.status !== undefined ? pkg.status : true);
                            const isFeatured = pkg.is_featured !== undefined ? pkg.is_featured : (pkg.featured || false);

                            const row = `
                                                <tr>
                                                    <td>${pkg.id}</td>
                                                    <td><strong>${pkg.name || 'Unnamed'}</strong></td>
                                                    <td>${placeName}</td>
                                                    <td>${categoryHtml}</td>
                                                    <td><small>${amenitiesHtml}</small></td>
                                                    <td>${currencySymbol} ${price}${discountPrice ? ' <small class="text-muted">(Was ${currencySymbol}${discountPrice})</small>' : ''}</td>
                                                    <td>${duration}<br><small class="text-muted">Pax: ${pkg.min_pax || 1}-${pkg.max_pax || pkg.total_pax || '∞'}</small></td>
                                                    <td>
                                                        <span class="badge ${isActive ? 'bg-success' : 'bg-secondary'} badge-status">
                                                            ${isActive ? 'Active' : 'Inactive'}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="text-warning small">
                                                            ${'★'.repeat(Math.floor(pkg.average_rating))}${'☆'.repeat(5 - Math.floor(pkg.average_rating))}
                                                            <span class="text-muted ms-1">(${pkg.reviews_count})</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        ${isFeatured ? '<span class="badge bg-warning">Featured</span>' : '-'}
                                                    </td>
                                                    <td>
                                                        <a href="/admin/itineraries/${pkg.id}/edit" class="btn btn-sm btn-info btn-action text-white" title="Manage Itinerary">
                                                             <i class="bi bi-calendar3"></i>
                                                         </a>
                                                         <button onclick="duplicatePackage(${pkg.id})" class="btn btn-sm btn-secondary btn-action" title="Duplicate Package">
                                                             <i class="bi bi-files"></i>
                                                         </button>
                                                         <a href="/admin/packages/${pkg.id}/edit" class="btn btn-sm btn-primary btn-action">
                                                             <i class="bi bi-pencil"></i>
                                                         </a>
                                                        <a href="/book/package/${pkg.id}" class="btn btn-sm btn-success btn-action">
                                                            <i class="bi bi-ticket"></i>
                                                        </a>
                                                        <button onclick="deletePackage(${pkg.id})" class="btn btn-sm btn-danger btn-action">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `;
                            tbody.append(row);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading packages:', xhr, status, error);
                        let errorMsg = 'Error loading packages';
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
                        $('#packages-table tbody').html(`<tr><td colspan="10" class="text-center text-danger">${errorMsg}</td></tr>`);
                    }
                });
            }

            function deletePackage(id) {
                if (!confirm('Are you sure you want to delete this package?')) return;

                $.ajax({
                    url: `/admin/packages/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        loadPackages();
                        alert(response.message || 'Package deleted successfully');
                    },
                    error: function (xhr) {
                        let errorMsg = 'Error deleting package';
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
                        console.error('Error deleting package:', xhr);
                        alert(errorMsg);
                    }
                });
            }

            function duplicatePackage(id) {
                if (!confirm('Are you sure you want to duplicate this package?')) return;

                $.ajax({
                    url: `/admin/packages/${id}/duplicate`,
                    type: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        loadPackages();
                        alert(response.message || 'Package duplicated successfully');
                    },
                    error: function (xhr) {
                        let errorMsg = 'Error duplicating package';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        }
                        alert(errorMsg);
                    }
                });
            }
        </script>
    @endpush
@endsection