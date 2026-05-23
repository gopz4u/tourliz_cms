@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Create New Booking</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('admin.bookings.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="card-title mb-0"><i class="bi bi-person-circle me-2 text-primary"></i>Customer
                            Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="Enter customer name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required
                                    placeholder="customer@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+1234567890">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes / Special Requests</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Any specific requirements..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="card-title mb-0"><i class="bi bi-suitcase-lg me-2 text-primary"></i>Trip Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select Package <span class="text-danger">*</span></label>
                            <select name="package_id" class="form-select select2" required>
                                <option value="">Choose a package...</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }} ({{ $package->currency }}
                                        {{ number_format($package->discount_price ?: $package->price) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Travel Date (Arrival) <span class="text-danger">*</span></label>
                            <input type="date" name="travel_date" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Adults <span class="text-danger">*</span></label>
                                <input type="number" name="adults" id="booking_adults" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Kids (Total)</label>
                                <input type="number" name="children" id="booking_children" class="form-control" min="0" value="0" readonly>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Kids (Age 2-6)</label>
                                <input type="number" name="kids_2_6" id="booking_kids_2_6" class="form-control" min="0" value="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Kids (Age 6-11)</label>
                                <input type="number" name="kids_6_10" id="booking_kids_6_10" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Status & Follow-up
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Booking Status</label>
                            <select name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Follow-up Status</label>
                            <select name="followup_status" class="form-select">
                                <option value="leads">Leads</option>
                                <option value="followed_up" selected>Followed Up</option>
                                <option value="waiting">Waiting</option>
                                <option value="interested">Interested</option>
                                <option value="not_interested">Not Interested</option>
                                <option value="converted">Converted</option>
                                <option value="dead">Dead</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Next Follow-up Date</label>
                            <input type="date" name="next_followup_date" class="form-control"
                                value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-0 rounded-bottom">
                            <i class="bi bi-check-circle me-2"></i> Save Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function updateKidsTotal() {
            var kids26 = parseInt($('#booking_kids_2_6').val()) || 0;
            var kids610 = parseInt($('#booking_kids_6_10').val()) || 0;
            $('#booking_children').val(kids26 + kids610);
        }

        $('#booking_kids_2_6, #booking_kids_6_10').on('input change', function() {
            updateKidsTotal();
        });
    });
</script>
@endpush