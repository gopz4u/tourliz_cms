@extends('layouts.admin')

@section('title', 'Global Search Results')

@section('content')
    <div class="page-header mb-4">
        <div>
            <h2><i class="bi bi-search me-2 text-primary"></i>Global Search Results</h2>
            <p class="text-muted mb-0">Search query: <strong class="text-dark">"{{ $q }}"</strong> &bull; Found {{ $totalCount }} matching records</p>
        </div>
    </div>

    <!-- Quick Search Box on Page -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded-3 p-3">
            <form action="{{ route('admin.search') }}" method="GET" class="row g-2 align-items-center">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-1" placeholder="Search by client name, title, quote ID, phone, email, or agency name..." value="{{ $q }}" required>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Search</button>
                </div>
            </form>
        </div>
    </div>

    @if($totalCount === 0)
        <!-- Empty State -->
        <div class="card border-0 shadow-sm my-5">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">No Results Found</h4>
                <p class="text-muted px-4" style="max-width: 500px; margin: 0 auto;">
                    We couldn't find any B2B, B2C, or Group proposals, client bookings, or agency partners matching <strong class="text-dark">"{{ $q }}"</strong>.
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house me-1"></i> Return Dashboard</a>
                </div>
            </div>
        </div>
    @else
        <!-- Results Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <ul class="nav nav-tabs card-header-tabs" id="searchResultTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-dark" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-results" type="button" role="tab" aria-controls="all-results" aria-selected="true">
                            All Matches ({{ $totalCount }})
                        </button>
                    </li>
                    @if($b2bResults->isNotEmpty())
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-info" id="b2b-tab" data-bs-toggle="tab" data-bs-target="#b2b-results" type="button" role="tab" aria-controls="b2b-results" aria-selected="false">
                                B2B Proposals ({{ $b2bResults->count() }})
                            </button>
                        </li>
                    @endif
                    @if($b2cResults->isNotEmpty())
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-success" id="b2c-tab" data-bs-toggle="tab" data-bs-target="#b2c-results" type="button" role="tab" aria-controls="b2c-results" aria-selected="false">
                                B2C Proposals ({{ $b2cResults->count() }})
                            </button>
                        </li>
                    @endif
                    @if($groupResults->isNotEmpty())
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-warning" id="group-tab" data-bs-toggle="tab" data-bs-target="#group-results" type="button" role="tab" aria-controls="group-results" aria-selected="false">
                                Group Proposals ({{ $groupResults->count() }})
                            </button>
                        </li>
                    @endif
                    @if($bookingResults->isNotEmpty())
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-primary" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking-results" type="button" role="tab" aria-controls="booking-results" aria-selected="false">
                                Bookings ({{ $bookingResults->count() }})
                            </button>
                        </li>
                    @endif
                    @if($agencyResults->isNotEmpty())
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-secondary" id="agency-tab" data-bs-toggle="tab" data-bs-target="#agency-results" type="button" role="tab" aria-controls="agency-results" aria-selected="false">
                                Agencies ({{ $agencyResults->count() }})
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="card-body p-3">
                <div class="tab-content" id="searchResultTabContent">
                    
                    <!-- TAB: ALL RESULTS -->
                    <div class="tab-pane fade show active" id="all-results" role="tabpanel" aria-labelledby="all-tab">
                        
                        <!-- B2B Results Summary -->
                        @if($b2bResults->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-2">
                                <h6 class="fw-bold mb-0 text-info"><i class="bi bi-file-earmark-text me-2"></i> B2B Proposals</h6>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="150">Quote ID</th>
                                            <th>Proposal / Client</th>
                                            <th>Agency</th>
                                            <th>Destination / Pax</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($b2bResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                                    <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $item->client_name ?? 'Guest' }}</small>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ $item->agency->company_name ?? 'N/A' }}</span></td>
                                                <td>
                                                    <div>{{ $item->destination->name ?? 'N/A' }} &bull; {{ $item->duration_days }} Days</div>
                                                    <small class="text-muted">{{ $item->adults }}A + {{ ($item->children_2_6 ?? 0) + ($item->children_6_11 ?? 0) }}C</small>
                                                </td>
                                                <td><div class="fw-bold text-primary">{{ $item->currency }} {{ number_format($item->total_price, 2) }}</div></td>
                                                <td>
                                                    <span class="badge bg-outline-info text-info border border-info px-2" style="font-size: 0.65rem;">{{ strtoupper($item->status ?? 'DRAFT') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.b2b-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="{{ route('admin.b2b-itineraries.pdf', $item->id) }}" class="btn btn-sm btn-outline-success" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- B2C Results Summary -->
                        @if($b2cResults->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-success"><i class="bi bi-person me-2"></i> B2C Proposals</h6>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="150">Quote ID</th>
                                            <th>Client Info</th>
                                            <th>Trip Details</th>
                                            <th>Destination / Pax</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($b2cResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->client_name }}</div>
                                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $item->phone ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                                    <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $item->start_date ? $item->start_date->format('d M Y') : 'TBD' }}</small>
                                                </td>
                                                <td>
                                                    <div>{{ $item->destination->name ?? 'N/A' }} &bull; {{ $item->duration_days }} Days</div>
                                                    <small class="text-muted">{{ $item->adults }}A + {{ ($item->children_2_6 ?? 0) + ($item->children_6_11 ?? 0) }}C</small>
                                                </td>
                                                <td><div class="fw-bold text-success">{{ $item->currency }} {{ number_format($item->total_price, 2) }}</div></td>
                                                <td>
                                                    <span class="badge bg-outline-success text-success border border-success px-2" style="font-size: 0.65rem;">{{ strtoupper($item->status ?? 'DRAFT') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.b2c-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="{{ route('admin.b2c-itineraries.pdf', $item->id) }}" class="btn btn-sm btn-outline-success" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Group Results Summary -->
                        @if($groupResults->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-warning"><i class="bi bi-people me-2"></i> Group Proposals</h6>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="150">Quote ID</th>
                                            <th>Client Info</th>
                                            <th>Trip Details</th>
                                            <th>Destination / Pax</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->client_name }}</div>
                                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $item->phone ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->title }}</div>
                                                    <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $item->start_date ? $item->start_date->format('d M Y') : 'TBD' }}</small>
                                                </td>
                                                <td>
                                                    <div>{{ $item->destination->name ?? 'N/A' }} &bull; {{ $item->duration_days }} Days</div>
                                                    <small class="text-muted">{{ $item->adults }}A + {{ ($item->children_2_6 ?? 0) + ($item->children_6_11 ?? 0) }}C</small>
                                                </td>
                                                <td><div class="fw-bold text-warning">{{ $item->currency }} {{ number_format($item->total_price, 2) }}</div></td>
                                                <td>
                                                    <span class="badge bg-outline-warning text-warning border border-warning px-2" style="font-size: 0.65rem;">{{ strtoupper($item->status ?? 'DRAFT') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.group-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="{{ route('admin.group-itineraries.pdf', $item->id) }}" class="btn btn-sm btn-outline-success" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Booking Results Summary -->
                        @if($bookingResults->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-check me-2"></i> Bookings</h6>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="150">Booking ID</th>
                                            <th>Customer Info</th>
                                            <th>Package Name</th>
                                            <th>Travel Date</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookingResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->customer_name ?? $item->name }}</div>
                                                    <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $item->customer_email ?? $item->email ?? 'N/A' }}</small>
                                                </td>
                                                <td><div class="fw-bold text-dark">{{ $item->package->name ?? 'Custom Package' }}</div></td>
                                                <td>
                                                    <div>{{ $item->travel_date ? $item->travel_date->format('d M Y') : 'N/A' }}</div>
                                                    <small class="text-muted">{{ ($item->adults ?? 1) + ($item->children ?? 0) }} Pax</small>
                                                </td>
                                                <td><div class="fw-bold text-primary">{{ $item->currency }} {{ number_format($item->total_amount ?? $item->price ?? 0, 2) }}</div></td>
                                                <td>
                                                    <span class="badge bg-outline-primary text-primary border border-primary px-2" style="font-size: 0.65rem;">{{ strtoupper($item->status ?? 'PENDING') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.bookings.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Agency Results Summary -->
                        @if($agencyResults->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom mt-4">
                                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shop-window me-2"></i> Agencies</h6>
                            </div>
                            <div class="table-responsive mb-2">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="80">ID</th>
                                            <th>Company Name</th>
                                            <th>Primary Contact</th>
                                            <th>WhatsApp / Phone</th>
                                            <th>Website / Details</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($agencyResults as $item)
                                            <tr>
                                                <td><code>#{{ $item->id }}</code></td>
                                                <td><div class="fw-bold text-dark">{{ $item->company_name }}</div></td>
                                                <td><div>{{ $item->primary_contact_name ?? 'N/A' }}</div></td>
                                                <td><span class="badge bg-light text-dark border">{{ $item->whatsapp_number ?? 'N/A' }}</span></td>
                                                <td>
                                                    @if($item->website)
                                                        <a href="{{ $item->website }}" target="_blank" class="small text-decoration-none">Website</a>
                                                    @else
                                                        <span class="text-muted small">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->is_active)
                                                        <span class="badge bg-success" style="font-size: 0.65rem;">ACTIVE</span>
                                                    @else
                                                        <span class="badge bg-danger" style="font-size: 0.65rem;">INACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.agencies.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>

                    <!-- TAB: B2B DETAILS -->
                    @if($b2bResults->isNotEmpty())
                        <div class="tab-pane fade" id="b2b-results" role="tabpanel" aria-labelledby="b2b-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Quote ID</th>
                                            <th>Title</th>
                                            <th>Client Name</th>
                                            <th>Agency Partner</th>
                                            <th>Destination</th>
                                            <th>Price</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($b2bResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td><div class="fw-bold text-dark">{{ $item->title }}</div></td>
                                                <td>{{ $item->client_name ?? 'Guest' }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $item->agency->company_name ?? 'N/A' }}</span></td>
                                                <td>{{ $item->destination->name ?? 'N/A' }} ({{ $item->duration_days }} Days)</td>
                                                <td><span class="fw-bold text-primary">{{ $item->currency }} {{ number_format($item->total_price, 2) }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.b2b-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="{{ route('admin.b2b-itineraries.pdf', $item->id) }}" class="btn btn-sm btn-outline-success" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- TAB: B2C DETAILS -->
                    @if($b2cResults->isNotEmpty())
                        <div class="tab-pane fade" id="b2c-results" role="tabpanel" aria-labelledby="b2c-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Quote ID</th>
                                            <th>Client Name</th>
                                            <th>Title / Description</th>
                                            <th>Contact Phone</th>
                                            <th>Destination</th>
                                            <th>Quote Price</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($b2cResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td><div class="fw-bold text-dark">{{ $item->client_name }}</div></td>
                                                <td>{{ $item->title }}</td>
                                                <td>{{ $item->phone ?? 'N/A' }}</td>
                                                <td>{{ $item->destination->name ?? 'N/A' }} ({{ $item->duration_days }} Days)</td>
                                                <td><span class="fw-bold text-success">{{ $item->currency }} {{ number_format($item->total_price, 2) }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.b2c-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="{{ route('admin.b2c-itineraries.pdf', $item->id) }}" class="btn btn-sm btn-outline-success" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- TAB: GROUP DETAILS -->
                    @if($groupResults->isNotEmpty())
                        <div class="tab-pane fade" id="group-results" role="tabpanel" aria-labelledby="group-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Quote ID</th>
                                            <th>Client Name</th>
                                            <th>Title</th>
                                            <th>Contact Info</th>
                                            <th>Destination</th>
                                            <th>Quote Price</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td><div class="fw-bold text-dark">{{ $item->client_name }}</div></td>
                                                <td>{{ $item->title }}</td>
                                                <td>{{ $item->phone ?? 'N/A' }}</td>
                                                <td>{{ $item->destination->name ?? 'N/A' }} ({{ $item->duration_days }} Days)</td>
                                                <td><span class="fw-bold text-warning">{{ $item->currency }} {{ number_format($item->total_price, 2) }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.group-itineraries.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="{{ route('admin.group-itineraries.pdf', $item->id) }}" class="btn btn-sm btn-outline-success" title="PDF"><i class="bi bi-file-pdf"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- TAB: BOOKINGS DETAILS -->
                    @if($bookingResults->isNotEmpty())
                        <div class="tab-pane fade" id="booking-results" role="tabpanel" aria-labelledby="booking-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Customer Name</th>
                                            <th>Package Booked</th>
                                            <th>Travel Date</th>
                                            <th>Price Paid</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookingResults as $item)
                                            <tr>
                                                <td><code class="fw-bold">{{ $item->quote_id }}</code></td>
                                                <td><div class="fw-bold text-dark">{{ $item->customer_name ?? $item->name }}</div></td>
                                                <td>{{ $item->package->name ?? 'Custom Package' }}</td>
                                                <td>{{ $item->travel_date ? $item->travel_date->format('d M Y') : 'N/A' }}</td>
                                                <td><span class="fw-bold text-primary">{{ $item->currency }} {{ number_format($item->total_amount ?? $item->price ?? 0, 2) }}</span></td>
                                                <td><span class="badge bg-outline-primary text-primary border border-primary px-2" style="font-size: 0.65rem;">{{ strtoupper($item->status ?? 'PENDING') }}</span></td>
                                                <td class="text-end pe-3">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.bookings.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- TAB: AGENCIES DETAILS -->
                    @if($agencyResults->isNotEmpty())
                        <div class="tab-pane fade" id="agency-results" role="tabpanel" aria-labelledby="agency-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Agency Name</th>
                                            <th>Primary Contact</th>
                                            <th>WhatsApp / Phone</th>
                                            <th>Website</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($agencyResults as $item)
                                            <tr>
                                                <td><code>#{{ $item->id }}</code></td>
                                                <td><div class="fw-bold text-dark">{{ $item->company_name }}</div></td>
                                                <td>{{ $item->primary_contact_name ?? 'N/A' }}</td>
                                                <td>{{ $item->whatsapp_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if($item->website)
                                                        <a href="{{ $item->website }}" target="_blank" class="small text-decoration-none">{{ $item->website }}</a>
                                                    @else
                                                        <span class="text-muted small">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->is_active)
                                                        <span class="badge bg-success" style="font-size: 0.65rem;">ACTIVE</span>
                                                    @else
                                                        <span class="badge bg-danger" style="font-size: 0.65rem;">INACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.agencies.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif
@endsection
