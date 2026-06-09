@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="mb-0">Booking Details</h4>
                <span class="badge bg-light text-dark border">{{ $booking->quote_id }}</span>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if(auth()->user()->isSuperAdmin())
                @if($booking->trashed())
                    <form action="{{ route('admin.bookings.restore', $booking->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-bootstrap-reboot me-2"></i>Restore Booking
                        </button>
                    </form>
                    <form action="{{ route('admin.bookings.forceDelete', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this booking? This action cannot be undone.')" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-octagon me-2"></i>Permanently Delete
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Delete Booking
                        </button>
                    </form>
                @endif
            @endif
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @if($booking->trashed())
        <div class="alert alert-warning border-warning bg-warning-subtle text-dark d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
            <div>
                <strong>Notice:</strong> This booking is currently soft-deleted. You can view its historical data here, restore it, or permanently delete it.
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Customer & Package Info -->
        <div class="col-lg-8">
            <!-- Customer Card -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Full Name</p>
                            <div class="fw-bold">{{ $booking->name ?: $booking->customer_name ?: 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Email Address</p>
                            <div class="fw-bold">
                                @if($booking->email || $booking->customer_email)
                                    <a href="mailto:{{ $booking->email ?: $booking->customer_email }}">{{ $booking->email ?: $booking->customer_email }}</a>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Phone Number</p>
                            <div class="fw-bold">
                                @if($booking->phone || $booking->customer_phone)
                                    <a href="tel:{{ $booking->phone ?: $booking->customer_phone }}">{{ $booking->phone ?: $booking->customer_phone }}</a>
                                    @if($booking->whatsapp_number)
                                        <small class="text-success ms-2"><i class="bi bi-whatsapp"></i> {{ $booking->whatsapp_number }}</small>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Contact Method</p>
                            <div>
                                @if($booking->contact_method)
                                    <span class="badge bg-info">{{ ucfirst($booking->contact_method) }}</span>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Location</p>
                            <div>{{ implode(', ', array_filter([$booking->customer_city, $booking->customer_state, $booking->customer_country])) ?: 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Postal Code</p>
                            <div>{{ $booking->customer_postal_code ?: 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <p class="small text-muted mb-1">Full Address</p>
                            <div>{{ $booking->address ?: $booking->customer_address ?: 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Booking Created</p>
                            <div>{{ $booking->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Last Updated</p>
                            <div>{{ $booking->updated_at->format('M d, Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package & Pricing Card -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-suitcase-lg me-2 text-primary"></i>Trip Details & Pricing</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="small text-muted mb-1">Selected Package</p>
                            <div class="fw-bold text-primary">{{ $booking->package->name ?? 'Custom / Deleted Package' }}</div>
                            <div class="small text-muted">{{ $booking->package->destination->name ?? '' }}</div>
                        </div>
                        <div class="col-md-3">
                            <p class="small text-muted mb-1">Travel Date</p>
                            <div class="fw-bold">{{ $booking->travel_date ? $booking->travel_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        <div class="col-md-3">
                            <p class="small text-muted mb-1">Pax</p>
                            <div class="fw-bold">{{ $booking->adults }} Adults, {{ $booking->children }} Kids</div>
                            @if($booking->kids_2_6 > 0 || $booking->kids_6_10 > 0)
                                <div class="small text-muted" style="font-size: 0.75rem">
                                    (2-6y: {{ $booking->kids_2_6 }}, 6-10y: {{ $booking->kids_6_10 }})
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive bg-light rounded p-3">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Base Price</td>
                                    <td class="text-end fw-bold">{{ $booking->currency }} {{ number_format($booking->base_price ?: $booking->package_price, 2) }}</td>
                                </tr>
                                @if(!empty($booking->addons) || ($booking->addons_amount > 0))
                                <tr>
                                    <td class="text-muted">
                                        Add-ons
                                        @if(!empty($booking->addons))
                                            <br><small class="text-muted fst-italic">{{ implode(', ', (array)$booking->addons) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">{{ $booking->currency }} {{ number_format($booking->addons_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if(($booking->services_amount > 0))
                                <tr>
                                    <td class="text-muted">Extra Services</td>
                                    <td class="text-end fw-bold">{{ $booking->currency }} {{ number_format($booking->services_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if(($booking->discount_amount > 0))
                                <tr>
                                    <td class="text-success">Discount @if($booking->coupon_code) <span class="badge bg-success-subtle text-success border border-success ms-1">{{ $booking->coupon_code }}</span> @endif</td>
                                    <td class="text-end text-success fw-bold">- {{ $booking->currency }} {{ number_format($booking->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="border-top border-secondary">
                                    <td class="pt-2 h6 mb-0">Total Amount</td>
                                    <td class="pt-2 text-end h5 mb-0 text-primary">{{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes Card -->
            @if($booking->notes || $booking->special_requests)
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-journal-text me-2 text-primary"></i>Notes & Requests</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 bg-light p-3 rounded">{{ $booking->notes ?: $booking->special_requests }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar: Status & Payment -->
        <div class="col-lg-4">
            <!-- Follow-up Date Card (Prominent) -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Follow-up Information</h6>
                </div>
                <div class="card-body">
                    @php
                        $followupDate = $booking->next_followup_date;
                        $followupClass = 'text-muted';
                        $followupBadge = 'bg-secondary';
                        $followupText = 'Not Set';
                        
                        if ($followupDate) {
                            $today = now()->startOfDay();
                            $date = $followupDate->startOfDay();
                            if ($date->lt($today)) {
                                $followupClass = 'text-danger fw-bold';
                                $followupBadge = 'bg-danger';
                                $followupText = 'OVERDUE';
                            } elseif ($date->eq($today)) {
                                $followupClass = 'text-warning fw-bold';
                                $followupBadge = 'bg-warning';
                                $followupText = 'TODAY';
                            } else {
                                $followupClass = 'text-primary';
                                $followupBadge = 'bg-primary';
                                $followupText = $followupDate->format('M d, Y');
                            }
                        }
                    @endphp
                    <div class="text-center mb-3">
                        <div class="mb-2">
                            <span class="badge {{ $followupBadge }} fs-6 px-3 py-2">
                                @if($followupDate)
                                    {{ $followupDate->format('M d, Y') }}
                                @else
                                    Not Set
                                @endif
                            </span>
                        </div>
                        @if($followupDate && ($followupDate->lt(now()) || $followupDate->eq(now()->startOfDay())))
                            <div class="alert alert-{{ $followupDate->lt(now()) ? 'danger' : 'warning' }} mb-0 py-2">
                                <small><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $followupText }}</small>
                            </div>
                        @endif
                        @if($booking->followed_up_at)
                            <div class="small text-muted mt-2">
                                Last followed up: {{ $booking->followed_up_at->format('M d, Y') }}<br>
                                <span class="text-muted">{{ $booking->followed_up_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                    @if(!$booking->trashed())
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Next Follow-up Date</label>
                            <div class="d-flex gap-2">
                                <input type="date" name="next_followup_date" class="form-control" 
                                    value="{{ $booking->next_followup_date ? $booking->next_followup_date->format('Y-m-d') : '' }}">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i></button>
                            </div>
                        </div>
                    </form>
                    @else
                        <div class="text-center py-2 text-muted">
                            <small><i class="bi bi-lock me-1"></i>Follow-ups locked for deleted bookings</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i>Status Management</h6>
                </div>
                <div class="card-body">
                     @if($booking->trashed())
                        <div class="text-center py-2 text-muted">
                            <i class="bi bi-lock fs-4 d-block mb-1"></i>
                            <small>Restore booking to manage status</small>
                        </div>
                    @else
                    <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted">Booking Status</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small text-muted">Lead Pipeline Stage</label>
                            <select name="followup_status" class="form-select" onchange="this.form.submit()">
                                <option value="leads" {{ $booking->followup_status == 'leads' ? 'selected' : '' }}>Leads</option>
                                <option value="followed_up" {{ $booking->followup_status == 'followed_up' ? 'selected' : '' }}>Followed Up</option>
                                <option value="waiting" {{ $booking->followup_status == 'waiting' ? 'selected' : '' }}>Waiting</option>
                                <option value="interested" {{ $booking->followup_status == 'interested' ? 'selected' : '' }}>Interested</option>
                                <option value="not_interested" {{ $booking->followup_status == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                                <option value="converted" {{ $booking->followup_status == 'converted' ? 'selected' : '' }}>Converted</option>
                                <option value="dead" {{ $booking->followup_status == 'dead' ? 'selected' : '' }}>Dead</option>
                            </select>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-wallet2 me-2 text-primary"></i>Payment Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 payment-status-badge">
                        <div class="d-grid">
                            @php
                                $statusMap = [
                                    'paid' => ['Confirmed / Paid', 'bg-success'],
                                    'partially_paid' => ['Partially Paid', 'bg-info'],
                                    'pending' => ['Payment Pending', 'bg-warning'],
                                    'refunded' => ['Refunded', 'bg-secondary'],
                                ];
                                $st = $statusMap[$booking->payment_status ?? 'pending'] ?? ['Pending', 'bg-warning'];
                            @endphp
                            <span class="badge {{ $st[1] }} py-2 fs-6">{{ $st[0] }}</span>
                        </div>
                    </div>
                    @if(!empty($booking->payment_details))
                        <ul class="list-group list-group-flush mb-0 small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Method</span>
                                <span class="fw-bold">{{ ucfirst($booking->payment_details['method'] ?? '-') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Transaction ID</span>
                                <span class="fw-bold text-break">{{ $booking->payment_details['transaction_id'] ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Date</span>
                                <span class="fw-bold">{{ $booking->payment_details['date'] ?? '-' }}</span>
                            </li>
                        </ul>
                    @else
                        <p class="text-muted small text-center mb-0">No payment details recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
