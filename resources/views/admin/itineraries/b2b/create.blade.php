@extends('layouts.admin')

@section('title', 'Create B2B Proposal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">Create B2B Partner Itinerary</h2>
        <p class="text-muted small mb-0">Configure agency pricing, net rates, markups, and operational notes.</p>
    </div>
    <a href="{{ route('admin.itineraries.b2b.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.itineraries.b2b.store') }}" method="POST">
    @csrf
    <div class="row">
        <!-- Main Form Left -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">B2B Proposal Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Proposal Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. 6D5N Custom Kerala Package for Apex Travel Agency">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Destination <span class="text-danger">*</span></label>
                            <select name="destination_id" class="form-select" required>
                                <option value="">Select Destination</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ old('destination_id') == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Target Agency</label>
                            <select name="agency_id" class="form-select">
                                <option value="">Select B2B Partner Agency (Optional)</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', 4) }}" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Nights) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', 3) }}" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Min Pax</label>
                            <input type="number" name="min_pax" class="form-control" value="{{ old('min_pax', 2) }}" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Max Pax</label>
                            <input type="number" name="max_pax" class="form-control" value="{{ old('max_pax', 20) }}" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proposal Overview</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Summary notes for the agency">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Operational & Vendor Section -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Operational Requirements & Notes (Internal Only)</div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Hotel Category</label>
                            <input type="text" name="hotel_category" class="form-control" placeholder="e.g. 4-Star Premium" value="{{ old('hotel_category') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room Category</label>
                            <input type="text" name="room_category" class="form-control" placeholder="e.g. Deluxe Sea View" value="{{ old('room_category') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vehicle Category</label>
                            <input type="text" name="vehicle_category" class="form-control" placeholder="e.g. Innova Crysta AC" value="{{ old('vehicle_category') }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Primary Supplier / Vendor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">Select Vendor (Optional)</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="guide_required" value="1" id="guideCheck" {{ old('guide_required') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="guideCheck">
                                    Tour Guide Required
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Internal Operational Notes (Never exposed to public API)</label>
                        <textarea name="operational_notes" class="form-control" rows="2" placeholder="Driver contact, voucher codes, supplier terms">{{ old('operational_notes') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agent Facing Notes</label>
                        <textarea name="agent_notes" class="form-control" rows="2" placeholder="Notes for agent reference">{{ old('agent_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Day-by-Day Schedule -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">Day-by-Day Itinerary Schedule</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn"><i class="bi bi-plus"></i> Add Day</button>
                </div>
                <div class="card-body" id="daysContainer">
                    <div class="day-card border rounded p-3 mb-3" data-day="1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-primary">Day 1 Schedule</h6>
                        </div>
                        <input type="hidden" name="days[0][day_number]" value="1">
                        <div class="row g-2 mb-2">
                            <div class="col-md-8">
                                <input type="text" name="days[0][title]" class="form-control" placeholder="Day Title (e.g. Airport Transfer & Hotel Check-in)">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="days[0][overnight_location]" class="form-control" placeholder="Overnight Location">
                            </div>
                        </div>
                        <textarea name="days[0][description]" class="form-control form-control-sm" rows="2" placeholder="Day description"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold text-primary">B2B Financial Matrix</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Net Cost Rate (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="net_rate" class="form-control" value="{{ old('net_rate', '0.00') }}" required>
                        <small class="text-muted">Vendor base cost before markup.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Agent Selling Rate (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="agent_rate" class="form-control" value="{{ old('agent_rate', '0.00') }}" required>
                        <small class="text-muted">Quoted price to B2B partner.</small>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Markup (%)</label>
                            <input type="number" step="0.01" name="markup_percentage" class="form-control" value="{{ old('markup_percentage', '10.00') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Markup (₹)</label>
                            <input type="number" step="0.01" name="markup_amount" class="form-control" value="{{ old('markup_amount', '0.00') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agent Commission (₹)</label>
                        <input type="number" step="0.01" name="commission" class="form-control" value="{{ old('commission', '0.00') }}">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Validity & Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Validity Start Date</label>
                        <input type="date" name="validity_start" class="form-control" value="{{ old('validity_start') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Validity End Date</label>
                        <input type="date" name="validity_end" class="form-control" value="{{ old('validity_end') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft Proposal</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Active / Approved</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                <i class="bi bi-check-circle me-1"></i> Save B2B Proposal
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let dayCount = 1;
    document.getElementById('addDayBtn').addEventListener('click', function() {
        dayCount++;
        const index = dayCount - 1;
        const container = document.getElementById('daysContainer');
        const dayHtml = `
            <div class="day-card border rounded p-3 mb-3" data-day="${dayCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-primary">Day ${dayCount} Schedule</h6>
                    <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();"><i class="bi bi-trash"></i></button>
                </div>
                <input type="hidden" name="days[${index}][day_number]" value="${dayCount}">
                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" name="days[${index}][title]" class="form-control" placeholder="Day Title">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="days[${index}][overnight_location]" class="form-control" placeholder="Overnight Location">
                    </div>
                </div>
                <textarea name="days[${index}][description]" class="form-control form-control-sm" rows="2" placeholder="Day description"></textarea>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', dayHtml);
    });
</script>
@endpush
@endsection
