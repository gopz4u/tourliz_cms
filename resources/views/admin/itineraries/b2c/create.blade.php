@extends('layouts.admin')

@section('title', 'Create B2C Itinerary')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 font-weight-bold text-gray-800 mb-1">Create B2C Travel Itinerary</h2>
        <p class="text-muted small mb-0">Add a public travel package with structured day-by-day activities.</p>
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

<form action="{{ route('admin.itineraries.b2c.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <!-- Main Form Left -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Quotation & Customer Details</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="isTemplateToggle" name="is_template" value="1" checked>
                                <label class="form-check-label fw-bold" for="isTemplateToggle">Save as Reusable Template</label>
                            </div>
                            <small class="text-muted">Turn off if this is a quotation for a specific customer/lead.</small>
                        </div>
                        <div class="col-md-6 customer-fields" style="display: none;">
                            <label class="form-label font-weight-bold">Lead / Customer Ref.</label>
                            <input type="text" name="quotation_reference" class="form-control form-control-sm" placeholder="Leave blank to auto-generate">
                        </div>
                    </div>
                    <div class="row g-3 customer-fields" style="display: none;">
                        <div class="col-md-4">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">WhatsApp Number</label>
                            <input type="text" name="customer_whatsapp" class="form-control" value="{{ old('customer_whatsapp') }}" placeholder="+1234567890">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}" placeholder="john@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Travel Start Date</label>
                            <input type="date" name="travel_date_start" class="form-control" value="{{ old('travel_date_start') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Adults</label>
                            <input type="number" name="adults" class="form-control" value="{{ old('adults', 2) }}" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Children</label>
                            <input type="number" name="children" class="form-control" value="{{ old('children', 0) }}" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Rooms</label>
                            <input type="number" name="rooms" class="form-control" value="{{ old('rooms', 1) }}" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Lead Source</label>
                            <select name="lead_source" class="form-select">
                                <option value="">Select...</option>
                                <option value="Facebook">Facebook Ads</option>
                                <option value="Instagram">Instagram</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Website">Website</option>
                                <option value="Referral">Referral</option>
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
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. 5 Days Magical Bali Paradise Tour">
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
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', 3) }}" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Duration (Nights) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_nights" class="form-control" value="{{ old('duration_nights', 2) }}" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2" placeholder="Brief tagline or overview for cards">{{ old('short_description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detailed itinerary description">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Package Highlights, Inclusions & Exclusions -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Highlights, Inclusions & Exclusions</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-warning"><i class="bi bi-star-fill me-1"></i> Package Highlights (One item per line)</label>
                        <textarea name="highlights" class="form-control" rows="3" placeholder="e.g.&#10;Private Sunset Cruise&#10;5-Star Luxury Resort Stay&#10;Guided Cultural Temple Tour">{{ old('highlights') }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Overall Inclusions (One item per line)</label>
                            <textarea name="inclusions" class="form-control" rows="4" placeholder="e.g.&#10;Airport Transfers&#10;Daily Breakfast&#10;All Entry Tickets">{{ old('inclusions') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i> Overall Exclusions (One item per line)</label>
                            <textarea name="exclusions" class="form-control" rows="4" placeholder="e.g.&#10;Personal Expenses&#10;Travel Insurance&#10;International Airfare">{{ old('exclusions') }}</textarea>
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
                    <div class="day-card border rounded p-3 mb-3" data-day="1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-primary">Day 1 Schedule</h6>
                            <button type="button" class="btn btn-sm btn-link text-danger remove-day-btn" style="display:none;"><i class="bi bi-trash"></i></button>
                        </div>
                        <input type="hidden" name="days[0][day_number]" value="1">
                        <div class="row g-2 mb-2">
                            <div class="col-md-8">
                                <input type="text" name="days[0][title]" class="form-control" placeholder="Day Title (e.g., Arrival & Sunset Cruise)" value="Arrival & Welcome">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="days[0][overnight_location]" class="form-control" placeholder="Overnight City/Hotel" value="">
                            </div>
                        </div>
                        <textarea name="days[0][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and itinerary summary"></textarea>
                        
                        <!-- Operations Inventory & Supplier Bookings for Day 1 -->
                        <div class="day-inventory-box mt-3 p-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap gap-2">
                                <span class="fw-bold small text-uppercase text-primary">
                                    <i class="bi bi-box-seam me-1"></i> Operations Inventory & Supplier Bookings
                                </span>
                                <div class="btn-group btn-group-sm flex-wrap">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInventoryModal(0, 'hotels')">
                                        <i class="bi bi-building"></i> + Hotel
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openInventoryModal(0, 'transports')">
                                        <i class="bi bi-car-front"></i> + Transport
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="openInventoryModal(0, 'tickets')">
                                        <i class="bi bi-ticket-perforated"></i> + Ticket
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="openInventoryModal(0, 'spots')">
                                        <i class="bi bi-geo-alt"></i> + Spot
                                    </button>
                                    <button type="button" class="btn btn-outline-warning text-dark btn-sm" onclick="openInventoryModal(0, 'activities')">
                                        <i class="bi bi-lightning"></i> + Activity
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="openInventoryModal(0, 'meals')">
                                        <i class="bi bi-cup-hot"></i> + Meal
                                    </button>
                                </div>
                            </div>

                            <div class="day-items-container" id="day-items-0">
                                {{-- Selected inventory items will appear here --}}
                            </div>
                        </div>

                        <div class="row g-2 bg-light p-2 rounded mt-2">
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
                </div>
            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Pricing & Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Primary Currency <span class="text-danger">*</span></label>
                        <select name="currency" id="primaryCurrency" class="form-select fw-bold" required>
                            <option value="MYR" {{ old('currency', 'MYR') == 'MYR' ? 'selected' : '' }}>MYR - Malaysian Ringgit (RM)</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar ($)</option>
                            <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee (₹)</option>
                        </select>
                    </div>

                    <div class="mb-3 border-bottom pb-3">
                        <label class="form-label font-weight-bold text-muted">Pricing Engine Calculator</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Adult Rate (<span class="currency-symbol-label">RM</span>)</label>
                                <input type="number" step="0.01" id="adultRate" class="form-control form-control-sm calc-trigger" value="0.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Child Rate (<span class="currency-symbol-label">RM</span>)</label>
                                <input type="number" step="0.01" id="childRate" class="form-control form-control-sm calc-trigger" value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-success">Total Package Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold text-success" id="sellingPriceCurrencyBadge">RM</span>
                            <input type="number" step="0.01" name="selling_price" id="sellingPrice" class="form-control fw-bold text-success" value="{{ old('selling_price', '0.00') }}" required>
                        </div>
                        <small class="text-muted" id="priceSummary">0 Adults × RM0 + 0 Children × RM0</small>
                    </div>

                    <!-- Live Multi-Currency Preview -->
                    <div class="card bg-light border-primary mb-3">
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold text-primary mb-2" style="font-size: 13px;">
                                <i class="bi bi-currency-exchange me-1"></i> Live Multi-Currency Preview
                            </h6>
                            <div id="multiCurrencyPreview" class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><strong>MYR (RM):</strong></span>
                                    <span id="previewMYR" class="fw-bold">RM 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span><strong>USD ($):</strong></span>
                                    <span id="previewUSD" class="fw-bold">$ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span><strong>INR (₹):</strong></span>
                                    <span id="previewINR" class="fw-bold">₹ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" style="display:none;">
                        <label class="form-label">Child Price (Internal)</label>
                        <input type="number" step="0.01" name="child_price" id="hiddenChildPrice" class="form-control" value="{{ old('child_price') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublishedCheck" checked>
                        <label class="form-check-label fw-bold" for="isPublishedCheck">
                            Visible on Public Website
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">SEO Optimization</div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label small">SEO Meta Title</label>
                        <input type="text" name="seo_title" class="form-control form-control-sm" value="{{ old('seo_title') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">SEO Meta Description</label>
                        <textarea name="seo_description" class="form-control form-control-sm" rows="2">{{ old('seo_description') }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold">
                <i class="bi bi-check-circle me-1"></i> Save B2C Itinerary
            </button>
        </div>
    </div>
</form>

<!-- Operations Inventory Modal Partial -->
@include('admin.itineraries.partials.inventory_modal_and_scripts')

@push('scripts')
<script>
    let dayCount = 1;
    document.getElementById('addDayBtn').addEventListener('click', function() {
        dayCount++;
        const index = dayCount - 1;
        const container = document.getElementById('daysContainer');
        const dayHtml = `
            <div class="day-card border rounded p-3 mb-3 bg-white shadow-sm" data-day="${dayCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-1"></i> Day ${dayCount} Schedule</h6>
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
                <textarea name="days[${index}][description]" class="form-control form-control-sm mb-2" rows="2" placeholder="Day description and flow..."></textarea>

                <!-- Operations Inventory & Supplier Bookings for Day -->
                <div class="day-inventory-box mt-3 p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom flex-wrap gap-2">
                        <span class="fw-bold small text-uppercase text-primary">
                            <i class="bi bi-box-seam me-1"></i> Operations Inventory & Supplier Bookings
                        </span>
                        <div class="btn-group btn-group-sm flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openInventoryModal(${index}, 'hotels')">
                                <i class="bi bi-building"></i> + Hotel
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openInventoryModal(${index}, 'transports')">
                                <i class="bi bi-car-front"></i> + Transport
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="openInventoryModal(${index}, 'tickets')">
                                <i class="bi bi-ticket-perforated"></i> + Ticket
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="openInventoryModal(${index}, 'spots')">
                                <i class="bi bi-geo-alt"></i> + Spot
                            </button>
                            <button type="button" class="btn btn-outline-warning text-dark btn-sm" onclick="openInventoryModal(${index}, 'activities')">
                                <i class="bi bi-lightning"></i> + Activity
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="openInventoryModal(${index}, 'meals')">
                                <i class="bi bi-cup-hot"></i> + Meal
                            </button>
                        </div>
                    </div>

                    <div class="day-items-container" id="day-items-${index}">
                        {{-- Newly selected inventory items will appear here --}}
                    </div>
                </div>

                <div class="row g-2 bg-light p-2 rounded mt-2">
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

    // Multi-Currency Rates & Conversions
    const exchangeRates = {
        MYR: 1.0,
        USD: 4.7428, // 1 USD = 4.7428 MYR
        INR: 0.0530, // 1 INR = 0.0530 MYR
    };

    const currencySymbols = {
        MYR: 'RM',
        USD: '$',
        INR: '₹'
    };

    function updateMultiCurrencyPreview() {
        const curSelect = document.getElementById('primaryCurrency');
        const cur = curSelect ? curSelect.value : 'MYR';
        const curSymbol = currencySymbols[cur] || cur;
        const amount = parseFloat(document.getElementById('sellingPrice').value) || 0;

        // Update symbols across UI
        document.querySelectorAll('.currency-symbol-label').forEach(el => el.textContent = curSymbol);
        const badge = document.getElementById('sellingPriceCurrencyBadge');
        if (badge) badge.textContent = curSymbol;

        // Base amount in MYR
        const rateFrom = exchangeRates[cur] || 1.0;
        const amountInMYR = amount * rateFrom;

        ['MYR', 'USD', 'INR'].forEach(targetCur => {
            const rateTo = exchangeRates[targetCur] || 1.0;
            const converted = amountInMYR / rateTo;
            const previewEl = document.getElementById(`preview${targetCur}`);
            if (previewEl) {
                previewEl.textContent = `${currencySymbols[targetCur]} ${converted.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
        });
    }

    // Pricing Engine Logic
    function calculateTotalPrice() {
        const adults = parseInt(document.querySelector('input[name="adults"]').value) || 0;
        const children = parseInt(document.querySelector('input[name="children"]').value) || 0;
        const adultRate = parseFloat(document.getElementById('adultRate').value) || 0;
        const childRate = parseFloat(document.getElementById('childRate').value) || 0;
        const curSelect = document.getElementById('primaryCurrency');
        const curSymbol = currencySymbols[curSelect ? curSelect.value : 'MYR'] || 'RM';
        
        const total = (adults * adultRate) + (children * childRate);
        
        document.getElementById('sellingPrice').value = total.toFixed(2);
        document.getElementById('hiddenChildPrice').value = childRate.toFixed(2);
        document.getElementById('priceSummary').textContent = `${adults} Adults × ${curSymbol}${adultRate.toFixed(2)} + ${children} Children × ${curSymbol}${childRate.toFixed(2)}`;

        updateMultiCurrencyPreview();
    }

    document.querySelectorAll('.calc-trigger').forEach(el => {
        el.addEventListener('input', calculateTotalPrice);
    });
    document.querySelector('input[name="adults"]').addEventListener('input', calculateTotalPrice);
    document.querySelector('input[name="children"]').addEventListener('input', calculateTotalPrice);

    document.getElementById('sellingPrice').addEventListener('input', updateMultiCurrencyPreview);
    document.getElementById('primaryCurrency').addEventListener('change', function() {
        calculateTotalPrice();
        updateMultiCurrencyPreview();
    });

    // Initial run
    updateMultiCurrencyPreview();
</script>
@endpush
@endsection
