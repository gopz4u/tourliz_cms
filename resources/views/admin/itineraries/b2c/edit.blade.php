@extends('layouts.admin')

@section('title', 'Edit B2C Itinerary')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">Edit B2C Travel Itinerary: {{ $itinerary->title }}</h2>
        <p class="text-muted small mb-0">Update public package details, day schedules, and pricing.</p>
    </div>
    <a href="{{ route('admin.itineraries.b2c.index') }}" class="btn btn-outline-secondary">
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

<form action="{{ route('admin.itineraries.b2c.update', $itinerary->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Main Form Left -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Quotation & Customer Details</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="isTemplateToggle" name="is_template" value="1" {{ old('is_template', $itinerary->b2cDetail->is_template ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isTemplateToggle">Save as Reusable Template</label>
                            </div>
                            <small class="text-muted">Turn off if this is a quotation for a specific customer/lead.</small>
                        </div>
                        <div class="col-md-6 customer-fields" style="display: {{ old('is_template', $itinerary->b2cDetail->is_template ?? true) ? 'none' : 'block' }};">
                            <label class="form-label font-weight-bold">Lead / Customer Ref.</label>
                            <input type="text" name="quotation_reference" class="form-control form-control-sm" value="{{ old('quotation_reference', $itinerary->b2cDetail->quotation_reference ?? '') }}">
                        </div>
                    </div>
                    <div class="row g-3 customer-fields" style="display: {{ old('is_template', $itinerary->b2cDetail->is_template ?? true) ? 'none' : 'flex' }};">
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $itinerary->b2cDetail->customer_name ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">WhatsApp Number</label>
                            <input type="text" name="customer_whatsapp" class="form-control" value="{{ old('customer_whatsapp', $itinerary->b2cDetail->customer_whatsapp ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $itinerary->b2cDetail->customer_email ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Travel Start Date</label>
                            <input type="date" name="travel_date_start" class="form-control" value="{{ old('travel_date_start', optional($itinerary->b2cDetail->travel_date_start ?? null)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Adults</label>
                            <input type="number" name="adults" class="form-control" value="{{ old('adults', $itinerary->b2cDetail->adults ?? 2) }}" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Children</label>
                            <input type="number" name="children" class="form-control" value="{{ old('children', $itinerary->b2cDetail->children ?? 0) }}" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Rooms</label>
                            <input type="number" name="rooms" class="form-control" value="{{ old('rooms', $itinerary->b2cDetail->rooms ?? 1) }}" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Lead Source</label>
                            <select name="lead_source" class="form-select">
                                <option value="">Select...</option>
                                <option value="Facebook" {{ old('lead_source', $itinerary->b2cDetail->lead_source ?? '') == 'Facebook' ? 'selected' : '' }}>Facebook Ads</option>
                                <option value="Instagram" {{ old('lead_source', $itinerary->b2cDetail->lead_source ?? '') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="WhatsApp" {{ old('lead_source', $itinerary->b2cDetail->lead_source ?? '') == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="Website" {{ old('lead_source', $itinerary->b2cDetail->lead_source ?? '') == 'Website' ? 'selected' : '' }}>Website</option>
                                <option value="Referral" {{ old('lead_source', $itinerary->b2cDetail->lead_source ?? '') == 'Referral' ? 'selected' : '' }}>Referral</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">General Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Itinerary Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $itinerary->title) }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Destination <span class="text-danger">*</span></label>
                            <select name="destination_id" class="form-select" required>
                                <option value="">Select Destination</option>
                                @foreach($destinations as $dest)
                                    <option value="{{ $dest->id }}" {{ old('destination_id', $itinerary->destination_id) == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $itinerary->duration_days) }}" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Nights) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', $itinerary->duration_nights) }}" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $itinerary->b2cDetail->short_description ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $itinerary->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Package Highlights, Inclusions & Exclusions -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Highlights, Inclusions & Exclusions</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-warning"><i class="bi bi-star-fill me-1"></i> Package Highlights (One item per line)</label>
                        <textarea name="highlights" class="form-control" rows="3" placeholder="e.g.&#10;Private Sunset Cruise&#10;5-Star Luxury Resort Stay">{{ old('highlights', is_array($itinerary->highlights) ? implode("\n", $itinerary->highlights) : $itinerary->highlights) }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Overall Inclusions (One item per line)</label>
                            <textarea name="inclusions" class="form-control" rows="4" placeholder="e.g.&#10;Airport Transfers&#10;Daily Breakfast">{{ old('inclusions', is_array($itinerary->inclusions) ? implode("\n", $itinerary->inclusions) : $itinerary->inclusions) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i> Overall Exclusions (One item per line)</label>
                            <textarea name="exclusions" class="form-control" rows="4" placeholder="e.g.&#10;Personal Expenses&#10;Travel Insurance">{{ old('exclusions', is_array($itinerary->exclusions) ? implode("\n", $itinerary->exclusions) : $itinerary->exclusions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Structured Day Activities -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">Day-by-Day Itinerary Schedule</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn"><i class="bi bi-plus"></i> Add Day</button>
                </div>
                <div class="card-body" id="daysContainer">
                    @forelse($itinerary->days as $index => $day)
                        <div class="day-card border rounded p-3 mb-3" data-day="{{ $day->day_number }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary">Day {{ $day->day_number }} Schedule</h6>
                                <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" onclick="this.closest('.day-card').remove();"><i class="bi bi-trash"></i></button>
                            </div>
                            <input type="hidden" name="days[{{ $index }}][day_number]" value="{{ $day->day_number }}">
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <input type="text" name="days[{{ $index }}][title]" class="form-control" value="{{ $day->title }}" placeholder="Day Title">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="days[{{ $index }}][overnight_location]" class="form-control" value="{{ $day->overnight_location }}" placeholder="Overnight Location">
                                </div>
                            </div>
                            <textarea name="days[{{ $index }}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description">{{ $day->description }}</textarea>

                            <div class="row g-2 bg-light p-2 rounded">
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                                    <textarea name="days[{{ $index }}][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line">{{ is_array($day->highlights) ? implode("\n", $day->highlights) : $day->highlights }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                                    <textarea name="days[{{ $index }}][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line">{{ is_array($day->inclusions) ? implode("\n", $day->inclusions) : $day->inclusions }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                                    <textarea name="days[{{ $index }}][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line">{{ is_array($day->exclusions) ? implode("\n", $day->exclusions) : $day->exclusions }}</textarea>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="day-card border rounded p-3 mb-3" data-day="1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary">Day 1 Schedule</h6>
                            </div>
                            <input type="hidden" name="days[0][day_number]" value="1">
                            <div class="row g-2 mb-2">
                                <div class="col-md-8">
                                    <input type="text" name="days[0][title]" class="form-control" placeholder="Day Title">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="days[0][overnight_location]" class="form-control" placeholder="Overnight Location">
                                </div>
                            </div>
                            <textarea name="days[0][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description"></textarea>

                            <div class="row g-2 bg-light p-2 rounded">
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                                    <textarea name="days[0][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                                    <textarea name="days[0][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                                    <textarea name="days[0][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line"></textarea>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Pricing & Status</div>
                <div class="card-body">
                    <div class="mb-3 border-bottom pb-3">
                        <label class="form-label font-weight-bold text-muted">Pricing Engine Calculator</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Adult Rate (RM)</label>
                                <input type="number" step="0.01" id="adultRate" class="form-control form-control-sm calc-trigger" value="0.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Child Rate (RM)</label>
                                <input type="number" step="0.01" id="childRate" class="form-control form-control-sm calc-trigger" value="{{ old('child_price', $itinerary->b2cDetail->child_price ?? '0.00') }}">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Rates above are for calculation only. The Total Selling Price below is what gets saved.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-success">Total Package Price (RM) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="selling_price" id="sellingPrice" class="form-control fw-bold text-success" value="{{ old('selling_price', $itinerary->b2cDetail->selling_price ?? '0.00') }}" required>
                        <small class="text-muted" id="priceSummary"></small>
                    </div>
                    <div class="mb-3" style="display:none;">
                        <label class="form-label">Child Price (Internal)</label>
                        <input type="number" step="0.01" name="child_price" id="hiddenChildPrice" class="form-control" value="{{ old('child_price', $itinerary->b2cDetail->child_price ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status', $itinerary->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $itinerary->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $itinerary->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublishedCheck" {{ old('is_published', $itinerary->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="isPublishedCheck">
                            Visible on Public Website
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        @if(!empty($itinerary->b2cDetail->image_url))
                            <div class="mb-2">
                                <img src="{{ $itinerary->b2cDetail->image_url }}" alt="Preview" class="img-thumbnail" style="height:80px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                <i class="bi bi-check-circle me-1"></i> Update B2C Itinerary
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let dayCount = {{ count($itinerary->days) > 0 ? count($itinerary->days) : 1 }};
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
                <textarea name="days[${index}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description"></textarea>

                <div class="row g-2 bg-light p-2 rounded">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-warning mb-1"><i class="bi bi-star me-1"></i> Day Highlights</label>
                        <textarea name="days[${index}][highlights]" class="form-control form-control-sm" rows="2" placeholder="One highlight per line"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-success mb-1"><i class="bi bi-check-lg me-1"></i> Day Inclusions</label>
                        <textarea name="days[${index}][inclusions]" class="form-control form-control-sm" rows="2" placeholder="One inclusion per line"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold text-danger mb-1"><i class="bi bi-x-lg me-1"></i> Day Exclusions</label>
                        <textarea name="days[${index}][exclusions]" class="form-control form-control-sm" rows="2" placeholder="One exclusion per line"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', dayHtml);
    });
    document.getElementById('isTemplateToggle').addEventListener('change', function() {
        const fields = document.querySelectorAll('.customer-fields');
        fields.forEach(el => {
            el.style.display = this.checked ? 'none' : (el.classList.contains('row') ? 'flex' : 'block');
        });
    });

    // Pricing Engine Logic
    function calculateTotalPrice() {
        const adults = parseInt(document.querySelector('input[name="adults"]').value) || 0;
        const children = parseInt(document.querySelector('input[name="children"]').value) || 0;
        const adultRate = parseFloat(document.getElementById('adultRate').value) || 0;
        const childRate = parseFloat(document.getElementById('childRate').value) || 0;
        
        // Only override selling price if user actually types into the rate boxes
        if(document.activeElement === document.getElementById('adultRate') || document.activeElement === document.getElementById('childRate') || document.activeElement === document.querySelector('input[name="adults"]') || document.activeElement === document.querySelector('input[name="children"]')) {
            const total = (adults * adultRate) + (children * childRate);
            document.getElementById('sellingPrice').value = total.toFixed(2);
            document.getElementById('hiddenChildPrice').value = childRate.toFixed(2);
        }
        
        document.getElementById('priceSummary').textContent = `${adults} Adults × RM${adultRate.toFixed(2)} + ${children} Children × RM${childRate.toFixed(2)}`;
    }

    document.querySelectorAll('.calc-trigger').forEach(el => {
        el.addEventListener('input', calculateTotalPrice);
    });
    document.querySelector('input[name="adults"]').addEventListener('input', calculateTotalPrice);
    document.querySelector('input[name="children"]').addEventListener('input', calculateTotalPrice);
    
    // Initial run
    calculateTotalPrice();
</script>
@endpush
@endsection
