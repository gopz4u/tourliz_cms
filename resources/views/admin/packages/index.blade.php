@extends('layouts.admin')

@section('title', 'Manage Packages')

@push('styles')
<style>
    .pkg-card {
        border: none;
        border-radius: 24px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: white;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .pkg-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
    .pkg-img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    .pkg-img {
        height: 100%;
        object-fit: cover;
        width: 100%;
        transition: transform 0.6s ease;
    }
    .pkg-card:hover .pkg-img {
        transform: scale(1.1);
    }
    .pkg-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 10;
        backdrop-filter: blur(12px);
        background: rgba(255,255,255,0.9);
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 800;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pkg-badge .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .stat-card {
        background: white;
        border-radius: 24px;
        padding: 1.8rem;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        height: 100%;
    }
    .filter-pill {
        padding: 10px 24px;
        border-radius: 16px;
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    .filter-pill:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .filter-pill.active {
        background: #0052cc;
        color: white;
        border-color: #0052cc;
        box-shadow: 0 10px 20px rgba(0, 82, 204, 0.2);
    }
    .pkg-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-black text-dark mb-1">Package Inventory</h2>
            <p class="text-muted small mb-0">Curating the world's finest experiences</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Create New Package
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary-light text-primary">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
                <h3 class="fw-black mb-0" id="stat-total">0</h3>
                <p class="text-muted small mb-0">Total Packages</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success-light text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h3 class="fw-black mb-0" id="stat-active">0</h3>
                <p class="text-muted small mb-0">Active & Published</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning-light text-warning">
                    <i class="bi bi-star-fill"></i>
                </div>
                <h3 class="fw-black mb-0" id="stat-featured">0</h3>
                <p class="text-muted small mb-0">Featured Experience</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger-light text-danger">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h3 class="fw-black mb-0" id="stat-revenue">₹0</h3>
                <p class="text-muted small mb-0">Total Potential Revenue</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-4 p-3 mb-4 shadow-sm border">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex gap-2 flex-wrap" id="category-filters">
                    <button class="filter-pill active" data-filter="all">All Packages</button>
                    <button class="filter-pill" data-filter="Honeymoon">Honeymoon</button>
                    <button class="filter-pill" data-filter="Budget">Budget</button>
                    <button class="filter-pill" data-filter="Premium">Premium</button>
                    <button class="filter-pill" data-filter="Standard">Standard</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill px-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="pkg-search" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Search by name or destination...">
                </div>
            </div>
        </div>
    </div>

    <!-- Package Grid -->
    <div class="row g-4" id="package-grid">
        <!-- Will be populated by AJAX -->
        <div class="col-12 text-center py-5" id="loader">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Loading your inventory...</p>
        </div>
    </div>
</div>

<!-- Package Card Template -->
<template id="pkg-card-template">
    <div class="col-md-4 col-lg-3 pkg-item" data-category="{CATEGORY}">
        <div class="pkg-card">
            <div class="pkg-img-wrapper">
                <div class="pkg-badge">{STATUS}</div>
                <img src="{IMAGE}" class="pkg-img" alt="{NAME}">
            </div>
            <div class="pkg-content">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2 small fw-bold">{DURATION}</span>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-2">
                            <li><a class="dropdown-item rounded-3 py-2" href="/admin/packages/{ID}/edit"><i class="bi bi-pencil me-2"></i> Edit Package</a></li>
                            <li><button class="dropdown-item rounded-3 py-2 text-primary" onclick="duplicatePackage({ID})"><i class="bi bi-files me-2"></i> Duplicate</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item rounded-3 py-2 text-danger" onclick="deletePackage({ID})"><i class="bi bi-trash me-2"></i> Delete</button></li>
                        </ul>
                    </div>
                </div>
                <h6 class="fw-bold text-dark mb-1 text-truncate">{NAME}</h6>
                <p class="text-muted small mb-4 mt-auto">
                    <i class="bi bi-geo-alt-fill text-primary me-1"></i> {COUNTRY} • {DESTINATION}
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted x-small mb-0">Starting at</p>
                        <h5 class="fw-black text-primary mb-0">₹{PRICE}</h5>
                    </div>
                    <div class="text-end">
                        <div class="small text-warning mb-1">
                            {RATING_STARS}
                        </div>
                        <span class="x-small text-muted">{REVIEWS} reviews</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        loadPackages();

        // Search
        $('#pkg-search').on('input', function() {
            let val = $(this).val().toLowerCase();
            $('.pkg-item').each(function() {
                let text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(val) > -1);
            });
        });

        // Category Filter
        $('.filter-pill').click(function() {
            $('.filter-pill').removeClass('active');
            $(this).addClass('active');
            let filter = $(this).data('filter');
            
            if(filter === 'all') {
                $('.pkg-item').fadeIn();
            } else {
                $('.pkg-item').hide();
                $(`.pkg-item[data-category="${filter}"]`).fadeIn();
            }
        });
    });

    function loadPackages() {
        console.log('Fetching packages from:', '{{ route("admin.packages.index") }}');
        
        $.get('{{ route("admin.packages.index") }}?_t=' + Date.now())
            .done(function(res) {
                console.log('Server response:', res);
                let packages = res.data || res;
                let container = $('#package-grid');
                container.empty();

                if(!packages || packages.length === 0) {
                    container.html(`
                        <div class="col-12 text-center py-5">
                            <div class="bg-light p-5 rounded-4 d-inline-block">
                                <i class="bi bi-inbox text-muted fs-1"></i>
                                <p class="text-muted mt-3 mb-0">No packages found in your inventory.</p>
                                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary btn-sm mt-3 rounded-pill px-4">Create First Package</a>
                            </div>
                        </div>
                    `);
                    return;
                }

                let template = $('#pkg-card-template').html();
                let totalRev = 0, activeCount = 0, featuredCount = 0;

                packages.forEach(pkg => {
                    if(pkg.status === 'active') activeCount++;
                    if(pkg.featured) featuredCount++;
                    totalRev += parseFloat(pkg.price) || 0;

                    let destNames = pkg.destinations && pkg.destinations.length > 0 
                        ? pkg.destinations.map(d => d.name).join(', ') 
                        : (pkg.destination ? pkg.destination.name : 'Various');

                    let html = template
                        .replace(/{ID}/g, pkg.id)
                        .replace(/{NAME}/g, pkg.name)
                        .replace(/{IMAGE}/g, pkg.image ? '/' + pkg.image : 'https://placehold.co/600x400?text=' + encodeURIComponent(pkg.name))
                        .replace(/{COUNTRY}/g, pkg.country ? pkg.country.name : 'Intl')
                        .replace(/{DESTINATION}/g, destNames)
                        .replace(/{PRICE}/g, pkg.price ? parseFloat(pkg.price).toLocaleString() : '0')
                        .replace(/{DURATION}/g, pkg.duration || 'Flexible')
                        .replace(/{CATEGORY}/g, pkg.package_category || 'Standard')
                        .replace(/{STATUS}/g, pkg.status === 'active' 
                        ? '<div class="status-dot bg-success"></div> Live' 
                        : '<div class="status-dot bg-secondary"></div> Draft')
                        .replace(/{REVIEWS}/g, pkg.reviews_count || 0)
                        .replace(/{RATING_STARS}/g, Array(5).fill().map((_, i) => `<i class="bi bi-star${i < (pkg.average_rating || 5) ? '-fill' : ''}"></i>`).join(''));

                    container.append(html);
                });

                $('#stat-total').text(packages.length);
                $('#stat-active').text(activeCount);
                $('#stat-featured').text(featuredCount);
                $('#stat-revenue').text('₹' + totalRev.toLocaleString());
            })
            .fail(function(err) {
                console.error('AJAX Error:', err);
                $('#package-grid').html(`
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-danger d-inline-block rounded-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                            Error loading inventory. Please check server logs.
                        </div>
                    </div>
                `);
            });
    }

    function deletePackage(id) {
        Swal.fire({
            title: 'Delete Experience?',
            text: "This action will soft-delete the package. You can force-delete it later to remove all data permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, remove it!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: `/admin/packages/${id}`,
                    type: 'DELETE'
                }).catch(err => {
                    Swal.showValidationMessage(
                        `Request failed: ${err.responseJSON?.message || err.statusText}`
                    );
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Deleted!', 'Package has been removed from active inventory.', 'success');
                loadPackages();
            }
        })
    }

    function duplicatePackage(id) {
        Swal.fire({
            title: 'Duplicate Package?',
            text: "This will create a draft copy of this experience.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, duplicate',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.post(`/admin/packages/${id}/duplicate`)
                    .catch(err => {
                        Swal.showValidationMessage(
                            `Request failed: ${err.responseJSON?.message || err.statusText}`
                        );
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Success!', 'Copy created successfully.', 'success');
                loadPackages();
            }
        })
    }
</script>
@endpush
