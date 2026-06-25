@extends('layouts.admin')

@section('title', 'Create Premium Holiday Package')

@push('extra_css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0052cc 0%, #00a3ff 100%);
            --glass-bg: rgba(255, 255, 255, 0.98);
            --glass-border: rgba(0, 0, 0, 0.08);
            --card-radius: 24px;
            --app-bg: #f4f7fe;
        }

        body {
            background-color: var(--app-bg);
        }

        .wizard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Stepper Styling */
        .wizard-stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3.5rem;
            position: relative;
            padding: 0 50px;
        }

        .wizard-stepper::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 100px;
            right: 100px;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            background: transparent;
            text-align: center;
            width: 100px;
        }

        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: white;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-weight: 800;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
        }

        .step-item.active .step-circle {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 82, 204, 0.3);
            transform: scale(1.1);
        }

        .step-item.completed .step-circle {
            background: #36b37e;
            border-color: transparent;
            color: white;
        }

        .step-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .step-item.active .step-title {
            color: #0052cc;
        }

        /* Content Area */
        .wizard-card {
            background: white;
            border-radius: var(--card-radius);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--glass-border);
            overflow: hidden;
        }

        .wizard-body {
            padding: 4rem;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: slideIn 0.5s ease-out forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* UI Components */
        .premium-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .premium-input {
            background: #f8fafc;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            transition: all 0.3s;
        }

        .premium-input:focus {
            background: white;
            border-color: #0052cc;
            box-shadow: 0 0 0 5px rgba(0, 82, 204, 0.08);
        }

        .itinerary-day-card {
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            background: white;
            margin-bottom: 2.5rem;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .itinerary-day-card:hover {
            border-color: #0052cc;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
        }

        .day-header {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            border-radius: 24px 24px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .day-count {
            background: var(--primary-gradient);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }

        /* Modern Dropzone */
        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }

        .dropzone-box:hover {
            border-color: #0052cc;
            background: #eff6ff;
        }

        /* Sticky Footer */
        .wizard-footer {
            padding: 1.5rem 4rem;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            bottom: 0;
            z-index: 100;
        }

        .price-tag {
            background: #f1f5f9;
            padding: 0.75rem 1.5rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-publish {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-weight: 800;
            box-shadow: 0 10px 20px rgba(0, 82, 204, 0.2);
        }
    </style>
@endpush

@section('content')
    <div class="wizard-container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h1 class="fw-black text-dark tracking-tight mb-1">New Holiday Package</h1>
                <p class="text-muted mb-0">Crafting premium travel experiences</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-primary border rounded-pill px-3 py-2 fw-bold">
                    <i class="bi bi-clock-history me-1"></i> Auto-saving...
                </span>
            </div>
        </div>

        <!-- Stepper -->
        <div class="wizard-stepper mb-5">
            <div class="step-item active" data-step="1">
                <div class="step-circle">01</div>
                <div class="step-label">Fundamentals</div>
            </div>
            <div class="step-item" data-step="2">
                <div class="step-circle">02</div>
                <div class="step-title">Journey Map</div>
            </div>
            <div class="step-item" data-step="3">
                <div class="step-circle">03</div>
                <div class="step-title">Commercials</div>
            </div>
            <div class="step-item" data-step="4">
                <div class="step-circle">04</div>
                <div class="step-title">Compliance</div>
            </div>
        </div>

        <form id="package-wizard-form" enctype="multipart/form-data">
            <div class="wizard-card">
                <div class="wizard-body p-4 p-md-5">
                    <!-- STEP 1: FUNDAMENTALS -->
                    <div class="form-step active" id="step-1">
                        <div class="mb-5">
                            <label class="premium-label fs-5">Package Title</label>
                            <input type="text" name="name" class="form-control premium-input form-control-lg fs-4 fw-bold"
                                placeholder="Ex: Luxury Escape to Maldives" required>
                            <p class="small text-muted mt-2">This is the public name of your holiday package.</p>
                        </div>

                        <div class="row g-5">
                            <div class="col-md-8">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="premium-label">Country</label>
                                        <select name="country_id" id="country_id" class="form-select select2" required>
                                            <option value="" data-country-name="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" data-country-name="{{ $country->name }}">
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Primary Destinations</label>
                                        <select name="destination_ids[]" id="destination_ids" class="form-select select2"
                                            multiple required data-placeholder="Select Primary Cities">
                                            @foreach($destinations as $dest)
                                                <option value="{{ $dest->id }}" data-country="{{ $dest->country }}">
                                                    {{ $dest->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="premium-label">Additional Countries Covered</label>
                                        <div class="row g-2" style="max-height: 120px; overflow-y: auto;">
                                            @foreach($countries as $c)
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="country_ids[]"
                                                            value="{{ $c->id }}" id="pkg_country_{{ $c->id }}">
                                                        <label class="form-check-label small"
                                                            for="pkg_country_{{ $c->id }}">{{ $c->name }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="premium-label">Package Category</label>
                                        <select name="package_category" class="form-select premium-input">
                                            <option value="Honeymoon">Honeymoon</option>
                                            <option value="Family">Family Holiday</option>
                                            <option value="Budget">Budget Friendly</option>
                                            <option value="Standard" selected>Standard Comfort</option>
                                            <option value="Premium">Premium Luxury</option>
                                            <option value="Adventure">Adventure Tour</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-4 mt-1">
                                    <div class="col-md-12">
                                        <label class="premium-label">Source Vendors (Suppliers)</label>
                                        <select name="supplier_ids[]" class="form-select select2" multiple
                                            data-placeholder="Select vendors for this package...">
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select all vendors providing services for this
                                            package.</small>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <label class="premium-label">Core Highlights</label>
                                    <div id="highlight-editor-container"
                                        style="height: 200px; border-radius: 16px; background: #f8fafc;"></div>
                                </div>

                                <div class="row g-4 mt-4">
                                    <div class="col-md-4">
                                        <label class="premium-label">Guest Limit (Min)</label>
                                        <input type="number" name="min_pax" class="form-control premium-input" value="1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="premium-label">Guest Limit (Max)</label>
                                        <input type="number" name="max_pax" class="form-control premium-input" value="10">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="premium-label">Flight Included?</label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="includes_flight"
                                                    value="1">
                                                <label class="form-check-label small fw-bold">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="includes_flight"
                                                    value="0" checked>
                                                <label class="form-check-label small fw-bold">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-5">
                                    <label class="premium-label">Hero Cover Image</label>
                                    <label for="hero-img" class="dropzone-box w-100 mb-0" style="cursor: pointer;">
                                        <i class="bi bi-image text-primary fs-1"></i>
                                        <p class="small text-muted mt-2">Click to upload cover photo</p>
                                        <input type="file" id="hero-img" name="image" class="d-none" accept="image/*"
                                            onchange="previewHero(this)">
                                    </label>
                                    <div id="hero-preview" class="mt-3 d-none">
                                        <img src="" class="img-fluid rounded-4 shadow-sm border">
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="premium-label">Gallery Images</label>
                                    <label for="gallery-img" class="dropzone-box py-3 w-100 mb-0" style="cursor: pointer;">
                                        <i class="bi bi-images text-primary fs-3"></i>
                                        <p class="small text-muted mb-0">Add up to 10 photos</p>
                                        <input type="file" id="gallery-img" name="gallery[]" class="d-none" multiple
                                            accept="image/*" onchange="previewGallery(this)">
                                    </label>
                                    <div id="gallery-previews" class="d-flex flex-wrap mt-2"></div>
                                </div>

                                <div class="bg-light p-4 rounded-4 border">
                                    <h6 class="fw-bold mb-3">Attributes</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_featured" checked>
                                        <label class="form-check-label small fw-bold">Mark as Featured</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_trending">
                                        <label class="form-check-label small fw-bold">Trending Experience</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" checked>
                                        <label class="form-check-label small fw-bold">Live Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: JOURNEY MAP -->
                    <div class="form-step" id="step-2">
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <div>
                                <h4 class="fw-black text-dark mb-1">Itinerary Builder</h4>
                                <p class="text-muted small mb-0">Total Duration: <span id="dur-count"
                                        class="fw-bold text-primary">0 Days</span></p>
                            </div>
                            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="addDay()">
                                <i class="bi bi-plus-lg me-2"></i> Add New Day
                            </button>
                        </div>

                        <div id="itinerary-timeline">
                            <!-- Dynamic Day Cards -->
                        </div>
                    </div>

                    <!-- STEP 3: COMMERCIALS -->
                    <div class="form-step" id="step-3">
                        <div class="row g-5">
                            <div class="col-md-7">
                                <div class="bg-white border rounded-4 p-5 shadow-sm mb-4">
                                    <h5 class="fw-black mb-4">Net Cost Aggregate</h5>
                                    <div id="cost-breakdown" class="mb-4">
                                        <!-- Dynamic Breakdown -->
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                        <span class="fw-bold text-muted">Total Component Value</span>
                                        <span class="fs-2 fw-black text-dark" id="net-total">RM 0</span>
                                    </div>
                                </div>

                                {{-- Price Mode Toggle --}}
                                <div class="bg-light p-3 rounded-3 border mb-4 d-flex align-items-center gap-3">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="direct-price-toggle"
                                            onchange="togglePriceMode(this)">
                                        <label class="form-check-label fw-bold small" for="direct-price-toggle">Enter Direct
                                            Price</label>
                                    </div>
                                    <span class="text-muted small" id="price-mode-hint">Auto-calculating from services
                                        below</span>
                                </div>

                                <div id="auto-price-section">
                                    <div class="row g-4 mt-2">
                                        <div class="col-md-6">
                                            <label class="premium-label">Service Markup (%)</label>
                                            <input type="number" name="markup_percentage" id="markup-in"
                                                class="form-control premium-input" value="10" oninput="calculateRates()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Government GST (%)</label>
                                            <input type="number" name="gst_percentage" id="gst-in"
                                                class="form-control premium-input" value="5" oninput="calculateRates()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">TCS (If applicable %)</label>
                                            <input type="number" name="tcs_percentage" id="tcs-in"
                                                class="form-control premium-input" value="0" oninput="calculateRates()">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Final Selling Rate (Auto-Calculated)</label>
                                            <input type="number" name="price" id="selling-in"
                                                class="form-control premium-input form-control-lg fw-black text-primary"
                                                readonly>
                                            <small class="text-muted">Formula: (Cost + Markup) + Taxes</small>
                                        </div>
                                    </div>
                                </div>

                                <div id="direct-price-section" style="display:none;">
                                    <div class="row g-4 mt-2">
                                        <div class="col-md-6">
                                            <label class="premium-label">Direct Selling Price (RM)</label>
                                            <input type="number" name="price" id="direct-price-in"
                                                class="form-control premium-input form-control-lg fw-black text-primary"
                                                placeholder="Enter price directly" min="0" step="0.01"
                                                oninput="onDirectPriceChange()">
                                            <small class="text-muted">Enter the final price when you don't have itemised
                                                services.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Service Markup (%)</label>
                                            <input type="number" name="markup_percentage" class="form-control premium-input"
                                                placeholder="Optional" value="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">Government GST (%)</label>
                                            <input type="number" name="gst_percentage" class="form-control premium-input"
                                                placeholder="Optional" value="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label">TCS (If applicable %)</label>
                                            <input type="number" name="tcs_percentage" class="form-control premium-input"
                                                placeholder="Optional" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="card border-0 bg-primary text-white rounded-4 shadow-lg p-4">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-4">Global Matrix</h5>
                                        <div id="fx-matrix" class="space-y-3">
                                            <!-- FX Previews -->
                                        </div>
                                        <div class="mt-4 p-3 bg-white bg-opacity-10 rounded-3 text-center">
                                            <p class="small mb-0 opacity-75">Rates based on daily market average</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: COMPLIANCE -->
                    <div class="form-step" id="step-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="premium-label">Package Inclusions</label>
                                <div id="inc-editor-container"
                                    style="height: 200px; border-radius: 16px; background: #f8fafc;"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="premium-label">Package Exclusions</label>
                                <div id="exc-editor-container"
                                    style="height: 200px; border-radius: 16px; background: #f8fafc;"></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-5">
                            <div class="col-md-6">
                                <label class="premium-label">Cancellation Terms</label>
                                <textarea name="cancellation_policy" class="form-control premium-input" rows="5"
                                    placeholder="Define refund brackets..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="premium-label">Terms & Conditions</label>
                                <textarea name="terms" class="form-control premium-input" rows="5"
                                    placeholder="Legal fine print..."></textarea>
                            </div>
                        </div>

                        <div class="row g-4 mt-5">
                            <div class="col-12">
                                <div class="bg-light p-4 rounded-4 border">
                                    <h6 class="fw-bold mb-4 text-primary"><i class="bi bi-search me-2"></i> Search Engine
                                        Optimization (SEO)</h6>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="premium-label small">Meta Title</label>
                                            <input type="text" name="meta_title" class="form-control premium-input"
                                                placeholder="SEO optimized title">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="premium-label small">Meta Keywords</label>
                                            <input type="text" name="meta_keywords" class="form-control premium-input"
                                                placeholder="travel, maldives, luxury, tour">
                                        </div>
                                        <div class="col-12">
                                            <label class="premium-label small">Meta Description</label>
                                            <textarea name="meta_description" class="form-control premium-input" rows="3"
                                                placeholder="Brief summary for Google search results..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Controls -->
                <div class="wizard-footer">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" id="btn-prev"
                        style="display:none;" onclick="step(-1)">
                        <i class="bi bi-chevron-left me-2"></i> Previous
                    </button>
                    <div class="ms-auto d-flex align-items-center gap-4">
                        <div class="price-tag d-none d-lg-flex">
                            <span class="small fw-bold text-muted">LIVE QUOTE:</span>
                            <span class="fw-black text-primary fs-5" id="foot-price">RM 0</span>
                        </div>
                        <button type="button" class="btn btn-success rounded-pill px-5 py-3 fw-bold" id="btn-save-immediate"
                            onclick="submitPackage()">
                            Save Package <i class="bi bi-check-circle ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-dark rounded-pill px-5 py-3 fw-bold" id="btn-next"
                            onclick="step(1)">
                            Next Step <i class="bi bi-chevron-right ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-publish px-5 py-3" id="btn-publish" style="display:none;"
                            onclick="submitPackage()">
                            FINISH & PUBLISH <i class="bi bi-rocket-takeoff ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Template: Day Card -->
    <template id="day-tpl">
        <div class="itinerary-day-card" data-day="{N}">
            <div class="day-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="day-count">{N}</div>
                    <input type="text" class="form-control border-0 bg-transparent fw-black fs-5 day-title-in"
                        placeholder="Day Theme (e.g. Arrival & Sunset Cruise)" style="width: 400px; outline:none;">
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle"
                    onclick="deleteDay(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <label class="premium-label">Experience Narration</label>
                    <textarea class="form-control premium-input day-desc-in" rows="3"
                        placeholder="Describe the day's journey..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="premium-label d-block mb-2">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i> Tourist Spots / Sightseeing Places
                    </label>
                    <select class="form-select select2-spots-multi" multiple
                        data-placeholder="Select multiple tourist spots for Day {N}..." style="width: 100%;">
                        @foreach($touristSpots as $spot)
                            <option value="{{ $spot->id }}" data-dest-country="{{ $spot->destination->country ?? '' }}">
                                {{ $spot->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-light p-4 rounded-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="premium-label mb-0"><i class="bi bi-box-seam me-2 text-primary"></i> Inventory
                            Components</label>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                            onclick="addServiceRow({N})">
                            <i class="bi bi-plus-lg me-1"></i> Add Service
                        </button>
                    </div>
                    <div class="services-container" id="services-day-{N}">
                        <!-- Dynamic Service Rows -->
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <label class="premium-label d-block mb-3">Complimentary Meals</label>
                        <div class="d-flex gap-4">
                            <div class="form-check custom-check">
                                <input class="form-check-input meal-cb" type="checkbox" value="Breakfast">
                                <label class="form-check-label fw-bold small">Breakfast</label>
                            </div>
                            <div class="form-check custom-check">
                                <input class="form-check-input meal-cb" type="checkbox" value="Lunch">
                                <label class="form-check-label fw-bold small">Lunch</label>
                            </div>
                            <div class="form-check custom-check">
                                <input class="form-check-input meal-cb" type="checkbox" value="Dinner">
                                <label class="form-check-label fw-bold small">Dinner</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Template: Service Row -->
    <template id="service-row-tpl">
        <div class="row g-3 mb-3 service-row align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Type</label>
                <select class="form-select premium-input service-type" onchange="onServiceTypeChange(this)">
                    <option value="hotel">Hotel</option>
                    <option value="transport">Transport</option>
                    <option value="activity">Activity</option>
                    <option value="ticket">Entry Ticket</option>
                    <option value="meal">Meal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Vendor</label>
                <select class="form-select select2-vendor" onchange="onVendorChange(this)">
                    <option value="">Select Vendor</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="small fw-bold text-muted mb-1">Product / Service</label>
                <select class="form-select select2-service" onchange="calculateRates()">
                    <option value="">Choose Service</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger border-0 mb-1"
                    onclick="$(this).closest('.service-row').remove(); calculateRates();">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <!-- Quill Editor Assets (if not in layout) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let activeStep = 1;
        let quillEditors = {};

        // Inventory Data
        @php
            $hotelList = $hotels->map(function ($h) {
                return ['id' => $h->id, 'name' => $h->name, 'supplier_id' => $h->supplier_id, 'price' => $h->roomTypes->avg('base_price') ?? 0]; });
            $transportList = $transportRoutes->map(function ($t) {
                return ['id' => $t->id, 'name' => $t->name, 'supplier_id' => $t->supplier_id, 'price' => $t->base_price]; });
            $activityList = $activities->map(function ($a) {
                return ['id' => $a->id, 'name' => $a->name, 'supplier_id' => $a->supplier_id, 'price' => $a->base_price]; });
            $ticketList = $entryTickets->map(function ($et) {
                return ['id' => $et->id, 'name' => $et->attraction_name, 'supplier_id' => $et->supplier_id, 'price' => $et->adult_price]; });
            $mealList = $meals->map(function ($m) {
                return ['id' => $m->id, 'name' => $m->name, 'supplier_id' => $m->supplier_id, 'price' => $m->price]; });
            $touristSpotList = $touristSpots->map(function ($ts) {
                return ['id' => $ts->id, 'name' => $ts->name, 'supplier_id' => $ts->supplier_id, 'destination_country' => $ts->destination->country ?? '', 'price' => 0]; });
        @endphp

        const inventory = {
            hotel: @json($hotelList),
            transport: @json($transportList),
            activity: @json($activityList),
            ticket: @json($ticketList),
            meal: @json($mealList),
            spot: @json($touristSpotList)
        };

        $(document).ready(function () {
            $('.select2').select2({ theme: 'bootstrap-5' });

            // Country to Destination Cascading
            $('#country_id').on('change', function () {
                let selectedCountryName = $(this).find(':selected').data('country-name') || '';
                let destSelect = $('#destination_ids');

                // Clear current selection
                destSelect.val(null).trigger('change');

                // Filter destinations by country string match
                destSelect.find('option').each(function () {
                    let optCountry = $(this).data('country'); // country string e.g. "Thailand"
                    if (!selectedCountryName || !optCountry || optCountry === selectedCountryName || $(this).val() === "") {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                    }
                });

                // Re-init select2 to refresh disabled states
                destSelect.select2({ theme: 'bootstrap-5' });

                // Filter tourist spots in all day cards by country
                filterAllSpotsByCountry(selectedCountryName);
            });

            quillEditors['highlight'] = initQuillEditor('#highlight-editor-container', 200);
            quillEditors['inc'] = initQuillEditor('#inc-editor-container', 200);
            quillEditors['exc'] = initQuillEditor('#exc-editor-container', 200);
            addDay();
        });

        function step(dir) {
            if (dir > 0) {
                let errors = validateStep(activeStep);
                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please complete the required fields',
                        html: `<div class="text-start small">${errors.join('<br>')}</div>`,
                        confirmButtonColor: '#1e293b'
                    });
                    return;
                }
            }

            let target = activeStep + dir;
            if (target < 1 || target > 4) return;
            $(`#step-${activeStep}`).removeClass('active');
            $(`#step-${target}`).addClass('active');
            $('.step-item').removeClass('active completed');
            for (let i = 1; i < target; i++) $(`.step-item[data-step="${i}"]`).addClass('completed');
            $(`.step-item[data-step="${target}"]`).addClass('active');
            activeStep = target;
            $('#btn-prev').toggle(activeStep > 1);
            $('#btn-next').toggle(activeStep < 4);
            $('#btn-save-immediate').toggle(activeStep < 4);
            $('#btn-publish').toggle(activeStep === 4);
            if (activeStep === 3) calculateRates();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(stepId) {
            let stepEl = document.getElementById(`step-${stepId}`);
            if (!stepEl) return [];

            let inputs = stepEl.querySelectorAll('input, select, textarea');
            let errors = [];

            const fieldLabels = {
                'name': 'Package Title',
                'country_id': 'Country',
                'destination_ids[]': 'Primary Destinations',
                'price': 'Final Selling Rate'
            };

            inputs.forEach(input => {
                if (input.hasAttribute('required') || input.required) {
                    let isEmpty = !input.value || input.value.trim() === '';
                    if (input.tagName === 'SELECT' && input.multiple) {
                        isEmpty = $(input).val() === null || $(input).val().length === 0;
                    }
                    if (isEmpty) {
                        let fieldName = fieldLabels[input.name] || input.placeholder || input.name || 'Required Field';
                        errors.push(`<b>${fieldName}</b> is required.`);
                    }
                }
                if (input.value && !input.checkValidity()) {
                    let fieldName = fieldLabels[input.name] || input.placeholder || input.name || 'Field';
                    errors.push(`<b>${fieldName}</b>: ${input.validationMessage}`);
                }
            });

            return errors;
        }

        function filterAllSpotsByCountry(countryName) {
            $('.itinerary-day-card').each(function () {
                let spotsSelect = $(this).find('.select2-spots-multi');
                spotsSelect.find('option').each(function () {
                    let spotCountry = $(this).data('dest-country');
                    if (!countryName || !spotCountry || spotCountry === countryName) {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                    }
                });
                // Refresh select2
                let currentVal = spotsSelect.val();
                spotsSelect.select2({ theme: 'bootstrap-5', placeholder: spotsSelect.data('placeholder') });
                if (currentVal) {
                    let validVals = currentVal.filter(v => {
                        return !spotsSelect.find('option[value="' + v + '"]').prop('disabled');
                    });
                    spotsSelect.val(validVals).trigger('change');
                }
            });
        }

        function addDay() {
            let n = $('.itinerary-day-card').length + 1;
            let tpl = $('#day-tpl').html().replace(/{N}/g, n);
            let card = $(tpl).appendTo('#itinerary-timeline');
            card.find('.select2-spots-multi').select2({ theme: 'bootstrap-5' });
            // Apply current country filter to new day
            let selectedCountryName = $('#country_id').find(':selected').data('country-name') || '';
            if (selectedCountryName) {
                card.find('.select2-spots-multi option').each(function () {
                    let spotCountry = $(this).data('dest-country');
                    if (spotCountry && spotCountry !== selectedCountryName) {
                        $(this).prop('disabled', true);
                    }
                });
                card.find('.select2-spots-multi').select2({ theme: 'bootstrap-5' });
            }
            updateDuration();
        }

        function addServiceRow(dayN, data = null) {
            let tpl = $('#service-row-tpl').html();
            let container = $(`#services-day-${dayN}`);
            container.append(tpl);
            let row = container.find('.service-row').last();
            row.find('.select2-vendor, .select2-service').select2({ theme: 'bootstrap-5' });

            if (data) {
                row.find('.service-type').val(data.type).trigger('change');
                row.find('.select2-vendor').val(data.supplier_id).trigger('change');
                setTimeout(() => {
                    row.find('.select2-service').val(data.id).trigger('change');
                }, 100);
            } else {
                row.find('.service-type').trigger('change');
            }
        }

        // Global vendors data for reconstruction
        const allVendors = @json($suppliers->map(function ($s) {
        return ['id' => $s->id, 'name' => $s->name]; }));

        function onServiceTypeChange(el) {
            let row = $(el).closest('.service-row');
            let type = $(el).val();
            let vendorSelect = row.find('.select2-vendor');

            // Find vendors who have items of this type
            let validVendorIds = [...new Set(inventory[type].map(i => i.supplier_id))];

            vendorSelect.empty().append('<option value="">Choose Vendor</option>');

            if (type === 'spot') {
                vendorSelect.append('<option value="none">No Vendor / Public Spot</option>');
            }

            allVendors.forEach(v => {
                if (validVendorIds.includes(v.id)) {
                    vendorSelect.append(`<option value="${v.id}">${v.name}</option>`);
                }
            });

            vendorSelect.trigger('change');
            row.find('.select2-service').empty().append('<option value="">Choose Service</option>').trigger('change');
        }

        function onVendorChange(el) {
            let row = $(el).closest('.service-row');
            let type = row.find('.service-type').val();
            let vendorId = $(el).val();
            let serviceSelect = row.find('.select2-service');
            serviceSelect.empty().append('<option value="">Choose Service</option>');
            if (type) {
                let items = [];
                if (type === 'spot' && (!vendorId || vendorId === 'none')) {
                    // If tourist spot and no specific vendor, show all spots
                    items = inventory[type];
                } else if (vendorId) {
                    items = inventory[type].filter(i => i.supplier_id == vendorId);
                }
                items.forEach(i => { serviceSelect.append(`<option value="${i.id}" data-price="${i.price}">${i.name}</option>`); });
            }
            serviceSelect.trigger('change');
        }

        function deleteDay(btn) {
            if ($('.itinerary-day-card').length === 1) return;
            $(btn).closest('.itinerary-day-card').fadeOut(300, function () {
                $(this).remove();
                reorderDays();
            });
        }

        function reorderDays() {
            $('.itinerary-day-card').each(function (i) {
                let n = i + 1;
                $(this).attr('data-day', n).find('.day-count').text(n);
                $(this).find('.services-container').attr('id', `services-day-${n}`);
                $(this).find('button[onclick^="addServiceRow"]').attr('onclick', `addServiceRow(${n})`);
            });
            updateDuration();
            calculateRates();
        }

        function updateDuration() {
            let d = $('.itinerary-day-card').length;
            $('#dur-count').text(`${d} Days / ${d > 0 ? d - 1 : 0} Nights`);
        }

        function calculateRates() {
            let total = 0;
            let html = '';
            $('.itinerary-day-card').each(function () {
                let dNum = $(this).attr('data-day');
                $(this).find('.service-row').each(function () {
                    let type = $(this).find('.service-type').val();
                    let s = $(this).find('.select2-service option:selected');
                    if (s.val()) {
                        let p = parseFloat(s.data('price')) || 0;
                        if (type !== 'hotel' && type !== 'transport') {
                            total += p;
                        }
                        let typeLabel = type.charAt(0).toUpperCase() + type.slice(1);
                        if (type === 'ticket') typeLabel = 'Entry Ticket';
                        html += `<div class="d-flex justify-content-between mb-2"><span>Day ${dNum}: ${typeLabel} - ${s.text()}</span><span class="fw-bold text-dark">RM ${p.toLocaleString()}</span></div>`;
                    }
                });
            });
            $('#cost-breakdown').html(html || '<p class="text-muted italic">No components added yet</p>');
            $('#net-total').text(`RM ${total.toLocaleString()}`);

            let mPercent = parseFloat($('#markup-in').val()) || 0;
            let gstPercent = parseFloat($('#gst-in').val()) || 0;
            let tcsPercent = parseFloat($('#tcs-in').val()) || 0;

            let markupAmount = total * (mPercent / 100);
            let subtotalWithMarkup = total + markupAmount;

            let gstAmount = subtotalWithMarkup * (gstPercent / 100);
            let tcsAmount = subtotalWithMarkup * (tcsPercent / 100);
            let totalTax = gstAmount + tcsAmount;

            let final = subtotalWithMarkup + totalTax;

            $('#selling-in').val(Math.round(final));
            $('#foot-price').text(`RM ${Math.round(final).toLocaleString()}`);

            // Add hidden fields for submission if not already there or update them
            if ($('#net-price-hidden').length === 0) {
                $('#package-wizard-form').append(`<input type="hidden" name="net_price" id="net-price-hidden">`);
                $('#package-wizard-form').append(`<input type="hidden" name="markup_amount" id="markup-amount-hidden">`);
                $('#package-wizard-form').append(`<input type="hidden" name="tax_amount" id="tax-amount-hidden">`);
            }
            $('#net-price-hidden').val(total);
            $('#markup-amount-hidden').val(markupAmount);
            $('#tax-amount-hidden').val(totalTax);

            const rates = { USD: 83.5, MYR: 17.8, AED: 22.8, SGD: 62.0 };
            let matrix = '';
            Object.keys(rates).forEach(c => { matrix += `<div class="d-flex justify-content-between align-items-center mb-3"><span class="fw-bold opacity-75">${c} Preview</span><span class="fw-black fs-5">${(final / rates[c]).toFixed(2)}</span></div>`; });
            $('#fx-matrix').html(matrix);
        }

        function togglePriceMode(toggle) {
            if (toggle.checked) {
                $('#auto-price-section').hide();
                $('#direct-price-section').show();
                $('#price-mode-hint').text('Entering price directly — no auto calculation');
                // Remove required from auto-calc selling input
                $('#selling-in').removeAttr('required').val('');
            } else {
                $('#auto-price-section').show();
                $('#direct-price-section').hide();
                $('#price-mode-hint').text('Auto-calculating from services below');
                $('#direct-price-in').val('');
                calculateRates();
            }
        }

        function onDirectPriceChange() {
            let val = parseFloat($('#direct-price-in').val()) || 0;
            $('#foot-price').text('RM ' + Math.round(val).toLocaleString());
        }

        function previewHero(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = e => { $('#hero-preview img').attr('src', e.target.result); $('#hero-preview').removeClass('d-none'); };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewGallery(input) {
            $('#gallery-previews').empty();
            if (input.files) {
                Array.from(input.files).forEach(f => {
                    let reader = new FileReader();
                    reader.onload = e => { $('#gallery-previews').append(`<img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;margin:5px;border-radius:12px;border:2px solid white;shadow:0 4px 10px rgba(0,0,0,0.1)">`); };
                    reader.readAsDataURL(f);
                });
            }
        }

        function submitPackage() {
            // Handle direct price mode
            let isDirectMode = $('#direct-price-toggle').is(':checked');
            if (isDirectMode) {
                let directPrice = parseFloat($('#direct-price-in').val()) || 0;
                if (directPrice <= 0) {
                    Swal.fire({ icon: 'error', title: 'Price Required', text: 'Please enter a direct selling price.' });
                    return;
                }
                // Sync price to the selling-in hidden value so form captures it
                $('#selling-in').val(directPrice);
            } else {
                calculateRates(); // Ensure rates and hidden fields are populated
            }
            let allErrors = [];
            for (let i = 1; i <= 4; i++) {
                allErrors = allErrors.concat(validateStep(i));
            }
            if (allErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Please complete the required fields',
                    html: `<div class="text-start small">${allErrors.join('<br>')}</div>`,
                    confirmButtonColor: '#1e293b'
                });
                return;
            }

            const formData = new FormData(document.getElementById('package-wizard-form'));
            let itinerary = [];
            $('.itinerary-day-card').each(function () {
                let day = { day_number: $(this).attr('data-day'), title: $(this).find('.day-title-in').val(), description: $(this).find('.day-desc-in').val(), meals: [], hotels: [], transports: [], activities: [], tickets: [], meals_list: [], spots: [] };
                $(this).find('.meal-cb:checked').each(function () { day.meals.push($(this).val()); });

                // Collect multi-select tourist spots
                let spotsVal = $(this).find('.select2-spots-multi').val() || [];
                spotsVal.forEach(val => {
                    day.spots.push({ tourist_spot_id: val, hours: 1, price_per_hour: 0 });
                });

                $(this).find('.service-row').each(function () {
                    let type = $(this).find('.service-type').val();
                    let val = $(this).find('.select2-service').val();
                    if (val) {
                        if (type === 'hotel') day.hotels.push({ hotel_id: val });
                        else if (type === 'transport') day.transports.push({ transport_id: val });
                        else if (type === 'activity') day.activities.push({ activity_id: val });
                        else if (type === 'ticket') day.tickets.push({ ticket_id: val });
                        else if (type === 'meal') day.meals_list.push({ meal_id: val });
                    }
                });
                itinerary.push(day);
            });
            formData.append('itinerary_data', JSON.stringify(itinerary));
            formData.append('short_description', quillEditors['highlight'] ? quillEditors['highlight'].root.innerHTML : '');
            formData.append('inclusions', quillEditors['inc'] ? quillEditors['inc'].root.innerHTML : '');
            formData.append('exclusions', quillEditors['exc'] ? quillEditors['exc'].root.innerHTML : '');
            formData.append('duration_days', $('.itinerary-day-card').length);
            formData.append('duration_nights', Math.max(0, $('.itinerary-day-card').length - 1));
            Swal.fire({ title: 'Finalizing Experience...', text: 'Syncing your custom journey configuration', didOpen: () => { Swal.showLoading(); } });
            $.ajax({
                url: '{{ route("admin.packages.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (res) => { Swal.fire({ icon: 'success', title: 'Package Created!', text: 'Your multi-vendor experience is now live.' }).then(() => window.location.href = '{{ route("admin.packages.index") }}'); },
                error: (err) => {
                    let msg = 'Review your configuration and try again.';
                    if (err.status === 422 && err.responseJSON.errors) {
                        msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                    } else {
                        msg = err.responseJSON?.message || err.statusText || 'Server Error';
                    }
                    Swal.fire({ icon: 'error', title: 'Configuration Error', html: `<div class="text-start small">${msg}</div>` });
                }
            });
        }
    </script>
@endpush