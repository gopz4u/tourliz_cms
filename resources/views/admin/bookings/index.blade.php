@extends('layouts.admin')

@section('content')
    <style>
        .bookings-scroll-container {
            max-height: 75vh;
            overflow-y: auto;
        }

        .list-group-item {
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .bg-purple {
            background-color: #6f42c1 !important;
        }

        @media (max-width: 768px) {
            .bookings-scroll-container {
                max-height: none;
            }
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">All Bookings</h4>
            <small class="text-muted">Total: <strong>{{ $totalCount ?? 0 }}</strong> bookings | Pending:
                <strong>{{ $newCount ?? 0 }}</strong> new</small>
        </div>
        <div>
            <span class="badge bg-primary me-2">New: {{ $newCount ?? 0 }}</span>
            <span class="badge bg-info me-2">Recent (2d): {{ $recentCount ?? 0 }}</span>
            <span class="badge bg-success me-2">Total: {{ $totalCount ?? 0 }}</span>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Create Booking
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header bg-white pb-0 border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ !request('source') ? 'active fw-bold' : '' }}"
                        href="{{ route('admin.bookings.index') }}">
                        All Channels
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('source') == 'tourliz-website' ? 'active fw-bold' : '' }}"
                        href="{{ route('admin.bookings.index', array_merge(request()->except('source', 'page'), ['source' => 'tourliz-website'])) }}">
                        <i class="bi bi-globe me-1"></i> Website
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('source') == 'mobile-app' ? 'active fw-bold' : '' }}"
                        href="{{ route('admin.bookings.index', array_merge(request()->except('source', 'page'), ['source' => 'mobile-app'])) }}">
                        <i class="bi bi-phone me-1"></i> Mobile App
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body bg-light mt-0 border-top-0 rounded-bottom">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
                <input type="hidden" name="source" value="{{ request('source') }}">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="Name, Email, Phone, BK-ID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Booking Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="">All Payment Statuses</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>
                            Partially Paid</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded
                        </option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="bookings-scroll-container">
                <div class="list-group list-group-flush">
                    @forelse($bookings as $booking)
                        @php
                            $paymentDetails = $booking->payment_details ?? [];
                            $paymentMethod = $paymentDetails['method'] ?? null;
                            $paymentDate = $paymentDetails['date'] ?? null;
                            $paymentStatus = $booking->payment_status ?? 'pending';
                            $packagePrice = $booking->package->price ?? 0;
                            $discountPrice = $booking->package->discount_price ?? null;
                            $totalAmount = $booking->total_amount ?? $booking->price ?? $packagePrice;
                            $currency = $booking->currency ?? ($booking->package ? $booking->package->currency : null) ?? 'USD';
                            $bookingType = $booking->booking_type ?? 'General';

                            $paymentStatusBadge = [
                                'paid' => 'bg-success',
                                'partially_paid' => 'bg-info',
                                'pending' => 'bg-warning',
                                'refunded' => 'bg-secondary',
                            ][$paymentStatus] ?? 'bg-warning';

                            $bookingTypeBadge = [
                                'quote' => 'bg-secondary',
                                'package' => 'bg-primary',
                                'custom' => 'bg-dark',
                            ][$bookingType] ?? 'bg-secondary';

                            $bookingStatusBadge = [
                                'pending' => 'bg-warning text-dark',
                                'confirmed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                            ][$booking->status] ?? 'bg-secondary';

                            $followupStatus = $booking->followup_status ?? 'leads';
                            $followupBadge = [
                                'leads' => 'bg-primary-subtle text-primary border border-primary',
                                'followed_up' => 'bg-info text-dark',
                                'waiting' => 'bg-warning text-dark',
                                'interested' => 'bg-info-subtle text-info border border-info',
                                'not_interested' => 'bg-danger-subtle text-danger border border-danger',
                                'converted' => 'bg-success',
                                'dead' => 'bg-dark',
                            ][$followupStatus] ?? 'bg-secondary';

                            // Follow-up reminder logic
                            $followupDate = $booking->next_followup_date;
                            $followupClass = 'text-muted';
                            if ($followupDate) {
                                $today = now()->startOfDay();
                                $date = $followupDate->startOfDay();
                                if ($date->lt($today)) {
                                    $followupClass = 'text-danger fw-bold'; // Overdue
                                } elseif ($date->eq($today)) {
                                    $followupClass = 'text-warning fw-bold'; // Today
                                } else {
                                    $followupClass = 'text-primary'; // Future
                                }
                            }

                            // Customer display variables
                            $displayName = $booking->name ?: $booking->customer_name ?: 'N/A';
                            $displayEmail = $booking->email ?: $booking->customer_email ?: 'N/A';
                            $displayPhone = $booking->phone ?: $booking->customer_phone ?: 'N/A';
                            $location = implode(', ', array_filter([$booking->customer_city, $booking->customer_state, $booking->customer_country]));

                            // Warning highlight logic: Created > 2 days ago AND not followed up (status 'leads')
                            $isOverdueForFollowup = $booking->created_at->lt(now()->subDays(2)) && ($booking->followup_status === 'leads' || $booking->status === 'pending');
                            $rowClass = $isOverdueForFollowup ? 'bg-danger-subtle border-danger' : '';
                        @endphp
                        <div class="list-group-item border-0 border-bottom p-3 {{ $rowClass }}">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="text-muted small mb-1">#{{ $bookings->firstItem() + $loop->index }}</div>
                                    <div class="fw-bold text-primary">{{ $displayName }}</div>
                                    <div class="small text-muted">ID: {{ $booking->quote_id }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="small text-muted mb-1">Phone</div>
                                    @if($displayPhone !== 'N/A')
                                        <a href="tel:{{ $displayPhone }}" class="text-decoration-none">
                                            <i class="bi bi-phone"></i> {{ $displayPhone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <div class="small text-muted mb-1">Package</div>
                                    <div class="fw-bold">{{ $booking->package->name ?? 'N/A' }}</div>
                                    @if($booking->package && $booking->package->destination)
                                        <div class="small text-muted">{{ $booking->package->destination->name }}</div>
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    <div class="small text-muted mb-1">Status</div>
                                    <span class="badge {{ $bookingStatusBadge }} d-block mb-1">{{ ucfirst($booking->status) }}</span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ ucfirst($bookingType) }}</small>
                                </div>
                                <div class="col-md-2">
                                    <div class="small text-muted mb-1">Payment</div>
                                    <span
                                        class="badge {{ $paymentStatusBadge }}">{{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}</span>
                                    @if($totalAmount)
                                        <div class="small text-muted mt-1">{{ $currency }} {{ number_format($totalAmount, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <div class="small text-muted mb-1">Follow-up</div>
                                    <div class="{{ $followupClass }}">
                                        <strong>{{ $followupDate ? $followupDate->format('M d, Y') : 'Not Set' }}</strong>
                                    </div>
                                    <div class="small mt-1">
                                        <span
                                            class="badge {{ $followupBadge }}">{{ ucfirst(str_replace('_', ' ', $followupStatus)) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#bookingModal{{ $booking->id }}" title="View Details">
                                        <i class="bi bi-eye"></i> Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details Modal -->
                        <div class="modal fade" id="bookingModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Booking Details:
                                            {{ $booking->quote_id }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Follow-up Date Section (Prominent) -->
                                        <div
                                            class="alert alert-{{ $followupDate && $followupDate->lt(now()) ? 'danger' : ($followupDate && $followupDate->eq(now()->startOfDay()) ? 'warning' : 'info') }} mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Follow-up
                                                        Information</h6>
                                                    <div class="mb-2">
                                                        <strong>Next Follow-up Date:</strong>
                                                        <span class="badge {{ $followupBadge }} fs-6 ms-2">
                                                            {{ $followupDate ? $followupDate->format('M d, Y') : 'Not Set' }}
                                                        </span>
                                                    </div>
                                                    <div class="small">
                                                        <strong>Follow-up Status:</strong>
                                                        <span
                                                            class="badge {{ $followupBadge }}">{{ ucfirst(str_replace('_', ' ', $followupStatus)) }}</span>
                                                        @if($booking->followed_up_at)
                                                            <span class="ms-2">Last followed:
                                                                {{ $booking->followed_up_at->format('M d, Y') }}
                                                                ({{ $booking->followed_up_at->diffForHumans() }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <form action="{{ route('admin.bookings.updateStatus', $booking) }}"
                                                    method="POST" class="d-flex gap-2 followup-form"
                                                    data-booking-id="{{ $booking->id }}">
                                                    @csrf
                                                    <input type="date" name="next_followup_date"
                                                        class="form-control form-control-sm followup-date-input"
                                                        value="{{ $booking->next_followup_date ? $booking->next_followup_date->format('Y-m-d') : '' }}"
                                                        style="width: 150px;">
                                                    <button type="submit" class="btn btn-sm btn-light"><i
                                                            class="bi bi-save"></i></button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <!-- Customer Details -->
                                            <div class="col-md-6">
                                                <h6 class="border-bottom pb-2"><i class="bi bi-person-circle me-2"></i>Customer
                                                    Information</h6>
                                                <p class="mb-1"><strong>Created Date:</strong>
                                                    {{ $booking->created_at->format('M d, Y h:i A') }}</p>
                                                <p class="mb-1"><strong>Name:</strong> {{ $displayName }}</p>
                                                <p class="mb-1"><strong>Email:</strong>
                                                    @if($displayEmail !== 'N/A')
                                                        <a href="mailto:{{ $displayEmail }}">{{ $displayEmail }}</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </p>
                                                <p class="mb-1"><strong>Phone:</strong>
                                                    @if($displayPhone !== 'N/A')
                                                        <a href="tel:{{ $displayPhone }}">{{ $displayPhone }}</a>
                                                        @if($booking->whatsapp_number)
                                                            <span class="badge bg-success ms-2"><i class="bi bi-whatsapp"></i>
                                                                {{ $booking->whatsapp_number }}</span>
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </p>
                                                <p class="mb-1"><strong>Location:</strong> {{ $location ?: 'N/A' }}</p>
                                                <p class="mb-1"><strong>Address:</strong>
                                                    {{ $booking->address ?: $booking->customer_address ?: 'N/A' }}</p>
                                                @if($booking->customer_postal_code)
                                                    <p class="mb-1"><strong>Postal Code:</strong>
                                                        {{ $booking->customer_postal_code }}</p>
                                                @endif
                                                @if($booking->contact_method)
                                                    <p class="mb-0"><strong>Contact Method:</strong> <span
                                                            class="badge bg-info">{{ ucfirst($booking->contact_method) }}</span></p>
                                                @endif
                                            </div>

                                            <!-- Package Details -->
                                            <div class="col-md-6">
                                                <h6 class="border-bottom pb-2"><i class="bi bi-suitcase-lg me-2"></i>Package
                                                    Information</h6>
                                                <p class="mb-1"><strong>Package:</strong>
                                                    {{ $booking->package->name ?? 'Deleted Package' }}</p>
                                                <p class="mb-1"><strong>Destination:</strong>
                                                    {{ $booking->package && $booking->package->destination ? $booking->package->destination->name : 'N/A' }}
                                                </p>
                                                <p class="mb-1"><strong>Travel Date:</strong>
                                                    {{ $booking->travel_date ? $booking->travel_date->format('M d, Y') : 'N/A' }}
                                                    @if($booking->travel_date)
                                                        <span
                                                            class="text-muted">({{ $booking->travel_date->diffForHumans() }})</span>
                                                    @endif
                                                </p>
                                                <p class="mb-1"><strong>Duration:</strong>
                                                    {{ $booking->package->duration ?? 'N/A' }}</p>
                                                <p class="mb-1"><strong>Booking Type:</strong>
                                                    <span
                                                        class="badge {{ $bookingTypeBadge }}">{{ ucfirst($bookingType) }}</span>
                                                </p>
                                                <p class="mb-0"><strong>Pax:</strong>
                                                    <strong>{{ $booking->adults }}</strong> Adults
                                                    @if($booking->children > 0)
                                                        , <strong>{{ $booking->children }}</strong> Children
                                                    @endif
                                                    @if($booking->kids_2_6 > 0 || $booking->kids_6_10 > 0)
                                                        <br><small class="text-muted">
                                                            Kids 2-6y: {{ $booking->kids_2_6 }}, Kids 6-10y:
                                                            {{ $booking->kids_6_10 }}
                                                        </small>
                                                    @endif
                                                    <br><small class="text-muted">Total:
                                                        {{ $booking->adults + $booking->children + ($booking->kids_2_6 ?? 0) + ($booking->kids_6_10 ?? 0) }}
                                                        Pax</small>
                                                </p>
                                            </div>

                                            <!-- Pricing & Payment -->
                                            <div class="col-12">
                                                <h6 class="border-bottom pb-2 mt-3"><i class="bi bi-wallet2 me-2"></i>Payment
                                                    Details</h6>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="w-50">Package Price</td>
                                                                        <td class="text-end">{{ $currency }}
                                                                            {{ number_format($packagePrice, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    @if(($booking->addons_amount ?? 0) > 0)
                                                                        <tr>
                                                                            <td>Add-ons Total</td>
                                                                            <td class="text-end">{{ $currency }}
                                                                                {{ number_format($booking->addons_amount, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    @if(($booking->services_amount ?? 0) > 0)
                                                                        <tr>
                                                                            <td>Services</td>
                                                                            <td class="text-end">{{ $currency }}
                                                                                {{ number_format($booking->services_amount, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    @if(($booking->discount_amount ?? 0) > 0)
                                                                        <tr>
                                                                            <td class="text-success">Discount
                                                                                @if($booking->coupon_code) <span
                                                                                    class="badge bg-success-subtle text-success border border-success ms-1">{{ $booking->coupon_code }}</span>
                                                                                @endif</td>
                                                                            <td class="text-end text-success">- {{ $currency }}
                                                                                {{ number_format($booking->discount_amount, 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                    <tr class="table-light">
                                                                        <td><strong>Total Amount</strong></td>
                                                                        <td class="text-end"><strong>{{ $currency }}
                                                                                {{ number_format($totalAmount, 2) }}</strong>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <p class="mb-2"><strong>Payment Status:</strong></p>
                                                                <span
                                                                    class="badge {{ $paymentStatusBadge }} fs-6 mb-3 d-block">{{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}</span>
                                                                @if($paymentMethod)
                                                                    <p class="mb-1"><strong>Method:</strong>
                                                                        {{ ucfirst($paymentMethod) }}</p>
                                                                @endif
                                                                @if($paymentDate)
                                                                    <p class="mb-1"><strong>Date:</strong>
                                                                        {{ \Carbon\Carbon::parse($paymentDate)->format('M d, Y') }}
                                                                    </p>
                                                                @endif
                                                                @if(!empty($booking->payment_details['transaction_id']))
                                                                    <p class="mb-0"><strong>Transaction ID:</strong> <small
                                                                            class="text-break">{{ $booking->payment_details['transaction_id'] }}</small>
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Status Management -->
                                            <div class="col-12">
                                                <h6 class="border-bottom pb-2 mt-3"><i class="bi bi-gear-fill me-2"></i>Status
                                                    Management</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <form action="{{ route('admin.bookings.updateStatus', $booking) }}"
                                                            method="POST">
                                                            @csrf
                                                            <label class="form-label small">Booking Status</label>
                                                            @php
                                                                $statusClass = [
                                                                    'confirmed' => 'bg-success text-white',
                                                                    'pending' => 'bg-warning text-dark',
                                                                    'cancelled' => 'bg-danger text-white',
                                                                ][$booking->status] ?? 'bg-light';
                                                            @endphp
                                                            <select name="status" class="form-select mb-3 {{ $statusClass }}"
                                                                onchange="this.form.submit()">
                                                                <option value="pending" class="bg-white text-dark" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="confirmed" class="bg-white text-dark" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                                <option value="cancelled" class="bg-white text-dark" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <form action="{{ route('admin.bookings.updateStatus', $booking) }}"
                                                            method="POST">
                                                            @csrf
                                                            <label class="form-label small">Follow-up Status</label>
                                                            @php
                                                                $followupClass = [
                                                                    'leads' => 'bg-primary-subtle text-primary border-primary',
                                                                    'followed_up' => 'bg-info text-dark',
                                                                    'waiting' => 'bg-warning text-dark',
                                                                    'interested' => 'bg-info-subtle text-info border-info',
                                                                    'not_interested' => 'bg-danger-subtle text-danger border-danger',
                                                                    'converted' => 'bg-success text-white',
                                                                    'dead' => 'bg-dark text-white',
                                                                ][$followupStatus] ?? 'bg-light';
                                                            @endphp
                                                            <select name="followup_status" class="form-select mb-3 {{ $followupClass }}"
                                                                onchange="this.form.submit()">
                                                                <option value="leads" class="bg-white text-dark" {{ $followupStatus == 'leads' ? 'selected' : '' }}>Leads</option>
                                                                <option value="followed_up" class="bg-white text-dark" {{ $followupStatus == 'followed_up' ? 'selected' : '' }}>Followed Up</option>
                                                                <option value="waiting" class="bg-white text-dark" {{ $followupStatus == 'waiting' ? 'selected' : '' }}>Waiting</option>
                                                                <option value="interested" class="bg-white text-dark" {{ $followupStatus == 'interested' ? 'selected' : '' }}>Interested</option>
                                                                <option value="not_interested" class="bg-white text-dark" {{ $followupStatus == 'not_interested' ? 'selected' : '' }}>Not
                                                                    Interested</option>
                                                                <option value="converted" class="bg-white text-dark" {{ $followupStatus == 'converted' ? 'selected' : '' }}>Converted</option>
                                                                <option value="dead" class="bg-white text-dark" {{ $followupStatus == 'dead' ? 'selected' : '' }}>Dead</option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Notes -->
                                            @php
                                                $displayNotes = $booking->notes ?: $booking->special_requests ?: $booking->message;
                                            @endphp
                                            @if($displayNotes)
                                                <div class="col-12">
                                                    <h6 class="border-bottom pb-2 mt-3">Notes / Special Requests</h6>
                                                    <div class="bg-light p-3 rounded">
                                                        {{ $displayNotes }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No bookings yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of
                    {{ $bookings->total() }} bookings
                </div>
                <div>
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle follow-up date form submissions
            document.querySelectorAll('.followup-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formElement = this;
                    const formData = new FormData(formElement);
                    const submitBtn = formElement.querySelector('button[type="submit"]');
                    const originalHtml = submitBtn.innerHTML;

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

                    fetch(formElement.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            }
                            throw new Error('Network response was not ok');
                        })
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                submitBtn.innerHTML = '<i class="bi bi-check-circle text-success"></i>';
                                setTimeout(function () {
                                    location.reload();
                                }, 500);
                            } else {
                                alert('Error updating follow-up date: ' + (data.message || 'Unknown error'));
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalHtml;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating follow-up date. Please try again.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        });
                });
            });
        });
    </script>
@endsection